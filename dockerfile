# Image PHP avec extensions nécessaires pour Laravel
FROM php:8.2-cli

# Définir le dossier de travail
WORKDIR /var/www/html

# Installer extensions PHP requises
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    git \
    unzip \
    && docker-php-ext-install intl pdo_mysql zip \
    && docker-php-ext-enable intl zip \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier le code dans le container
COPY . .

# Corriger le warning Git
RUN git config --global --add safe.directory /var/www/html

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Installer Node et npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Installer les dépendances JS
RUN npm install

# Compiler les assets
RUN npm run build

# Exposer le port pour php artisan serve
EXPOSE 8000

# Commande pour lancer l'API
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
