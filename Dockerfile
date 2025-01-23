# Use a PHP base image
FROM php:8.1-apache

# Install required extensions
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql

# Copy your project files to the server
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Set proper permissions (if needed)
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
