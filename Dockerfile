FROM php:8.1-apache

# Install PHP extensions commonly needed by PHP apps
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite

# Copy project files into the web root
COPY . /var/www/html/

WORKDIR /var/www/html/

# Ensure Apache can read files
RUN chown -R www-data:www-data /var/www/html     && chmod -R 755 /var/www/html

# Make sure index.php is used if present
RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf

# Apache config to allow PHP
RUN echo "<Directory /var/www/html/> \
    AllowOverride All \
    Require all granted \
</Directory>" > /etc/apache2/conf-available/roshni.conf && \
    a2enconf roshni && \
    echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf


EXPOSE 80
