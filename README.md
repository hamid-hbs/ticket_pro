# Ticket Pro

Application web Laravel de gestion de billetterie evenementielle, avec :

- achat de billet par un visiteur,
- paiement (callback Kkiapay + mode sandbox),
- envoi email avec QR code + PDF,
- back-office admin pour controle des tickets,
- role superadmin avec gestion des utilisateurs et des tickets.

Ce document explique l'application de A a Z : architecture, installation, commandes, ordre d'execution, comptes, workflow complet, et procedures de maintenance.

## 1) Stack technique

- Backend : Laravel 12, PHP 8.2+
- Frontend : Blade + Vite + Tailwind v4
- Base de donnees : MySQL (configuration actuelle)
- Queue/Cache/Session : drivers database
- Mail : SMTP (exemple Gmail), avec piece jointe QR + PDF
- Generation QR : endroid/qr-code
- Generation PDF : barryvdh/laravel-dompdf

Dependances principales (composer.json) :

- laravel/framework
- endroid/qr-code
- simplesoftwareio/simple-qrcode
- barryvdh/laravel-dompdf

## 2) Fonctionnalites metier

### Cote client (public)

- Affiche l'evenement principal.
- Permet d'acheter un billet (nom, email).
- Redirige vers une page de paiement.
- Gere le callback de paiement.
- Affiche une page de succes.

### Cote admin

- Dashboard statistiques (tickets payes/utilises).
- Liste des tickets avec filtre (statut + recherche).
- Detail ticket.
- Suppression ticket.
- Scanner QR pour valider l'entree.

### Cote superadmin

- Tout ce qu'un admin peut faire.
- Gestion utilisateurs (ajouter, modifier, supprimer).
- Gestion tickets avancee (ajouter, modifier, supprimer).

## 3) Gestion des roles et droits

Deux colonnes de role en base :

- is_admin
- is_superadmin

Regles :

- Un superadmin est aussi considere admin.
- Routes admin : accessibles admin + superadmin.
- Routes superadmin : accessibles uniquement superadmin.

Middleware utilises :

- admin : verification admin/superadmin
- superadmin : verification superadmin strict

## 4) Etats du ticket

Enum de la table tickets :

- pending
- paid
- used

Important :

- Le statut pending existe toujours en base (ticket cree avant paiement).
- Le back-office affiche et filtre principalement paid/used.

## 5) Flux complet du billet

### Etape 1 : creation du ticket

- Route : POST /buy
- Un ticket est cree avec un qr_code unique.
- Le statut initial reste pending (par defaut DB).

### Etape 2 : paiement

- Route page paiement : GET /pay/{id}
- Callback paiement : POST /callback
- Si paiement SUCCESS, le statut passe a paid.

### Etape 3 : envoi email

Lors du passage a paid :

- envoi d'un email de confirmation,
- piece jointe QR PNG,
- piece jointe PDF du billet,
- email_sent_at rempli pour eviter un doublon.

### Etape 4 : controle d'entree

- Route scan : POST /admin/scan
- Si ticket paid : passe a used + used_at renseigne.
- Si ticket used : acces refuse (deja utilise).

## 6) Routes principales

### Public

- GET /
- POST /buy
- GET /pay/{id}
- POST /callback
- GET /success/{id}
- POST /sandbox/pay/{ticket}

### Auth

- GET /login
- POST /login
- POST /logout

### Admin

- GET /admin/dashboard
- GET /admin/tickets
- GET /admin/tickets/{ticket}
- DELETE /admin/tickets/{ticket}
- GET /admin/scan
- POST /admin/scan

### Superadmin

- GET /admin/users
- GET /admin/users/create
- POST /admin/users
- GET /admin/users/{user}/edit
- PUT /admin/users/{user}
- DELETE /admin/users/{user}
- GET /admin/tickets/create
- POST /admin/tickets
- GET /admin/tickets/{ticket}/edit
- PUT /admin/tickets/{ticket}

## 7) Prerequis

Avant de lancer le projet, verifier :

- PHP 8.2+
- Composer 2+
- Node.js 20+ et npm
- MySQL
- Extension GD active (generation QR)

## 8) Installation complete (ordre exact)

Executer ces commandes dans cet ordre :

### 8.1 Recuperer le projet

```bash
git clone <URL_DU_REPO>
cd ticket-pro
```

### 8.2 Installer les dependances PHP

```bash
composer install
```

### 8.3 Installer les dependances front

```bash
npm install
```

### 8.4 Configurer le fichier environnement

```bash
copy .env.example .env
```

Adapter au minimum :

- DB_CONNECTION
- DB_HOST
- DB_PORT
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD

Option paiement :

- KKIAPAY_PUBLIC_KEY
- KKIAPAY_SANDBOX=true en local

Option email SMTP :

- MAIL_MAILER
- MAIL_HOST
- MAIL_PORT
- MAIL_USERNAME
- MAIL_PASSWORD
- MAIL_FROM_ADDRESS
- MAIL_FROM_NAME

### 8.5 Generer la cle applicative

```bash
php artisan key:generate
```

### 8.6 Migrer + seeder (obligatoire)

```bash
php artisan migrate --seed
```

Cette etape :

- cree toutes les tables,
- ajoute les colonnes de roles,
- cree les comptes de base,
- cree l'evenement de demo.

### 8.7 Lancer en developpement

Option simple (tout en parallele via script composer) :

```bash
composer run dev
```

Option manuelle (plus de controle) :

Terminal 1

```bash
php artisan serve
```

Terminal 2

```bash
npm run dev
```

Terminal 3 (si jobs asynchrones utilises)

```bash
php artisan queue:listen --tries=1 --timeout=0
```

## 9) Comptes par defaut apres seed

Mot de passe commun :

- password

Comptes :

- admin@example.com (admin)
- superadmin@example.com (superadmin)

## 10) Commandes utiles au quotidien

### Developpement

```bash
composer run dev
```

### Build front production

```bash
npm run build
```

### Tests

```bash
composer run test
```

### Reinitialiser la base en dev

```bash
php artisan migrate:fresh --seed
```

### Voir les routes

```bash
php artisan route:list
```

## 11) Configuration paiements et email

### Paiement Kkiapay

Variables utilisees :

- KKIAPAY_PUBLIC_KEY
- KKIAPAY_SANDBOX

Le callback backend est :

- POST /callback

### Email

Le mail de confirmation est construit par TicketPurchasedMail.

Pieces jointes envoyees :

- qr-billet-{id}.png
- billet-{id}.pdf

Si l'email ne part pas :

- verifier les variables SMTP,
- verifier la connectivite sortante SMTP,
- verifier les logs Laravel dans storage/logs.

## 12) Structure du projet (vue rapide)

- app/Http/Controllers/TicketController.php : parcours achat/paiement
- app/Http/Controllers/AdminController.php : dashboard + tickets + scan
- app/Http/Controllers/SuperAdminController.php : gestion users/tickets avancee
- app/Http/Controllers/Auth/LoginController.php : auth admin/superadmin
- app/Http/Middleware/EnsureUserIsAdmin.php
- app/Http/Middleware/EnsureUserIsSuperAdmin.php
- app/Mail/TicketPurchasedMail.php
- app/Models/User.php
- app/Models/Ticket.php
- app/Models/Event.php
- routes/web.php
- database/migrations/*
- database/seeders/DatabaseSeeder.php
- resources/views/*

## 13) Bonnes pratiques d'exploitation

- Ne jamais committer le .env.
- Changer les comptes seedes en environnement reel.
- Mettre KKIAPAY_SANDBOX=false en production.
- Utiliser une vraie configuration SMTP de production.
- Faire des sauvegardes regulieres de la base.

## 14) Troubleshooting

### Erreur de connexion base

- verifier les variables DB_* dans .env,
- verifier que MySQL est demarre,
- verifier que la base existe.

### Erreur vue/Vite

- relancer npm install,
- relancer npm run dev,
- ou rebuild via npm run build.

### Role non pris en compte

- verifier en base les colonnes is_admin et is_superadmin,
- reexecuter php artisan migrate --seed si environnement local de test.

### Callback paiement non recu

- verifier URL du callback configuree chez le provider,
- verifier logs Laravel,
- tester en mode sandbox.

## 15) Ordre de demarrage recommande (resume ultra-court)

1. composer install
2. npm install
3. copy .env.example .env
4. configurer .env
5. php artisan key:generate
6. php artisan migrate --seed
7. composer run dev

---

Documentation maintenue pour l'application Ticket Pro. Mettre ce fichier a jour a chaque modification de routes, roles, flux de paiement ou schema de base.
