FROM dunglas/frankenphp:php8.2

WORKDIR /app

COPY . .

# 🔥 IMPORTANT : ajouter gd ici
RUN install-php-extensions gd pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php

RUN composer install --optimize-autoloader --no-interaction

RUN chmod -R 775 storage bootstrap/cache

CMD php artisan serve --host=0.0.0.0 --port=8000