#!/usr/bin/env sh
set -eu

cd backend

export APP_ENV="${APP_ENV:-production}"
export APP_DEBUG="${APP_DEBUG:-false}"
export LOG_CHANNEL="${LOG_CHANNEL:-stderr}"
export DB_CONNECTION="${DB_CONNECTION:-sqlite}"
export PERSISTENT_DATA_PATH="${PERSISTENT_DATA_PATH:-${RAILWAY_VOLUME_MOUNT_PATH:-/var/data}}"
export DB_DATABASE="${DB_DATABASE:-${PERSISTENT_DATA_PATH}/database.sqlite}"
export UPLOADS_PATH="${UPLOADS_PATH:-${PERSISTENT_DATA_PATH}/uploads}"
export API_BASE_URL="${API_BASE_URL:-/api/v1}"
export APP_KEY="${APP_KEY:-base64:$(php -r 'echo base64_encode(random_bytes(32));')}"
export REQUIRE_PERSISTENT_SQLITE="${REQUIRE_PERSISTENT_SQLITE:-true}"

if [ "${APP_ENV}" = "production" ] && [ "${DB_CONNECTION}" = "sqlite" ] && [ "${REQUIRE_PERSISTENT_SQLITE}" = "true" ]; then
  case "${DB_DATABASE}" in
    "${PERSISTENT_DATA_PATH}"/*) ;;
    *)
      echo "ERROR: DB_DATABASE must be inside the persistent Railway volume: ${PERSISTENT_DATA_PATH}" >&2
      echo "Current DB_DATABASE: ${DB_DATABASE}" >&2
      exit 1
      ;;
  esac

  if command -v mountpoint >/dev/null 2>&1 && ! mountpoint -q "${PERSISTENT_DATA_PATH}"; then
    echo "ERROR: ${PERSISTENT_DATA_PATH} is not mounted as a persistent Railway volume." >&2
    echo "Attach a Railway Volume to this service and set its mount path to ${PERSISTENT_DATA_PATH}." >&2
    echo "The app is stopping to avoid creating a fresh SQLite DB on ephemeral storage." >&2
    exit 1
  fi
fi

mkdir -p "$(dirname "${DB_DATABASE}")" \
  "${UPLOADS_PATH}" \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

if [ "${DB_CONNECTION}" = "sqlite" ] && [ ! -f "${DB_DATABASE}" ]; then
  touch "${DB_DATABASE}"
fi

rm -rf public/uploads
ln -s "${UPLOADS_PATH}" public/uploads

cat > public/config.js <<EOF
window.__VEXEL_CONFIG__ = {
  API_BASE_URL: '${API_BASE_URL}',
}
EOF

php artisan config:clear --no-interaction
php artisan route:clear --no-interaction
php artisan view:clear --no-interaction
php artisan migrate --force --no-interaction
php artisan config:cache --no-interaction
php artisan view:cache --no-interaction

php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
