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

rm -rf \
  backend/public/_redirects \
  backend/public/assets \
  backend/public/audio \
  backend/public/config.js \
  backend/public/favicon.svg \
  backend/public/icons.svg \
  backend/public/images \
  backend/public/index.html \
  backend/public/*.mp3
cp -R web/dist/. backend/public/
