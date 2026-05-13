#!/usr/bin/env sh
set -eu

cd backend

export APP_ENV="${APP_ENV:-production}"
export APP_DEBUG="${APP_DEBUG:-false}"
export LOG_CHANNEL="${LOG_CHANNEL:-stderr}"
export DB_CONNECTION="${DB_CONNECTION:-sqlite}"
export DB_DATABASE="${DB_DATABASE:-/var/data/database.sqlite}"
export UPLOADS_PATH="${UPLOADS_PATH:-/var/data/uploads}"
export API_BASE_URL="${API_BASE_URL:-/api/v1}"
export APP_KEY="${APP_KEY:-base64:$(php -r 'echo base64_encode(random_bytes(32));')}"

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
