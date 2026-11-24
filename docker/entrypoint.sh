#!/bin/sh
# Tự chuẩn bị môi trường lần đầu rồi chạy server
set -e
[ -f .env ] || cp .env.example .env
[ -d vendor ] || composer install --no-interaction
grep -q '^APP_KEY=.\+' .env || php artisan key:generate --force

# Không dùng `artisan serve`: nó xóa biến môi trường của container (DB_HOST=mariadb)
# nên app sẽ đọc DB_HOST=127.0.0.1 từ .env và bị "Connection refused".
cd public
export PHP_CLI_SERVER_WORKERS=4
exec php -S 0.0.0.0:8000 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
