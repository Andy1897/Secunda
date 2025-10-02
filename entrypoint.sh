#!/bin/bash

cd /var/www/html || exit 1

if [ ! -f "vendor/autoload.php" ]; then
    echo "Installing dependencies..."
    composer install --no-scripts --no-progress --working-dir=/var/www/html
else
    echo "Composer autoload exists. Skipping install."
fi

if [ -n "${WWWUSER}" ] && [ -n "${WWWGROUP}" ]; then
    chown -R ${WWWUSER}:${WWWGROUP} /var/www/html 2>/dev/null || true
fi

if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
    else
        cat > .env << 'EOF'
APP_NAME=SecundaAPI
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost
APP_API_KEY=dev-secret-key

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=secunda_api
DB_USERNAME=sail
DB_PASSWORD=password

WWWGROUP=1000
WWWUSER=1000
SAIL_VERSION=v1.27.0
EOF
    fi

    php /var/www/html/artisan key:generate --ansi
fi

# Ждём, пока MySQL станет доступен
echo "Waiting for MySQL to be ready..."
if command -v mysqladmin >/dev/null 2>&1; then
    until mysqladmin ping -h"mysql" -P3306 -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; do
        echo "MySQL is unavailable - sleeping"
        sleep 5
    done
    echo "MySQL is ready!"
else
    echo "mysqladmin not found, sleeping 15s as a fallback..."
    sleep 15
fi

echo "Starting Laravel server..."
exec php /var/www/html/artisan serve --host=0.0.0.0 --port=80
