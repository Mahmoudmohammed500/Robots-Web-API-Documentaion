# Use official PHP Apache image
FROM php:8.1-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip libonig-dev libpng-dev libfreetype6-dev libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd mbstring opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable apache rewrite
RUN a2enmod rewrite

# Copy project into the container
COPY . /var/www/html/

# Ensure correct permissions for uploads (adjust as needed)
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Expose port 80
EXPOSE 80

# Render will use the default CMD from the php:apache image (apache2-foreground)
