#!/usr/bin/env sh
set -eu

cat > /usr/share/nginx/html/config.js <<EOF
window.__VEXEL_CONFIG__ = {
  API_BASE_URL: '${API_BASE_URL:-http://127.0.0.1:8000/api/v1}',
}
EOF
