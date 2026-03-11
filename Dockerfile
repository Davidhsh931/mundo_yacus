# PHP 8.2 es más estable y consume menos CPU que 8.4 en Intel i5
FROM php:8.2-fpm-bookworm

# Evitamos que las instalaciones pidan interacción (ahorra tiempo y procesos)
ENV DEBIAN_FRONTEND=noninteractive

# Instalamos todo en un solo bloque para reducir el peso de la imagen
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    zip \
    curl \
    ca-certificates \
    gnupg \
    python3 \
    python3-pip \
    && docker-php-ext-install pdo pdo_mysql zip gd \
    # Limpieza inmediata de archivos temporales
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# IA: Instalación con flags para no guardar basura en el disco (no-cache-dir)
RUN pip3 install pandas numpy scikit-learn --break-system-packages --no-cache-dir

# Node.js 18 LTS: Mucho más suave para la RAM de tu Mac que la versión 20 o 23
RUN mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_18.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y nodejs

# Composer (Copiamos el binario directamente, muy eficiente)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Instalamos dependencias de JS de forma silenciosa
COPY package*.json ./
RUN npm install --quiet

EXPOSE 9000