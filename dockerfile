# Image PHP avec extensions nécessaires pour Laravel
FROM php:8.2-cli

# Définir le dossier de travail
WORKDIR /var/www/html

# Installer les dépendances système et PHP requises
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git curl \
    && docker-php-ext-install pdo_mysql zip

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 5Copier le code dans le container
COPY . .

#  Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Exposer le port pour php artisan serve
EXPOSE 8000

# Commande pour lancer l'API
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
