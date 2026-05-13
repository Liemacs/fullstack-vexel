#!/usr/bin/env sh
set -eu

cd backend
composer install \
  --no-dev \
  --no-interaction \
  --no-progress \
  --prefer-dist \
  --optimize-autoloader
cd ..

cd web
npm install --include=optional --legacy-peer-deps
npm run build
cd ..

rm -rf backend/public/assets backend/public/index.html backend/public/config.js
cp -R web/dist/. backend/public/
