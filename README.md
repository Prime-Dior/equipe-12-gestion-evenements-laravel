# Eventia

Application web Laravel de gestion d’événements et d’inscriptions 

- **Équipe :** 12
- **Enseignante :** GAHOU Maryse
- **Sujet :** B — Gestion des événements et inscriptions
- **Membres :** Afwane SOULEYMANE et Primael KPATINDE
- **Base de données :** MySQL 8

## Fonctionnalités

- Consultation, recherche par titre et filtres par catégorie et date.
- Détail d’un événement, disponibilité et places restantes.
- Création de compte, connexion et déconnexion.
- Inscription et annulation par l’utilisateur connecté.
- Liste personnelle des inscriptions.
- Dashboard administrateur dispo pour gerer les évenements.


## Technologies

Laravel 13, PHP 8.3 ou supérieur, Blade, HTML/CSS, MySQL 8, Composer, Fortify et PHPUnit. Les styles de l’application sont servis depuis `public/css/eventia.css`.

## Prérequis et installation

Installer PHP 8.3 ou supérieur avec l’extension `pdo_mysql`, Composer et MySQL 8. Démarrer MySQL, puis ouvrir PowerShell. Remplacer le chemin de l’exemple par celui du dossier du projet :

```powershell
cd C:\chemin\vers\equipe-12-gestion-evenements-laravel
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Créer une base MySQL nommée `eventia`, puis renseigner dans le fichier `.env` les identifiants de connexion à MySQL :

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eventia
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

Appliquer les migrations, créer les données de démonstration et lancer Laravel :

```powershell
php artisan migrate --seed
php artisan serve
```

Ouvrir l’adresse affichée par la commande `php artisan serve`

## Structure du code

- `routes/web.php` : routes des événements, de l’authentification, des inscriptions et de l’administration.
- `app/Http/Controllers/` : contrôleurs d’authentification, d’événements et d’inscriptions.
- `app/Models/` : modèles `User`, `Category`, `Event` et `Registration`, avec leurs relations Eloquent.
- `app/Providers/` : configuration des services et autorisations, dont l’accès à l’administration.
- `database/migrations/` : création et évolution des tables, clés étrangères, index et contraintes.
- `database/seeders/` : données de démonstration pour les catégories et événements, appelées par `DatabaseSeeder`.
- `database/factories/` : fabriques utilisées pour générer des données de test.
- `resources/views/` : vues Blade d’authentification, événements, inscriptions et administration, ainsi que layouts et composants.
- `resources/css/` et `resources/js/` : ressources front-end du projet.
- `public/css/eventia.css` : feuille de style utilisée par l’application.
- `tests/Feature/` et `tests/Unit/` : tests fonctionnels HTTP et tests unitaires.
- `docs/` : documentation détaillée du projet et guide de soutenance.
