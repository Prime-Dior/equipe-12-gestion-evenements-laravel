# Eventia

Application Laravel de gestion d’événements et d’inscriptions, réalisée dans le cadre du cours **Projet d’initiation aux frameworks** à l’IFRI-UAC (GL3, année 2026-2027), équipe 12.

- **Enseignante :** GAHOU Maryse
- **Membres :** Afwane SOULEYMANE et Primael KPATINDE
- **Sujet B :** Gestion des événements et inscriptions
- **Échéance :** 2 novembre 2026 à 23 h 59, heure du Bénin

## État actuel

Le travail de cette branche (`feature/events-admin`) couvre la partie **catégories et événements** attribuée au membre A : modèles, migrations, recherche et filtres backend, pages publiques, CRUD des événements, autorisation par rôle, données de démonstration et tests.

La branche ne contient pas le module du membre B : connexion/inscription aux comptes, modèle et migration `Registration`, inscriptions des participants et annulations. Les pages de connexion et d’inscription restent à intégrer par le membre B. La page de détail indique clairement que le formulaire participant sera relié à ce module. Aucun paiement ni courriel réel n’est prévu.

## Fonctionnalités de cette branche

- Liste des événements à venir, avec recherche par titre et filtres catégorie/date côté serveur.
- Pagination qui conserve les filtres.
- Détail d’un événement et affichage des places restantes, calculées depuis les inscriptions si leur table existe.
- Création, modification et suppression protégées des événements.
- Protection de l’administration par authentification et autorisation `manage-events` (rôle `admin`).
- Validation des données et refus de diminuer la capacité sous le nombre d’inscriptions ou de supprimer un événement déjà inscrit.
- Catégories et événements de démonstration reproductibles.

## Technologies et prérequis

Laravel 13, PHP 8.4, Blade, MySQL 8, Composer et Git. L’extension PHP `pdo_mysql` est nécessaire. Node.js/npm ne sont pas nécessaires pour les pages actuelles : le layout sert directement `public/css/eventia.css`. **SQLite est interdit pour ce projet.**

## Installation locale

Dans PowerShell :

```powershell
cd C:\Users\Zbook\projets\eventia
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Créez une base MySQL locale, par exemple `eventia`, puis réglez les variables de connexion dans `.env` :

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eventia
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

Ne partagez et ne commitez jamais le fichier `.env`. Une fois le module de comptes du membre B intégré, lancez les migrations et les données d’exemple :

```powershell
php artisan migrate
php artisan db:seed --class=EventiaSeeder
```

Le seeder crée des catégories et événements de démonstration sans doublonner les événements lors de ses exécutions successives. Les migrations du module B devront également être présentes avant `migrate` si elles sont nécessaires au schéma commun.

Lancez le serveur Laravel :

```powershell
php artisan serve
```

Ouvrez l’adresse indiquée par `artisan serve` (habituellement `http://127.0.0.1:8000`). La feuille `resources/css/eventia.css` est conservée comme source miroir du fichier public ; les pages actuelles chargent la copie `public/css/eventia.css`, donc Vite n’est pas requis pour les visualiser.

## Tests

Les tests de cette branche utilisent une base **MySQL distincte** nommée `eventia_testing`, configurée dans `phpunit.xml`. Créez cette base vide avec les droits de l’utilisateur MySQL de test avant de lancer :

```powershell
php artisan test
```

Les tests exécutés sur cette branche couvrent la consultation, les filtres, le détail et les erreurs 404, la protection de l’administration, le CRUD et les validations. Ils ne couvrent pas les inscriptions participant : ce périmètre est confié au membre B.

## Organisation du code réalisé par le membre A

- `routes/web.php` : routes publiques et routes d’administration protégées.
- `app/Http/Controllers/EventController.php` : recherche, détail et opérations d’administration.
- `app/Models/Category.php`, `app/Models/Event.php` : attributs et relations Eloquent.
- `database/migrations/` : rôle utilisateur, catégories et événements.
- `database/factories/`, `database/seeders/` : données cohérentes pour les tests et démonstrations.
- `resources/views/events/`, `resources/views/admin/events/`, `resources/views/components/` : pages Blade publiques et d’administration.
- `tests/Feature/EventsManagementTest.php` : tests HTTP et règles vérifiées pour le module événements.

## Suite du développement par l’équipe

Le membre B peut intégrer Fortify ou le mécanisme d’authentification choisi, puis concevoir `Registration`, sa migration, ses relations et son contrôleur. Il faudra ensuite relier le formulaire d’inscription sur `events/show.blade.php` et les pages d’inscriptions aux nouvelles routes. Les règles de doublons, d’annulation et de capacité simultanée doivent être validées côté serveur et couvertes par des tests. Le contrôle de capacité dans cette branche est informatif ; l’inscription transactionnelle et son verrouillage restent à implémenter avec le module B.

La migration `add_role_to_users_table` est utilisée par l’autorisation admin de cette branche ; coordonnez son intégration avec le membre B pour ne pas créer une migration concurrente du même champ.

## Démonstration et documentation

Consultez [DEMONSTRATION.md](DEMONSTRATION.md) pour le parcours oral et les notions Laravel à expliquer. Cette documentation décrit les responsabilités prévues ; elle ne prétend pas que les éléments du membre B sont déjà réalisés.

## Configuration Git d’équipe

Le travail du membre A est isolé sur `feature/events-admin`, créé après synchronisation de `main`. La branche doit être publiée puis intégrée par Pull Request selon le guide de l’équipe. Ne poussez pas directement sur `main` et ne forcez pas de push.
