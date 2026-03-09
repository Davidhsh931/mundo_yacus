FROM php:8.4-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Instalar Node.js y NPM (Versión estable para Vite)
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Instalamos dependencias de JS al construir la imagen
COPY package*.json ./
RUN npm install

EXPOSE 9000