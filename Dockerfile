# Use official PHP image
FROM php:8.1-apache

# Copy all files to container's web root
COPY . /var/www/html/

# Enable mod_rewrite (optional)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Expose port
EXPOSE 80
