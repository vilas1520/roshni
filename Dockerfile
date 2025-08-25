FROM php:8.1-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

COPY . /var/www/html/
WORKDIR /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Proper multi-line Apache conf
RUN echo '<Directory /var/www/html/>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/roshni.conf \
    && a2enconf roshni \
    && echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf

EXPOSE 80
