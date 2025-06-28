#!/bin/bash

# This script prepares the Laravel application for deployment to Cloud Run

# Exit on error
set -e

# Variables
ENVIRONMENT=${1:-production}
BUILD_ID=${BUILD_ID:-$(date +%Y%m%d%H%M%S)}

echo "Preparing for deployment to $ENVIRONMENT environment (Build: $BUILD_ID)"

# Install dependencies
echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Generate key if not exists
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    APP_KEY=$(php artisan key:generate --show)
    echo "Generated key: $APP_KEY"
fi

# Create .env file from template
echo "Creating .env file for $ENVIRONMENT..."
cp .env.$ENVIRONMENT .env
sed -i "s/APP_KEY=/APP_KEY=$APP_KEY/" .env
sed -i "s/\${BUILD_ID}/$BUILD_ID/" .env

# Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize
echo "Optimizing application..."
php artisan optimize

# Run migrations if needed
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force
fi

echo "Deployment preparation complete!"