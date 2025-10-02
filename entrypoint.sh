#!/bin/bash

if [ ! -d "vendor" ]; then
    echo "Installing dependencies..."
    composer install --no-scripts --no-progress
else
    echo "Vendor directory exists. Skipping composer install."
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

    # Генерируем ключ приложения
    php artisan key:generate --ansi
fi

# Ждём, пока MySQL станет доступен
echo "Waiting for MySQL to be ready..."
until mysqladmin ping -h"mysql" -P3306 -u"${DB_USERNAME:-sail}" -p"${DB_PASSWORD:-password}" --silent; do
    echo "MySQL is unavailable - sleeping"
    sleep 5
done
echo "MySQL is ready!"

# Выполняем миграции и сиды
echo "Running migrations..."
php artisan migrate --force

echo "Running seeders..."
php artisan db:seed --force

echo "Generating Swagger documentation..."
php artisan l5-swagger:generate

echo "Starting Laravel server..."
# Запускаем Laravel
exec php artisan serve --host=0.0.0.0 --port=80
