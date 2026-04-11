FROM dunglas/frankenphp:php8.2

WORKDIR /app

# Installer dépendances système + extensions PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Installer Composer 
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le projet
COPY . .

# Installer dépendances Laravel
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Permissions Laravel
RUN chmod -R 775 storage bootstrap/cache

# Exposer port (important pour Railway/Render)
EXPOSE 8000

# Lancer FrankenPHP (mieux que artisan serve)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]