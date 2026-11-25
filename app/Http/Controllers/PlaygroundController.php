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
    /** Áp mode từ request (mode + modes[]) lên connection */
    private function applyMode(Request $r): void
    {
        DemoConnection::apply((string) $r->input('mode', 'strict'), (array) $r->input('modes', []));
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
        $this->applyMode($r);

        return response()->json(DemoConnection::effective());
    }

    public function products(): JsonResponse
    {
        return response()->json($this->tables());
    }

    public function reset(): JsonResponse
    {
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
            if (! in_array($scenario, ['group_by', 'myisam'], true)) {
                $id = (int) $db->getPdo()->lastInsertId();
                $row = $id ? DB::table('products')->where('id', $id)->first() : null;
                $stored = $row ? (array) $row : [];
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
