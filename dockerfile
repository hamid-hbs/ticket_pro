# ─── Étape 1 : image de base PHP avec Apache ────────────────────────────────
FROM php:8.2-apache
# ─── Étape 2 : dépendances système ──────────────────────────────────────────
RUN apt-get update && apt-get install -y \
git \
curl \
libpng-dev \
libonig-dev \
libxml2-dev \
libzip-dev \
zip \
unzip \
&& docker-php-ext-install \
pdo_mysql \
mbstring \
exif \
pcntl \
bcmath \
gd \
zip \
sockets \
&& apt-get clean \
&& rm -rf /var/lib/apt/lists/*
# ─── Étape 3 : activer mod_rewrite pour Laravel ──────────────────────────────
RUN a2enmod rewrite
# ─── Étape 4 : installer Composer ────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# ─── Étape 5 : Configurer Apache pour Laravel ──────────────────────────────────────────
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf
# ─── Étape 6 : copier les fichiers du projet ─────────────────────────────────
WORKDIR /var/www/html
COPY . .
# ─── Étape 7 : installer les dépendances PHP ─────────────────────────────────
RUN composer install --no-dev --optimize-autoloader --no-interaction
# ─── Étape 8 : permissions des dossiers Laravel ──────────────────────────────
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache
# ─── Étape 9 : script de démarrage ───────────────────────────────────────────
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
EXPOSE ${PORT:-80}
ENTRYPOINT ["docker-entrypoint.sh"]