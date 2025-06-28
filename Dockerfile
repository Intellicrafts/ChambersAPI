FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    build-essential \
    libpq-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies with increased memory limit
RUN php -d memory_limit=-1 /usr/bin/composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --no-autoloader

# Copy the rest of the application
COPY . .

# Generate optimized autoloader
RUN php -d memory_limit=-1 /usr/bin/composer dump-autoload --no-dev --optimize

# Create production .env file
RUN echo "APP_NAME=ChambersAPI" > .env \
    && echo "APP_ENV=production" >> .env \
    && echo "APP_DEBUG=true" >> .env \
    && echo "APP_URL=https://chambers-api-staging-27296519338.asia-south1.run.app" >> .env \
    && echo "LOG_CHANNEL=stderr" >> .env \
    && echo "LOG_LEVEL=debug" >> .env \
    && echo "DB_CONNECTION=pgsql" >> .env \
    && echo "DB_HOST=34.100.232.139" >> .env \
    && echo "DB_PORT=5432" >> .env \
    && echo "DB_DATABASE=chambers_api" >> .env \
    && echo "DB_USERNAME=postgres" >> .env \
    && echo "DB_PASSWORD=" >> .env

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/logs \
    && touch /var/www/html/storage/logs/laravel.log \
    && chmod 777 /var/www/html/storage/logs/laravel.log

# Configure Apache
RUN a2enmod rewrite
RUN sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/public/g' /etc/apache2/sites-available/000-default.conf

# Set environment variables
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV APACHE_LOG_DIR=/var/log/apache2

# Enable Apache error display
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN echo "ErrorLog /dev/stderr" >> /etc/apache2/apache2.conf
RUN echo "CustomLog /dev/stdout combined" >> /etc/apache2/apache2.conf
RUN echo "php_flag display_errors on" >> /etc/apache2/conf-available/php.conf
RUN echo "php_value error_reporting E_ALL" >> /etc/apache2/conf-available/php.conf

# Update Apache configuration
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Run Laravel setup commands
RUN php artisan key:generate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose port 8080 (Cloud Run expects this)
RUN sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf

EXPOSE 8080

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Start with our custom entrypoint
CMD ["docker-entrypoint.sh"]
