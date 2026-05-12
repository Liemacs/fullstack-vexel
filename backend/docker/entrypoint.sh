#!/usr/bin/env sh
set -eu

mkdir -p /var/data storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
  touch "${DB_DATABASE:-/var/data/database.sqlite}"
fi

chown -R www-data:www-data /var/data storage bootstrap/cache

if [ -z "${APP_KEY:-}" ]; then
  php artisan key:generate --force --no-interaction
fi

php artisan config:clear --no-interaction
php artisan route:clear --no-interaction
php artisan view:clear --no-interaction
php artisan migrate --force --no-interaction
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

exec "$@"
