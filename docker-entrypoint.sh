#!/bin/bash
set -e
# Mettre à jour le port Apache dynamiquement (Railway assigne un port variable)
sed -i "s/\${PORT}/${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf
echo "Listen ${PORT:-80}" > /etc/apache2/ports.conf
# Générer la clé si absente
if [ -z "$APP_KEY" ]; then
php artisan key:generate --force
fi
# Optimiser Laravel pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
# Lancer les migrations automatiquement
php artisan migrate --force
# Démarrer Apache en avant-plan
exec apache2-foreground