# Eventia

Application Laravel de gestion d’événements et d’inscriptions, réalisée dans le cadre du cours **Projet d’initiation aux frameworks** à l’IFRI-UAC (GL3, année académique 2026-2027).

- **Équipe :** 12
- **Enseignante :** GAHOU Maryse (`MaryseGAHOU`)
- **Sujet :** B — Gestion des événements et inscriptions
- **Membres :**
  - Afwane SOULEYMANE (`Ratel404`)
  - Primael KPATINDE (`Prime-Dior`)
- **Base de données :** MySQL 8 (SQLite n’est pas utilisé)

## Fonctionnalités

- Liste des événements, recherche par titre et filtres par catégorie ou date.
- Détails, capacité et places restantes.
- Création de compte, connexion et déconnexion.
- Inscriptions et annulations pour l’utilisateur connecté ; vérification des doublons, de la capacité et de la date de l’événement.
- Espace des inscriptions personnelles et administration des événements protégée.
- API REST versionnée `/api/v1` pour l’authentification, les événements et les inscriptions.
- Documentation interactive de l’API avec Swagger UI.

## Technologies et prérequis

Laravel 13, PHP 8.4.1 ou supérieur avec `pdo_mysql`, Composer, MySQL 8, Fortify, Sanctum, L5-Swagger et PHPUnit. Le frontend web utilise Blade et CSS dans le même projet Laravel. Le verrou Composer actuel contient des composants Symfony qui exigent PHP 8.4.1 minimum.

## Installation

Dans PowerShell, depuis le dossier du projet :

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Créez une base MySQL appelée `eventia`, puis configurez `.env` avec votre connexion locale :

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eventia
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

Ne partagez jamais le fichier `.env`. Exécutez les migrations et les seeders, puis démarrez l’application :

```powershell
php artisan migrate --seed
php artisan serve
```

Ouvrez l’adresse indiquée par Laravel, généralement `http://127.0.0.1:8000`.

## Utiliser la documentation Swagger

Après `composer install` et la configuration de l’application, les deux commandes sont :

```powershell
php artisan l5-swagger:generate
php artisan serve
```

La première régénère le document OpenAPI à partir des attributs présents dans `app/OpenApi/ApiSpec.php` et les contrôleurs `app/Http/Controllers/Api/V1/`. La seconde démarre Laravel. Ouvrez ensuite **http://127.0.0.1:8000/api/documentation** pour parcourir les endpoints et essayer les requêtes depuis Swagger UI.

La configuration L5-Swagger et sa vue sont déjà versionnées dans le projet ; il n’est pas nécessaire de relancer `vendor:publish`. Pour tester les routes protégées, utilisez d’abord l’endpoint de connexion afin d’obtenir le jeton Sanctum, puis autorisez ce jeton dans Swagger avec le bouton **Authorize**.

## API disponible

Les routes sont déclarées dans `routes/api.php` sous le préfixe `v1` :

- `POST /api/v1/auth/register`, `POST /api/v1/auth/login`, `GET /api/v1/auth/me`, `POST /api/v1/auth/logout`.
- `GET /api/v1/events`, `GET /api/v1/events/{event}` ; création, modification et suppression sont réservées à l’administration.
- `GET /api/v1/registrations`, `POST /api/v1/registrations`, `GET /api/v1/registrations/{registration}`, `DELETE /api/v1/registrations/{registration}` pour l’utilisateur authentifié.

Consultez Swagger UI pour les paramètres exacts, corps JSON, réponses et exigences d’authentification.

## Tests

```powershell
vendor/bin/phpunit
```

Configurez une base MySQL distincte `eventia_testing` pour les tests, afin de ne pas réinitialiser la base de démonstration.

## Organisation du projet

- `routes/web.php` et `resources/views/` : application web Laravel avec Blade.
- `routes/api.php` : endpoints versionnés.
- `app/Http/Controllers/` : traitements web et contrôleurs API.
- `app/Models/` et `database/migrations/` : modèles Eloquent et structure relationnelle.
- `app/OpenApi/ApiSpec.php` : métadonnées OpenAPI communes.
- `storage/api-docs/api-docs.json` : spécification générée par L5-Swagger.
- `tests/Feature/` : tests HTTP des fonctionnalités.
- `docs/EXPLICATION_MEMBRE_A.md` : guide pédagogique de soutenance.

## Limites connues

L’annulation d’une inscription supprime actuellement la ligne au lieu de conserver un historique. Aucun compte administrateur de démonstration avec mot de passe n’est publié ; préparez un compte local pour présenter l’administration. Les jetons d’accès et identifiants de base de données ne doivent pas être committés.

## Références

- [Documentation du paquet L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger/wiki/Installation-%26-Configuration)
- [Dépôt L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)
