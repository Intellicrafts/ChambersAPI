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

# Copy application files
COPY . /var/www/html

# Create .env file if it doesn't exist
RUN if [ ! -f .env ]; then cp .env.example .env || echo "APP_KEY=" > .env; fi

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Apache
RUN a2enmod rewrite
RUN sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/public/g' /etc/apache2/sites-available/000-default.conf

# Set environment variables
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV APACHE_LOG_DIR=/var/log/apache2

# Update Apache configuration
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install dependencies with increased memory limit
RUN php -d memory_limit=-1 /usr/bin/composer install --no-dev --optimize-autoloader --no-interaction

# Generate application key if not exists
RUN php artisan key:generate --force

# Expose port 8080 (Cloud Run expects this)
RUN sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf

EXPOSE 8080

# Start Apache
CMD ["apache2-foreground"]
