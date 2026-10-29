# Eventia — événements et inscriptions

Projet universitaire Laravel du cours **Projet d’initiation aux frameworks**, IFRI-UAC, GL3, année académique 2026-2027. Équipe 12, sujet B : **gestion des événements et des inscriptions**.

- **Enseignante :** GAHOU Maryse
- **Membres :** Afwane SOULEYMANE et Primael KPATINDE
- **Date limite annoncée :** lundi 2 novembre 2026 à 23 h 59 (heure du Bénin)

## Présentation et état du projet

Eventia est une maquette d’interface pour découvrir des événements au Bénin et préparer leur gestion. Cette étape réalise le frontend Blade et ses styles. Les contenus visibles sont des données d’exemple en mémoire ; ils ne viennent pas d’une base de données.

**Déjà réalisé :** squelette Laravel indépendant, mise en page Blade partagée, accueil avec filtres de prévisualisation, détail, gestion, formulaires d’administration, page des inscriptions, feuille CSS responsive, routes temporaires de lecture pour prévisualiser les vues et exemples isolés dans `app/Support/EventiaPreview.php`.

**À réaliser par l’équipe :** backend métier complet. Il n’y a pas encore de modèles Event/Registration, de migrations métier, de persistance des inscriptions, de règles de disponibilité ou d’annulation, d’autorisations ni de tests métier. Les formulaires sont des maquettes et leurs boutons indiquent qu’ils n’enregistrent aucune action.

## Technologies et prérequis

PHP 8.4, Laravel 13, Composer, Blade, HTML, CSS, MySQL 8, Git, Node.js et npm. PHP doit inclure `pdo_mysql`, `mbstring`, `openssl` et `fileinfo`. **SQLite est exclu.**

## Installation locale

Dans PowerShell :

```powershell
cd C:\Users\Zbook\projets\eventia
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Créez une base MySQL vide appelée `eventia`, puis réglez les paramètres du fichier `.env` :

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eventia
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

Utilisez un compte MySQL local approprié et ne publiez jamais son mot de passe. Les migrations métier ne sont pas encore écrites. Après les avoir créées, lancez `php artisan migrate`, puis `php artisan serve`.

La page charge le style depuis `public/css/eventia.css`. Pour utiliser le pipeline standard du squelette Laravel, installez les dépendances JavaScript puis lancez `npm install` et `npm run dev`. La source est `resources/css/eventia.css` ; gardez une seule version de la feuille de style quand vous relierez Vite.

## Vues et données attendues

| Vue | Rôle | Données à brancher |
| --- | --- | --- |
| `resources/views/welcome.blade.php` | Accueil et filtres | Événements paginés et catégories |
| `resources/views/components/event-card.blade.php` | Carte réutilisable | Titre, catégorie, date, lieu, places restantes |
| `resources/views/events/show.blade.php` | Détail et inscription | Événement, disponibilité et description |
| `resources/views/admin/events/index.blade.php` | Tableau de gestion | Événements et nombre d’inscriptions actives |
| `resources/views/admin/events/form.blade.php` | Création/modification | Événement facultatif, ancienne saisie et erreurs |
| `resources/views/registrations/index.blade.php` | Suivi des inscriptions | Participant, événement, date et statut |
| `resources/views/layouts/app.blade.php` | Layout commun | Messages flash et erreurs éventuelles |

Les jeux de données provisoires sont regroupés dans `app/Support/EventiaPreview.php`. Les routes de prévisualisation se trouvent dans `routes/web.php` ; elles filtrent uniquement un tableau en mémoire et ne traitent aucune écriture. Remplacez-les par vos contrôleurs quand le backend sera prêt.

## Guide pour poursuivre le backend

### Modèles et migrations à concevoir

Commencez par `Event` (titre, catégorie, description, date/heure, lieu, capacité) et `Registration` (événement, nom, courriel, statut ou date d’annulation). Définissez `Event hasMany Registration` et `Registration belongsTo Event`. Ajoutez les clés étrangères et index nécessaires. Calculez les places restantes depuis les inscriptions actives, sans enregistrer un compteur qui pourrait devenir incohérent.

### Contrôleurs à écrire

- `EventController` : liste, recherche et filtres validés, page de détail.
- `RegistrationController` : inscription et annulation contrôlée.
- `Admin/EventController` : liste, création, modification et suppression protégée.

### Routes à relier aux formulaires

- `GET /` avec `q`, `category` et `date` ; `GET /evenements/{event}`.
- `POST /evenements/{event}/inscriptions`.
- `GET /admin/evenements`, `GET /admin/evenements/create`, `POST /admin/evenements`.
- `GET /admin/evenements/{event}/edit`, `PUT /admin/evenements/{event}`, `DELETE /admin/evenements/{event}`.
- `GET /admin/inscriptions` et une route d’annulation autorisée.

Ajoutez `@csrf`, les directives `@method` nécessaires, les validateurs Laravel et des messages compréhensibles. Il faudra refuser les inscriptions aux événements passés ou complets, prévenir les doublons, traiter la capacité dans une transaction avec verrouillage MySQL et définir qui peut annuler une inscription. Conservez les filtres dans les liens de pagination. Écrivez ensuite des tests de fonctionnalités couvrant ces règles et les accès administratifs.

## Démonstration

Un parcours réaliste de 5 à 7 minutes se trouve dans [DEMONSTRATION.md](DEMONSTRATION.md). Les exemples de modification directe portent sur le texte d’accueil ou le jeu de données fictif ; ils ne simulent pas un changement en base.

## Limites connues

- Le squelette a été créé, mais Composer n’a pas encore terminé l’installation et la génération de `vendor/autoload.php` dans cet environnement. Lancer `composer install` sur une configuration réseau Composer fonctionnelle avant `php artisan`.
- Aucun test n’a été exécuté ; les tests métier restent à écrire.
- La base `eventia`, ses migrations et sa persistance restent à mettre en place.
- Les dates des événements de démonstration sont relatives au jour de prévisualisation et ne constituent pas un calendrier confirmé.
- Les polices web distantes sont facultatives ; une police système sert de remplacement.
- L’accès GitHub de `Prime-Dior` doit être réauthentifié avant la création et la publication du dépôt.

## Contributions

La répartition sera décidée et consignée par l’équipe au fil du travail réellement accompli. Ne présentez pas cette maquette comme un historique de contributions ni comme un backend terminé.
