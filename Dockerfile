FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# Set permissions for uploads and logs directories
RUN mkdir -p /var/www/html/uploads /var/www/html/logs \
    && chmod -R 755 /var/www/html/uploads \
    && chmod -R 755 /var/www/html/logs \
    && chown -R www-data:www-data /var/www/html

# Copy the startup script and make it executable
COPY coolify-init.sh /var/www/html/coolify-init.sh
RUN chmod +x /var/www/html/coolify-init.sh

# Expose port 80
EXPOSE 80

# Set working directory
WORKDIR /var/www/html

# Use the coolify-init.sh script as entrypoint
CMD ["/var/www/html/coolify-init.sh"]
