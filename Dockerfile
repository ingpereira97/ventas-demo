FROM php:8.2-cli

# Instalar dependencias del sistema + Node
RUN apt-get update && apt-get install -y \
    unzip git curl libsqlite3-dev nodejs npm \
    && docker-php-ext-install pdo pdo_sqlite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# 🔥 INSTALAR Y COMPILAR VITE
RUN npm install
RUN npm run build

# Crear sqlite
RUN mkdir -p database && touch database/database.sqlite

# Permisos
RUN chmod -R 777 storage bootstrap/cache database

EXPOSE 10000

CMD php artisan config:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000