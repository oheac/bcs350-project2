# Use official PHP image with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install required PHP extensions (Cached unless image changes)
RUN docker-php-ext-install pdo pdo_mysql

# Enable mod_rewrite for URL routing (Cached)
RUN a2enmod rewrite

# Configure Apache (Cached)
RUN echo '<Directory /var/www/html>' > /etc/apache2/conf-available/app.conf && \
    echo '  Options Indexes FollowSymLinks' >> /etc/apache2/conf-available/app.conf && \
    echo '  AllowOverride All' >> /etc/apache2/conf-available/app.conf && \
    echo '  Require all granted' >> /etc/apache2/conf-available/app.conf && \
    echo '</Directory>' >> /etc/apache2/conf-available/app.conf && \
    a2enconf app

# Copy application files (Only triggers rebuild if code changes)
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
