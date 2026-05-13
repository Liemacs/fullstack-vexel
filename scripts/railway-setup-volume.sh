#!/usr/bin/env sh
set -eu

SERVICE="${RAILWAY_SERVICE:-rp-vexel}"
MOUNT_PATH="${RAILWAY_VOLUME_MOUNT_PATH:-/var/data}"

if ! command -v railway >/dev/null 2>&1; then
  echo "ERROR: Railway CLI is not installed." >&2
  echo "Install it first: npm i -g @railway/cli" >&2
  exit 1
fi

echo "Using Railway service: ${SERVICE}"
echo "Creating/attaching persistent volume at: ${MOUNT_PATH}"

railway volume add \
  --service "${SERVICE}" \
  --mount-path "${MOUNT_PATH}"

echo "Setting SQLite variables for Railway..."

railway variables set \
  --service "${SERVICE}" \
  --skip-deploys \
  APP_ENV=production \
  APP_DEBUG=false \
  API_BASE_URL=/api/v1 \
  LOG_CHANNEL=stderr \
  DB_CONNECTION=sqlite \
  DB_DATABASE="${MOUNT_PATH}/database.sqlite" \
  PERSISTENT_DATA_PATH="${MOUNT_PATH}" \
  UPLOADS_PATH="${MOUNT_PATH}/uploads" \
  REQUIRE_PERSISTENT_SQLITE=true

echo "Done. Redeploy ${SERVICE} from Railway after applying these changes."
