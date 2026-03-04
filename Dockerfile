FROM php:8.4-fpm

# Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    curl \
    npm \
    nodejs \
    && docker-php-ext-install pdo pdo_mysql zip

# Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Marcar como safe directory para Git (evita el error de "dubious ownership")
RUN git config --global --add safe.directory /var/www/html

WORKDIR /var/www/html
COPY . /var/www/html

# Instalar dependencias de PHP
RUN composer install

EXPOSE 9000
CMD ["php-fpm"]