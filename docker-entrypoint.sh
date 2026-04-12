#!/bin/bash
set -e

echo ">>> PORT assigné par Railway : ${PORT}"

# Réécrire complètement la config Apache avec le bon port
cat > /etc/apache2/sites-available/000-default.conf << EOF
<VirtualHost *:${PORT:-80}>
    ServerName localhost
    DocumentRoot /var/www/html/public
    <Directory /var/www/html/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF

# Réécrire ports.conf
echo "Listen ${PORT:-80}" > /etc/apache2/ports.conf

echo ">>> Config Apache mise à jour"

a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan db:seed --force

echo ">>> Démarrage Apache sur port ${PORT:-80}"
exec apache2-foreground