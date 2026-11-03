# Eventia

Application web Laravel de gestion d’événements et d’inscriptions pour le cours **Projet d’initiation aux frameworks**, IFRI-UAC, GL3, année 2026-2027.

- **Équipe :** 12
- **Enseignante :** GAHOU Maryse
- **Sujet :** B — Gestion des événements et inscriptions
- **Membres :** Afwane SOULEYMANE et Primael KPATINDE
- **Base de données :** MySQL 8 (SQLite n’est pas utilisé)

## Fonctionnalités

- Consultation, recherche par titre et filtres par catégorie et date.
- Détail d’un événement, disponibilité et places restantes.
- Création de compte, connexion et déconnexion.
- Inscription et annulation par l’utilisateur connecté, avec contrôle de capacité, doublon et événement passé.
- Liste personnelle des inscriptions.
- Administration protégée des événements par rôle.
- Validations Laravel, contraintes relationnelles en base et tests automatisés.

## Technologies

Laravel 13, PHP 8.4, Blade, HTML/CSS, MySQL 8, Composer, Fortify et PHPUnit. Le CSS public est servi directement depuis `public/css/eventia.css` ; Node/npm et Vite ne sont pas nécessaires au lancement des pages actuelles.

## Prérequis et installation

Installer PHP 8.4 avec l’extension `pdo_mysql`, Composer et MySQL 8. Dans PowerShell :

```powershell
cd C:\Users\Zbook\projets\eventia
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Créer une base MySQL nommée `eventia`, puis compléter `.env` avec le compte local :

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eventia
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

Ne partagez jamais `.env`. Créer également une base vide `eventia_testing` pour PHPUnit ; les paramètres de celle-ci sont dans `phpunit.xml`.

Appliquer les migrations, créer les données de démonstration et lancer Laravel :

```powershell
php artisan migrate --seed
php artisan serve
```

Ouvrir l’adresse indiquée par `artisan serve`, généralement `http://127.0.0.1:8000`.

## Tests

```powershell
vendor/bin/phpunit
```

La dernière exécution effectuée après le contrôle des inscriptions aux événements passés a réussi : **12 tests et 59 assertions**. La base `eventia_testing` doit être disponible. Les tests réinitialisent leur schéma ; n’utilisez pas la base contenant des données à conserver.

## Structure du code

- `routes/web.php` : routes publiques, authentification, inscriptions et administration.
- `app/Http/Controllers/` : traitements des événements, comptes et inscriptions.
- `app/Models/` : relations Eloquent entre utilisateurs, inscriptions, événements et catégories.
- `database/migrations/` : tables, clés étrangères, index et contraintes.
- `database/seeders/` : catégories et événements de démonstration.
- `resources/views/` : pages Blade partagées et formulaires.
- `public/css/eventia.css` : styles responsives.
- `tests/Feature/` : tests de parcours HTTP.
- `docs/EXPLICATION_MEMBRE_A.md` : guide détaillé de soutenance, code et contributions.

## Limites et démonstration

L’annulation supprime actuellement l’inscription au lieu d’en garder un historique. Le dépôt ne fournit pas de compte administrateur de démonstration : préparer un compte local avec rôle admin avant de présenter le CRUD. Le guide [DEMONSTRATION.md](DEMONSTRATION.md) propose un déroulé, et le guide détaillé est dans `docs/EXPLICATION_MEMBRE_A.md`.

La date limite indiquée par l’enseignante était le 2 novembre 2026 à 23 h 59, heure du Bénin. Consultez l’historique Git pour identifier les contributions réelles de chaque membre.
