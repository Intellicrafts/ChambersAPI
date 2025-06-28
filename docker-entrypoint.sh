#!/bin/bash
set -e

# Display PHP and Laravel versions
echo "PHP version: $(php -v | head -n 1)"
echo "Laravel version: $(php artisan --version)"

# Check if storage directories are writable
echo "Checking storage directory permissions..."
if [ -w "/var/www/html/storage" ]; then
    echo "Storage directory is writable"
else
    echo "Storage directory is not writable"
    exit 1
fi

# Clear caches in case of updates
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
echo "Starting Apache..."
exec apache2-foreground