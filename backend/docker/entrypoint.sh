#!/usr/bin/env sh
set -eu

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --force
fi

until php artisan db:show >/dev/null 2>&1; do
    echo "Waiting for PostgreSQL..."
    sleep 2
done

php artisan migrate --force

if [ "${SEED_DATABASE:-false}" = "true" ]; then
    php artisan db:seed --force
fi

exec "$@"
