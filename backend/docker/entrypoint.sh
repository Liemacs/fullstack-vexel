#!/usr/bin/env sh
set -eu

PERSISTENT_DATA_PATH="${PERSISTENT_DATA_PATH:-/var/data}"
SQLITE_BACKUP_KEEP="${SQLITE_BACKUP_KEEP:-10}"

mkdir -p "${PERSISTENT_DATA_PATH}/uploads" storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
rm -rf public/uploads
ln -s "${PERSISTENT_DATA_PATH}/uploads" public/uploads

APP_NAME_VALUE="${APP_NAME:-Vexel API}"
APP_ENV_VALUE="${APP_ENV:-production}"
APP_DEBUG_VALUE="${APP_DEBUG:-false}"
APP_URL_VALUE="${APP_URL:-http://localhost}"
LOG_CHANNEL_VALUE="${LOG_CHANNEL:-stderr}"
DB_CONNECTION_VALUE="${DB_CONNECTION:-sqlite}"
DB_DATABASE_VALUE="${DB_DATABASE:-${PERSISTENT_DATA_PATH}/database.sqlite}"
SESSION_DRIVER_VALUE="${SESSION_DRIVER:-database}"
CACHE_STORE_VALUE="${CACHE_STORE:-database}"
QUEUE_CONNECTION_VALUE="${QUEUE_CONNECTION:-database}"

if [ -z "${REQUIRE_PERSISTENT_SQLITE:-}" ]; then
  if [ "${APP_ENV_VALUE}" = "production" ]; then
    REQUIRE_PERSISTENT_SQLITE_VALUE="true"
  else
    REQUIRE_PERSISTENT_SQLITE_VALUE="false"
  fi
else
  REQUIRE_PERSISTENT_SQLITE_VALUE="${REQUIRE_PERSISTENT_SQLITE}"
fi

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
  case "${DB_DATABASE_VALUE}" in
    "${PERSISTENT_DATA_PATH}"/*) ;;
    *)
      if [ "${REQUIRE_PERSISTENT_SQLITE_VALUE}" = "true" ]; then
        echo "ERROR: DB_DATABASE must be inside ${PERSISTENT_DATA_PATH} for persistent SQLite storage." >&2
        exit 1
      fi
      ;;
  esac

  if [ "${REQUIRE_PERSISTENT_SQLITE_VALUE}" = "true" ] \
    && ! awk -v path="${PERSISTENT_DATA_PATH}" '$2 == path { found=1 } END { exit found ? 0 : 1 }' /proc/mounts; then
    echo "ERROR: ${PERSISTENT_DATA_PATH} is not mounted as a persistent disk/volume." >&2
    echo "On Render, add a persistent disk with mount path ${PERSISTENT_DATA_PATH} before deploying." >&2
    echo "The app is stopping to avoid creating a new empty SQLite database on ephemeral storage." >&2
    exit 1
  fi

  mkdir -p "$(dirname "${DB_DATABASE_VALUE}")"
  if [ -s "${DB_DATABASE_VALUE}" ]; then
    SQLITE_BACKUP_DIR="${SQLITE_BACKUP_DIR:-${PERSISTENT_DATA_PATH}/backups}"
    mkdir -p "${SQLITE_BACKUP_DIR}"
    cp "${DB_DATABASE_VALUE}" "${SQLITE_BACKUP_DIR}/database-$(date -u +%Y%m%d%H%M%S).sqlite"
    find "${SQLITE_BACKUP_DIR}" -name 'database-*.sqlite' -type f | sort -r | awk "NR>${SQLITE_BACKUP_KEEP}" | xargs -r rm -f
  elif [ ! -f "${DB_DATABASE_VALUE}" ]; then
    touch "${DB_DATABASE_VALUE}"
  fi
fi

chown -R www-data:www-data "${PERSISTENT_DATA_PATH}" storage bootstrap/cache public/uploads .env

php artisan config:clear --no-interaction
php artisan route:clear --no-interaction
php artisan view:clear --no-interaction
php artisan migrate --force --no-interaction
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

exec "$@"
