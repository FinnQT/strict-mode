<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\DemoConnection;
use Illuminate\Database\Connection;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class PlaygroundController extends Controller
{
    /** Áp connection + mode từ request (conn + mode + modes[]) lên connection */
    private function applyMode(Request $r): void
    {
        DemoConnection::apply(
            (string) $r->input('mode', 'strict'),
            (array) $r->input('modes', []),
            $r->input('conn') ? (string) $r->input('conn') : null
        );
    }

    private function tables(): array
    {
        return [
            'products' => array_map(fn ($o) => (array) $o, DB::select('SELECT * FROM products ORDER BY id')),
            'log_myisam' => array_map(fn ($o) => (array) $o, DB::select('SELECT * FROM log_myisam ORDER BY id')),
        ];
    }

    public function status(Request $r): JsonResponse
    {
        try {
            $this->applyMode($r);

            return response()->json(DemoConnection::effective());
        } catch (Throwable $e) {
            return response()->json([
                'connection' => $r->input('conn', config('database.default')),
                'message' => 'Không kết nối được DB (' . $r->input('conn', config('database.default')) . '): ' . $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Đặt GLOBAL sql_mode theo danh sách cờ tùy chọn hoặc preset
     */
    public function setGlobal(Request $r): JsonResponse
    {
        $this->applyMode($r);
        $connName = config('database.default');
        $preset = (string) $r->input('preset', 'custom');
        $pdo = DB::connection($connName)->getPdo();
        $isMaria = str_contains(strtolower((string) $pdo->query('SELECT @@version')->fetchColumn()), 'mariadb');

        if ($preset === 'loose') {
            $newGlobal = 'NO_ENGINE_SUBSTITUTION';
        } elseif ($preset === 'empty') {
            $newGlobal = '';
        } elseif ($preset === 'strict') {
            $newGlobal = $isMaria
                ? 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'
                : 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
        } else {
            $allowed = array_values(array_intersect(DemoConnection::ALLOWED_GLOBAL_MODES, (array) $r->input('global_modes', [])));
            if (! $isMaria) {
                $allowed = array_values(array_diff($allowed, ['NO_AUTO_CREATE_USER']));
            }
            $newGlobal = implode(',', $allowed);
        }

        $sqlSetGlobal = "SET GLOBAL sql_mode = '$newGlobal'";
        $pdo->exec($sqlSetGlobal);

        // Mở lại kết nối theo mode hiện tại để trả về effective mới nhất
        DB::purge($connName);
        $this->applyMode($r);

        return response()->json([
            'sql' => $sqlSetGlobal,
            'new_global' => $newGlobal,
            'effective' => DemoConnection::effective(),
        ]);
    }

    public function products(Request $r): JsonResponse
    {
        $this->applyMode($r);

        return response()->json($this->tables());
    }

    public function reset(Request $r): JsonResponse
    {
        $this->applyMode($r);
        DB::statement('TRUNCATE TABLE products');
        DB::statement('TRUNCATE TABLE log_myisam');
        $seed = fn (string $note, int $stock) => [
            'name' => 'Ban phim', 'price' => 100000, 'stock' => $stock,
            'sale_date' => '2026-01-15', 'note' => $note,
        ];
        Product::insert([$seed('OK', 99), $seed('lo 2', 3), $seed('lo 3', 7)]);

        return response()->json($this->tables());
    }

    public function run(Request $r): JsonResponse
    {
        $this->applyMode($r);
        $scenario = (string) $r->input('scenario');
        $payload = (array) $r->input('payload', []);

        // Bắt SQL thực thi (nội suy bindings để hiển thị)
        $sqls = [];
        $capture = false;
        DB::listen(function ($q) use (&$sqls, &$capture) {
            if ($capture) {
                $sqls[] = self::interpolate($q->sql, $q->bindings, $q->connection);
            }
        });

        $db = DB::connection();
        $sent = $payload;
        $affected = 0;
        $warnings = [];
        $error = null;
        $stored = [];
        $rows = null;
        $id = 0;
        $start = microtime(true);

        try {
            $capture = true;
            switch ($scenario) {
                case 'div_zero':
                    $d = (int) ($payload['divisor'] ?? 0);
                    $sent = ['name' => 'Chia 0', 'price' => 1, 'stock' => 1, 'note' => 'div', 'ratio' => "10/$d"];
                    $affected = DB::affectingStatement(
                        "INSERT INTO products (name, price, stock, note, ratio) VALUES (?, ?, ?, ?, 10/$d)",
                        ['Chia 0', 1, 1, 'div']
                    );
                    break;
                case 'group_by':
                    $sent = [];
                    $rows = array_map(fn ($o) => (array) $o,
                        DB::select('SELECT name, note, SUM(stock) AS stock FROM products GROUP BY name'));
                    break;
                case 'innodb_batch':
                    $sent = [];
                    DB::statement("DELETE FROM products WHERE note LIKE 'batch %'");
                    $affected = DB::affectingStatement(
                        "INSERT INTO products (name, price, stock, sale_date, note) VALUES
                        ('Chuot 1', 150000, 10, '2026-01-15', 'batch 1'),
                        ('Chuot 2', 150000, 999, '2026-01-15', 'batch 2'),
                        ('Chuot 3', 150000, 20, '2026-01-15', 'batch 3')"
                    );
                    break;
                case 'myisam':
                    $sent = [];
                    DB::statement('TRUNCATE TABLE log_myisam');
                    $affected = DB::affectingStatement('INSERT INTO log_myisam VALUES (1,1),(2,999),(3,3)');
                    break;
                case 'insert_ignore':
                    $affected = DB::table('products')->insertOrIgnore([$payload]);
                    break;
                default: // too_long, text_in_int, out_of_range, bad_date, missing_not_null, combo, zero_date
                    Product::create($payload);
                    $affected = 1;
            }
            $capture = false;

            // Laravel/PDO thật sự KHÔNG làm bước này; ở đây chỉ để phơi bày warnings cho khán giả.
            // Dùng query thường trên CÙNG connection, chạy ngay sau câu lệnh ghi.
            foreach ($db->getPdo()->query('SHOW WARNINGS')->fetchAll(PDO::FETCH_ASSOC) as $w) {
                $warnings[] = ['level' => $w['Level'], 'code' => (int) $w['Code'], 'message' => $w['Message']];
            }

            // Đọc lại dòng vừa ghi theo lastInsertId
            if (! in_array($scenario, ['group_by', 'myisam', 'innodb_batch'], true)) {
                $id = (int) $db->getPdo()->lastInsertId();
                $row = $id ? DB::table('products')->where('id', $id)->first() : null;
                $stored = $row ? (array) $row : [];
            }
            if ($scenario === 'innodb_batch') {
                $rows = array_map(fn ($o) => (array) $o, DB::select("SELECT id, name, price, stock, note FROM products WHERE note LIKE 'batch %' ORDER BY id"));
            }
            if ($scenario === 'myisam') {
                $rows = array_map(fn ($o) => (array) $o, DB::select('SELECT * FROM log_myisam ORDER BY id'));
            }
        } catch (QueryException $e) {
            $capture = false;
            $sqls[] = self::interpolate($e->getSql(), $e->getBindings(), $db);
            $pdo = $e->getPrevious();
            $error = [
                'http_status' => 500,
                'chain' => ['PDOException', QueryException::class],
                'sqlstate' => $e->errorInfo[0] ?? null,
                'code' => $e->errorInfo[1] ?? null,
                'message' => $e->errorInfo[2] ?? $e->getMessage(),
                'pdo_message' => $pdo ? $pdo->getMessage() : null,
                'laravel_message' => $e->getMessage(),
            ];
            if ($scenario === 'innodb_batch') { // InnoDB có transaction: đọc lại để chứng minh 0 dòng nào thuộc lô batch bị lọt vào
                $rows = array_map(fn ($o) => (array) $o, DB::select("SELECT id, name, price, stock, note FROM products WHERE note LIKE 'batch %' ORDER BY id"));
            }
            if ($scenario === 'myisam') { // MyISAM không rollback: đọc lại để thấy dòng đã lỡ ghi
                $rows = array_map(fn ($o) => (array) $o, DB::select('SELECT * FROM log_myisam ORDER BY id'));
            }
        } catch (Throwable $e) {
            $capture = false;
            $error = [
                'http_status' => 500, 'chain' => [get_class($e)],
                'sqlstate' => null, 'code' => $e->getCode(), 'message' => $e->getMessage(),
            ];
        }

        return response()->json([
            'ok' => $error === null,
            'sql' => implode(";\n", $sqls),
            'affected' => $affected,
            'warnings' => $warnings,
            'error' => $error,
            'sent' => (object) $sent,
            'stored' => (object) $stored,
            'rows' => $rows,
            'effective' => DemoConnection::effective(),
            'elapsed_ms' => round((microtime(true) - $start) * 1000, 1),
        ]);
    }

    /** Thay các dấu ? bằng giá trị đã escape để hiển thị */
    private static function interpolate(string $sql, array $bindings, Connection $conn): string
    {
        $i = 0;

        return preg_replace_callback('/\?/', fn () => (string) $conn->escape($bindings[$i++] ?? null), $sql);
    }
}
