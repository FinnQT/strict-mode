<?php

// Các config khác (app, session, cache...) lấy mặc định của framework + biến trong .env
$common = [
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3308'),
    'database' => env('DB_DATABASE', 'strict_demo'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', 'secret'),
    'unix_socket' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'engine' => null,
    'options' => [],
];

return [
    // Tên connection luôn đọc từ đây, không hardcode ở nơi khác
    'default' => env('DB_CONNECTION', 'mariadb'),

    'connections' => [
        'mariadb' => [
            'driver' => 'mariadb',
            'strict' => true,
            'host' => env('MARIADB_HOST', env('DB_HOST', '127.0.0.1')),
            'port' => env('MARIADB_PORT', env('DB_PORT', '3308')),
        ] + $common,
        // So sánh trực tiếp với MySQL 8.4 ngay trên giao diện
        'mysql' => [
            'driver' => 'mysql',
            'strict' => true,
            'host' => env('MYSQL_DB_HOST', '127.0.0.1'),
            'port' => env('MYSQL_DB_PORT', '3307'),
        ] + $common,
    ],

    'migrations' => ['table' => 'migrations'],
];
