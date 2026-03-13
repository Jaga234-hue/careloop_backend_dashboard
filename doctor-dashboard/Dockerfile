# Use the official PHP with Apache image
FROM php:8.2-apache

# Install mysqli and other needed extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite if needed (optional)
RUN a2enmod rewrite

# Set the working directory to Apache's default web root
WORKDIR /var/www/html

# Copy the source code into the container
COPY . .

# Adjust permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Environment variables can be passed at runtime
# API_URL will be used by config.php
ENV API_URL=http://localhost:8000
