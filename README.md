# Ticket Pro

Application Laravel de billetterie evenementielle avec back-office admin/superadmin, vente manuelle, scan QR, paiement callback, envoi d'email (QR + PDF), et suivi des actions (vendeur/scanneur).

## 1) Stack

- Backend: Laravel 12, PHP 8.2+
- Frontend: Blade + Vite
- DB: MySQL
- Mail: SMTP
- QR: `endroid/qr-code` (PNG) + `simplesoftwareio/simple-qrcode` (PDF)
- PDF: `barryvdh/laravel-dompdf`

## 2) Fonctionnalites actuelles

### Public / Paiement

- `/` affiche la page d'accueil.
- Les routes de paiement existent toujours:
	- `POST /buy`
	- `GET /pay/{id}`
	- `POST /callback`
	- `GET /success/{id}`

### Admin

- Dashboard (tickets payes/utilises)
- Vente manuelle de ticket depuis `GET /admin/sell`
- Creation ticket `paid` + envoi email automatique (QR + PDF)
- Liste tickets avec recherche et filtre statut
- Detail ticket
- Scan QR pour valider l'entree (mode texte + image + camera live)

### Superadmin

- Tout ce qu'un admin peut faire
- Gestion utilisateurs complete (create/edit/delete)
- Gestion tickets avancee (create/edit)
- Suppression de ticket (superadmin uniquement)

## 3) Roles et permissions

Colonnes utilisateurs:

- `is_admin`
- `is_superadmin`

Regles:

- Un superadmin est aussi admin.
- Routes `admin` accessibles admin + superadmin.
- Routes `superadmin` accessibles superadmin uniquement.
- Suppression ticket reservee au superadmin (route + controle serveur).

## 4) Statuts ticket

- `pending`
- `paid`
- `used`

Le back-office travaille principalement avec `paid` et `used`.

## 5) Nouvelles donnees de tracking

La table `tickets` utilise aussi:

- `sold_by_user_id`: utilisateur ayant vendu le ticket
- `used_by_user_id`: utilisateur ayant scanne le ticket
- `email_sent_at`: date d'envoi du mail

Affichage:

- Colonne `Vendu par` dans la liste tickets
- Champ `Scanne par` visible dans le detail ticket quand le ticket est `used`

## 6) Validation email

Creation/vente de ticket:

- Regle: `email:rfc,dns`
- Objectif: verifier format email + domaine valide

## 7) Scan QR (admin)

Page `GET /admin/scan`:

- Saisie manuelle du code
- Scan depuis image
- Scan camera en direct (mobile/desktop compatible navigateur)

Comportement:

- Ticket inconnu: refuse
- Ticket deja utilise: refuse
- Ticket non paye: refuse
- Ticket `paid`: passe a `used`, renseigne `used_at` et `used_by_user_id`

## 8) Emails envoyes

Classe: `app/Mail/TicketPurchasedMail.php`

Contenu:

- Email HTML responsive
- Version texte plain
- Piece jointe PNG du QR (`qr-billet-{id}.png`)
- Piece jointe PDF (`billet-{id}.pdf`)

Points importants:

- Le QR est responsive et centre dans son cadre en HTML email
- La date affichee dans email/PDF est la date de l'event (jamais date du jour par defaut)

## 9) Routes principales

### Auth

- `GET /login`
- `POST /login`
- `POST /logout`
- `GET /dashboard` (redirige vers dashboard admin)

### Admin (`auth + admin`)

- `GET /admin/dashboard`
- `GET /admin/sell`
- `POST /admin/sell`
- `GET /admin/tickets`
- `GET /admin/tickets/{ticket}`
- `GET /admin/scan`
- `POST /admin/scan`

### Superadmin (`auth + superadmin`)

- `GET /admin/users`
- `GET /admin/users/create`
- `POST /admin/users`
- `GET /admin/users/{user}/edit`
- `PUT /admin/users/{user}`
- `DELETE /admin/users/{user}`
- `GET /admin/tickets/create`
- `POST /admin/tickets`
- `GET /admin/tickets/{ticket}/edit`
- `PUT /admin/tickets/{ticket}`
- `DELETE /admin/tickets/{ticket}`

## 10) Installation

```bash
git clone <URL_DU_REPO>
cd ticket-pro
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Lancer en dev:

```bash
composer run dev
```

Ou manuellement:

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

## 11) Variables .env essentielles

DB:

- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

Paiement:

- `FEDAPAY_PUBLIC_KEY`
- `FEDAPAY_ENVIRONMENT`

Email:

- `MAIL_MAILER`
- `MAIL_HOST`
- `MAIL_PORT`
- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS`
- `MAIL_FROM_NAME`

## 12) Comptes seed par defaut

Mot de passe commun:

- `password`

Comptes:

- `admin@example.com`
- `superadmin@example.com`

## 13) Commandes utiles

```bash
# tests
composer run test

# build front
npm run build

# reset db local
php artisan migrate:fresh --seed

# voir les routes
php artisan route:list
```

## 14) UI recente

- Boutons d'action icones dans les listes tickets/utilisateurs
- Codes couleur type Bootstrap:
	- detail: info
	- modifier: warning
	- supprimer: danger
- Bouton `Modifier` en warning aussi dans le detail ticket

## 15) Troubleshooting

### L'email ne part pas

- Verifier config SMTP dans `.env`
- Verifier logs `storage/logs`
- Verifier connectivite SMTP sortante

### Le scan camera ne demarre pas

- Utiliser HTTPS (ou localhost)
- Autoriser la permission camera navigateur
- Tester avec un QR net et bien cadre

### Les roles ne s'appliquent pas

- Verifier `is_admin` et `is_superadmin` en base
- Verifier middleware sur les routes

---

Mettre ce README a jour a chaque changement de routes, permissions, schema tickets, ou workflow email/scan.
