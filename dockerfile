FROM php:8.2-cli

# Installer GD
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Installer dépendances Laravel
COPY . /app
WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php
RUN php composer.phar install --optimize-autoloader --no-interaction

CMD php artisan serve --host=0.0.0.0 --port=8000