# Use an official PHP image with Apache
FROM php:8.1-apache

# Enable mod_rewrite and extensions
RUN a2enmod rewrite \
    && docker-php-ext-install mysqli

# Copy project files into the container
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Expose the default port
EXPOSE 80