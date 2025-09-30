#!/bin/bash

# Ждём, пока MySQL станет доступен
until mysqladmin ping -h"mysql" -P3306 -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; do
    echo "Waiting for MySQL..."
    sleep 5
done

# Устанавливаем зависимости, если vendor/ нет
if [ ! -d "vendor" ]; then
    echo "Installing dependencies..."
    composer install --no-scripts --no-progress
else
    echo "Vendor directory exists. Skipping composer install."
fi

# Генерируем ключ, если нет .env
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate --ansi
fi

# Выполняем миграции
php artisan migrate --force
php artisan db:seed --force
php artisan l5-swagger:generate

# Запускаем Laravel
exec php artisan serve --host=0.0.0.0 --port=80
