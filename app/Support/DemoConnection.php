<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class DemoConnection
{
    /** Các connection cho phép chọn từ UI */
    public const ALLOWED_CONNECTIONS = ['mariadb', 'mysql'];

    /** Các mode cho phép chọn ở chế độ custom */
    public const ALLOWED_MODES = [
        'STRICT_TRANS_TABLES', 'STRICT_ALL_TABLES', 'NO_ZERO_IN_DATE',
        'NO_ZERO_DATE', 'ERROR_FOR_DIVISION_BY_ZERO', 'ONLY_FULL_GROUP_BY',
    ];

    /** Các mode bổ sung thường có sẵn trong GLOBAL mặc định của MariaDB / MySQL */
    public const ALLOWED_GLOBAL_MODES = [
        'STRICT_TRANS_TABLES', 'STRICT_ALL_TABLES', 'NO_ZERO_IN_DATE',
        'NO_ZERO_DATE', 'ERROR_FOR_DIVISION_BY_ZERO', 'ONLY_FULL_GROUP_BY',
        'NO_AUTO_CREATE_USER', 'NO_ENGINE_SUBSTITUTION',
    ];

    /** Đổi connection + config mode rồi mở lại kết nối (chỉ ảnh hưởng request hiện tại) */
    public static function apply(string $mode, array $modes = [], ?string $conn = null): void
    {
        if ($conn !== null && in_array($conn, self::ALLOWED_CONNECTIONS, true)) {
            config(['database.default' => $conn]);
        }

        $name = config('database.default');
        $c = config("database.connections.$name");
        unset($c['modes']);

        switch ($mode) {
            case 'loose':  // Laravel đặt sql_mode chỉ còn NO_ENGINE_SUBSTITUTION
                $c['strict'] = false;
                break;
            case 'server': // null => Laravel không chạy SET SESSION sql_mode, kế thừa GLOBAL
                $c['strict'] = null;
                break;
            case 'custom': // 'modes' được ưu tiên hơn 'strict'
                $c['modes'] = array_values(array_intersect(self::ALLOWED_MODES, $modes));
                break;
            default:       // strict
                $c['strict'] = true;
        }

        config(["database.connections.$name" => $c]);
        DB::purge($name); // buộc mở kết nối mới với sql_mode mới
    }

    /** Thông tin "sự thật từ database" cho UI */
    public static function effective(): array
    {
        $r = DB::selectOne('SELECT @@version AS version, @@GLOBAL.sql_mode AS global_mode, @@SESSION.sql_mode AS session_mode');
        $c = config('database.connections.'.config('database.default'));

        if (isset($c['modes'])) {
            $snippet = "'modes' => [".implode(', ', array_map(fn ($m) => "'$m'", $c['modes'])).'],';
        } elseif (isset($c['strict'])) {
            $snippet = "'strict' => ".($c['strict'] ? 'true' : 'false').',';
        } else {
            $snippet = "// không khai báo 'strict'";
        }

        return [
            'connection' => config('database.default'),
            'version' => $r->version,
            'global_mode' => $r->global_mode,
            'session_mode' => $r->session_mode,
            'is_strict' => str_contains($r->session_mode, 'STRICT_TRANS_TABLES')
                || str_contains($r->session_mode, 'STRICT_ALL_TABLES'),
            'config_snippet' => $snippet,
        ];
    }
}
