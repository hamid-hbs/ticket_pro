#!/bin/bash
set -e

echo ">>> PORT assigné par Railway : ${PORT}"

# Adapter le port Apache
echo "" > /etc/apache2/ports.conf
echo "Listen ${PORT:-80}" >> /etc/apache2/ports.conf
sed -i "s/*:80/*:${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf

echo ">>> Config Apache mise à jour"

# Fix : désactiver les MPM en conflit et garder uniquement prefork
a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

echo ">>> Démarrage Apache sur port ${PORT:-80}"
exec apache2-foreground