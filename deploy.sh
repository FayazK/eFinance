#!/bin/bash
set -e  # Exit on any error

echo "🚀 Starting deployment..."

# Navigate to project directory
cd /home/fayazk/finance.empowerbits.com

# Pull latest code
echo "📥 Pulling latest code..."
git pull origin main

# Install PHP dependencies (production mode)
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Clear all caches BEFORE build
echo "🧹 Clearing caches..."
php artisan optimize:clear

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Install Node dependencies and build frontend
# This will trigger Wayfinder generation during build
echo "🔨 Building frontend assets..."
npm ci
npm run build

# Cache routes and config AFTER build
echo "⚡ Optimizing application..."
php artisan route:cache
php artisan config:cache
php artisan view:cache

# Reload PHP-FPM
echo "🔄 Reloading PHP-FPM..."
echo "" | sudo -S service php8.4-fpm reload

echo "✅ Application deployed successfully!"
