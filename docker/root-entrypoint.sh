#!/usr/bin/env sh
set -eu

mkdir -p /var/data storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

APP_NAME_VALUE="${APP_NAME:-Vexel API}"
APP_ENV_VALUE="${APP_ENV:-production}"
APP_DEBUG_VALUE="${APP_DEBUG:-false}"
APP_URL_VALUE="${APP_URL:-http://localhost}"
LOG_CHANNEL_VALUE="${LOG_CHANNEL:-stderr}"
DB_CONNECTION_VALUE="${DB_CONNECTION:-sqlite}"
DB_DATABASE_VALUE="${DB_DATABASE:-/var/data/database.sqlite}"
SESSION_DRIVER_VALUE="${SESSION_DRIVER:-database}"
CACHE_STORE_VALUE="${CACHE_STORE:-database}"
QUEUE_CONNECTION_VALUE="${QUEUE_CONNECTION:-database}"

if [ -z "${APP_KEY:-}" ]; then
  APP_KEY_VALUE="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
else
  APP_KEY_VALUE="${APP_KEY}"
fi

cat > .env <<EOF
APP_NAME="${APP_NAME_VALUE}"
APP_ENV=${APP_ENV_VALUE}
APP_KEY=${APP_KEY_VALUE}
APP_DEBUG=${APP_DEBUG_VALUE}
APP_URL=${APP_URL_VALUE}

APP_LOCALE=ru
APP_FALLBACK_LOCALE=ru
APP_FAKER_LOCALE=ru_RU

LOG_CHANNEL=${LOG_CHANNEL_VALUE}
LOG_LEVEL=${LOG_LEVEL:-debug}

DB_CONNECTION=${DB_CONNECTION_VALUE}
DB_DATABASE=${DB_DATABASE_VALUE}
DB_FOREIGN_KEYS=true

SESSION_DRIVER=${SESSION_DRIVER_VALUE}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=${SESSION_DOMAIN:-null}

CACHE_STORE=${CACHE_STORE_VALUE}
QUEUE_CONNECTION=${QUEUE_CONNECTION_VALUE}

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
MAIL_MAILER=log
EOF

if [ "${DB_CONNECTION_VALUE}" = "sqlite" ]; then
  touch "${DB_DATABASE_VALUE}"
fi

cat > public/config.js <<EOF
window.__VEXEL_CONFIG__ = {
  API_BASE_URL: '${API_BASE_URL:-/api/v1}',
}
EOF

chown -R www-data:www-data /var/data storage bootstrap/cache public/config.js .env

php artisan config:clear --no-interaction
php artisan route:clear --no-interaction
php artisan view:clear --no-interaction
php artisan migrate --force --no-interaction
php artisan config:cache --no-interaction
php artisan view:cache --no-interaction

exec "$@"
