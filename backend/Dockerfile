FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --no-scripts \
    --optimize-autoloader

COPY . .
RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi

FROM php:8.2-apache

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpq-dev \
        libsqlite3-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite zip \
    && a2enmod rewrite headers \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf \
    && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /app /var/www/html
COPY docker/entrypoint.sh /usr/local/bin/vexel-entrypoint

RUN chmod +x /usr/local/bin/vexel-entrypoint \
    && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/data/database.sqlite \
    PERSISTENT_DATA_PATH=/var/data \
    REQUIRE_PERSISTENT_SQLITE=false \
    SQLITE_BACKUP_KEEP=10

EXPOSE 80

ENTRYPOINT ["vexel-entrypoint"]
CMD ["apache2-foreground"]
