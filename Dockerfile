FROM php:8.3-cli

# PHP extension cho MariaDB/MySQL + công cụ cần cho composer
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html
EXPOSE 8000
ENTRYPOINT ["sh", "docker/entrypoint.sh"]
