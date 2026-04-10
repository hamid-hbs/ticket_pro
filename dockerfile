FROM dunglas/frankenphp:php8.2

WORKDIR /app

# =========================
# System dependencies
# =========================
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# =========================
# Copier projet
# =========================
COPY . .

# =========================
# Composer
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# =========================
# NPM (IMPORTANT ORDER)
# =========================
RUN npm install
RUN npm run build

# =========================
# Permissions Laravel
# =========================
RUN chmod -R 775 storage bootstrap/cache

# =========================
# Run server (IMPORTANT)
# =========================
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]