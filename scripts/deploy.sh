#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/var/www/fitmanager"

if [ ! -d "$APP_DIR" ]; then
  echo "Diretório da aplicação não encontrado: $APP_DIR"
  exit 1
fi

cd "$APP_DIR"

sudo chown -R ubuntu:www-data "$APP_DIR"
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;

git fetch origin main
git reset --hard origin/main

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

if command -v npm >/dev/null 2>&1; then
  npm ci
  npm run build
fi

sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan optimize

sudo chown -R ubuntu:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;

sudo systemctl reload nginx

echo "Deploy concluído com sucesso."