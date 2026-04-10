FROM dunglas/frankenphp:php8.2

WORKDIR /app

COPY . .

# 🔥 Installer dépendances système + Composer
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# 🔥 Installer Composer manuellement
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 🔥 Installer dépendances Laravel
RUN composer install --optimize-autoloader --no-interaction

RUN chmod -R 775 storage bootstrap/cache

CMD php artisan serve --host=0.0.0.0 --port=8000