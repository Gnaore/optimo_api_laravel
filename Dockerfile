# Utilisez l'image PHP 8.1.13 avec Apache
FROM php:8.1.13-apache

# Set working directory
WORKDIR /var/www/html

# Wait for MySQL to be ready
RUN apt-get update && apt-get install -y wait-for-it \
    && chmod +x /usr/bin/wait-for-it

# Install dépendances nécessaires
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    nano \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libpng-dev \
    zip \
    vim \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-webp=/usr/include/  --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd pdo_mysql zip

# Activer les modules Apache nécessaires pour Laravel
RUN a2enmod rewrite

# Activer le support d'URL rewriting pour Apache
RUN sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www/html

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www/html

RUN chown -R www-data:www-data /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage

RUN chmod -R 775 /var/www/html/storage

# Set a default umask for the container
RUN umask 0002

# Installer les dépendances de l'application
RUN composer install --optimize-autoloader --no-dev


# Exposer le port 80
EXPOSE 80

# Commande pour démarrer Apache
CMD ["wait-for-it", "mysql:3306", "--", "apache2-foreground"]