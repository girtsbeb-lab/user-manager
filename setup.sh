#!/bin/bash

set -e

echo "🚀 User Manager — Setup"
echo "========================"

echo ""
echo "📋 1. Kopējam .env failu..."
cp .env.example .env

echo ""
echo "🐳 2. Palaižam Docker konteinerus..."
docker compose up -d --build

echo ""
echo "📦 3. Instalējam Redis PHP extension..."
docker compose exec app bash -c "echo 'no' | pecl install redis && docker-php-ext-enable redis"
docker compose restart app

echo ""
echo "⏳ Gaidām kamēr app konteiners ir gatavs..."
sleep 5

echo ""
echo "🎼 4. Instalējam Composer dependencies..."
docker compose exec app composer install

echo ""
echo "🔧 5. Kopējam Laravel 10 middleware..."
docker compose exec app composer create-project laravel/laravel:^10.0 /tmp/laravel10 --prefer-dist
docker compose exec app cp /tmp/laravel10/app/Http/Middleware/*.php /var/www/app/Http/Middleware/
docker compose exec app rm -rf /var/www/app/Http/Middleware/Middleware
docker compose exec app composer dump-autoload

echo ""
echo "🔑 6. Ģenerējam app atslēgu..."
docker compose exec app php artisan key:generate

echo ""
echo "🗄️  7. Palaižam migrācijas..."
docker compose exec app php artisan migrate

echo ""
echo "🎨 8. Pārbūvējam frontend..."
docker compose run --rm node

echo ""
echo "👥 9. Importējam lietotājus no API..."
docker compose exec app php artisan users:import

echo ""
echo "✅ Viss gatavs!"
echo "🌐 Atver pārlūkā: http://localhost:8080"
