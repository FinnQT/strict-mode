# 🍓 Strict Mode Playground

Demo seminar "Strict mode trong MySQL/MariaDB" (Laravel + MariaDB 11, không cần npm).

**Chỉ cần Docker** (PHP + Composer nằm trong container):

```bash
docker compose up -d --build   # app: http://localhost:8000, MariaDB 11: 127.0.0.1:3306 (root / secret / strict_demo)
docker compose logs -f app     # lần đầu chờ composer install xong
```

Chạy không dùng Docker cho app (cần PHP 8.3 + Composer; DB vẫn bằng Docker):
`docker compose up -d mariadb && composer install && cp .env.example .env && php artisan key:generate && php artisan serve`

- HeidiSQL: host `127.0.0.1`, port `3306`, user `root`, password `secret`. Đổi GLOBAL bằng
  `SET GLOBAL sql_mode='...';` rồi bấm ↻ trên trang (chọn thẻ "🔗 Theo server").
- So sánh MySQL 8.4: `docker compose --profile mysql up -d`, đặt `DB_CONNECTION=mysql`, `DB_PORT=3307` trong `.env`.
- Phím tắt: `1`–`9` chọn kịch bản, `Enter` chạy, `C` so sánh, `R` reset.
- Nếu `docker compose` đã chạy từ trước, schema chỉ nạp lần đầu: `docker compose down -v` để khởi tạo lại.
