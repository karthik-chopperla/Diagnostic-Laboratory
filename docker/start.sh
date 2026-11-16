#!/bin/sh
set -eu

port="${PORT:-10000}"
sed -i "s/Listen 80/Listen ${port}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${port}>/" /etc/apache2/sites-available/000-default.conf

php artisan migrate --force

if [ "${DEMO_SEED_ON_START:-false}" = "true" ]; then
    php artisan demo:seed-once
fi

exec apache2-foreground
