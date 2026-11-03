# Ma participation au projet Eventia — Primael KPATINDE

**Projet d’initiation aux frameworks — IFRI-UAC, GL3, équipe 12 — 2026-2027**
**Enseignante :** GAHOU Maryse · **Sujet B :** Gestion des événements et inscriptions
**Équipe :** Afwane SOULEYMANE (`Ratel404`) et Primael KPATINDE (`Prime-Dior`)

## Pourquoi ce guide

Ce document explique **ma contribution personnelle** telle qu’elle apparaît dans les commits attribués à `Prime-Dior` / `Prime Dior`. Il détaille les changements que j’ai faits dans le frontend, le backend des événements, les tests et la documentation. Il ne m’attribue pas le travail de mon binôme.

Le dépôt contient aussi des fonctionnalités réalisées par Afwane (authentification, inscriptions, API et Swagger). Elles sont intégrées à l’application finale, mais je les présente ici seulement pour situer les parties avec lesquelles mon code s’articule.

## Ma participation en bref

- J’ai créé le projet Laravel Eventia et posé la première structure Blade, les pages de prévisualisation et les styles de départ.
- J’ai relié les vues de démonstration à des routes web Laravel.
- J’ai développé le backend de gestion des événements : modèles `Event` et `Category`, migrations, relations Eloquent, recherche et filtres, affichage, CRUD d’administration, autorisation administrateur, seeders et tests associés.
- J’ai simplifié l’apparence des pages publiques d’événement et rendu leur feuille CSS plus sobre.
- J’ai ajouté le refus d’inscription à un événement passé et un test pour cette règle dans le flux d’inscription existant.
- J’ai préparé des documents de présentation, documenté Swagger dans le README et aligné les dépendances Composer sur PHP 8.4.

Ces contributions ont été faites après la date limite indiquée dans le sujet (2 novembre 2026 à 23 h 59, heure du Bénin), comme le montrent les dates Git. Il faut respecter cette chronologie pendant la présentation.

## Mes commits repérables dans Git

| Commit | Contribution personnelle |
| --- | --- |
| `6b81027` | Initialisation d’Eventia, base Laravel, premières vues Blade, styles et documentation de démarrage. |
| `4aac146` | Routes web pour relier les vues de prévisualisation et premières retouches des vues et du README. |
| `9903a49` | Module événements : modèles, migrations, relations, contrôleur, filtres, administration, seeders, factories et tests. |
| `151eeb1` | Documentation du module et parcours de démonstration. |
| `7d01788` | Simplification visuelle des pages publiques et de la feuille CSS. |
| `124f610` | Refus d’une inscription à un événement passé et test de régression associé. |
| `9eb799c` | Guide initial, documentation de soutenance et configuration MySQL par défaut. |
| `8314fc1` | Instructions d’utilisation de Swagger ajoutées au README. |
| `4f05c59` | Ajustement des dépendances Composer pour PHP 8.4. |

La mise à jour actuelle du guide est également un changement documentaire. Les commits de Ratel404 ne sont pas listés comme mes réalisations.

## 1. Frontend Blade que j’ai préparé

### Organisation et rôle des vues

J’ai créé la première version des vues dans `resources/views` :

- `layouts/app.blade.php` : structure HTML partagée, navigation, zone centrale et pied de page ;
- `events/index.blade.php` et `events/show.blade.php` : liste/aperçu des événements et fiche de détail ;
- `components/event-card.blade.php` : présentation réutilisable d’un événement ;
- `admin/events/index.blade.php` et `admin/events/form.blade.php` : maquettes de la liste et du formulaire de gestion ;
- `registrations/index.blade.php` : maquette de l’espace des inscriptions.

Les premiers écrans utilisaient des données de démonstration regroupées dans `app/Support/EventiaPreview.php`. Cela permettait de montrer et de régler le frontend avant la création du backend métier. Ces données provisoires ne constituent pas une base de données et ne doivent pas être décrites comme des inscriptions réellement persistées.

Les vues sont écrites avec Blade, le moteur de templates de Laravel. Le layout évite de recopier le squelette HTML. Les directives comme `@foreach` affichent une collection, `{{ $event->title }}` insère une valeur échappée, et les composants permettent de réutiliser le même rendu de carte. Plus tard, les contrôleurs ont remplacé les données provisoires par des modèles Eloquent.

### Routes de prévisualisation

Dans `routes/web.php`, j’ai relié les pages à des routes nommées. Une route `GET` affiche la liste ou la fiche. Au stade de la maquette, les routes d’écriture retournaient explicitement un message indiquant qu’aucune modification n’était enregistrée. Cette étape rendait les formulaires prévisualisables sans faire croire qu’ils persistaient déjà les données.

Ensuite, dans `9903a49`, j’ai relié la liste, le détail et les pages d’administration au vrai contrôleur et à la base. L’application finale contient aussi les routes d’authentification et d’inscription ajoutées par Ratel404. Il faut séparer les routes de démonstration initiales de celles qui exécutent aujourd’hui les opérations métier.

### Présentation visuelle

Le style de l’application se trouve principalement dans `public/css/eventia.css` (avec les fichiers CSS de ressources gardés par le projet). Dans `7d01788`, j’ai réduit et simplifié la feuille de style ainsi que le rendu des cartes et pages publiques. L’objectif était une interface plus lisible, avec une hiérarchie simple, des informations d’événement visibles et un affichage adaptable aux écrans étroits.

Pour expliquer le frontend à l’oral : « Les vues présentent les données, mais ne décident pas si une action métier est autorisée. Les règles de capacité et les validations sont vérifiées côté serveur. »

## 2. Backend événements que j’ai développé

### Modèle relationnel et migrations

Dans `9903a49`, j’ai ajouté le modèle `Category` et le modèle `Event`, leurs migrations et leurs factories/seeders. Une catégorie peut avoir plusieurs événements; un événement appartient à une catégorie. `events.category_id` est la clé étrangère qui porte ce lien.

La migration des catégories crée les noms de catégories. La migration des événements crée notamment `category_id`, titre, description, date, lieu et capacité. La clé étrangère évite d’enregistrer un événement lié à une catégorie inexistante. Les migrations sont la description versionnée du schéma SQL : Laravel peut les appliquer avec `php artisan migrate`.

J’ai aussi ajouté le champ `role` à la table des utilisateurs avec `user` comme valeur par défaut, puis un Gate `manage-events` dans `AppServiceProvider`. Ce contrôle est utilisé par les routes d’administration afin qu’un utilisateur ordinaire ne puisse pas gérer les événements simplement en saisissant une URL.

### Modèle Eloquent `Event`

Dans `app/Models/Event.php`, j’ai défini les attributs remplissables, les conversions de types et les relations. Le cast `date => datetime` permet de manipuler la date comme un objet Carbon; `capacity => integer` garantit un entier côté application.

- `category()` retourne `belongsTo(Category::class)` : l’événement porte la clé étrangère de la catégorie.
- `registrations()` retourne `hasMany(Registration::class)` : plusieurs inscriptions peuvent concerner un événement. Le modèle Registration et son flux ont été complétés par mon binôme.
- `scopeUpcoming()` ajoute le filtre des dates futures à une requête.
- `places_remaining` calcule la capacité moins le nombre d’inscriptions; le résultat n’est pas stocké comme compteur indépendant.
- `isPast()` et `isFull()` centralisent deux questions réutilisées par les pages et la logique applicative.

Éviter de stocker deux valeurs dérivées — capacité et places restantes — aide à prévenir les incohérences de comptage.

### Recherche et filtres côté serveur

Dans `EventController::index`, je valide les paramètres reçus dans l’URL : `q` est une chaîne limitée en longueur, `category` est un identifiant existant et `date` suit le format `Y-m-d`. Puis je construis une requête Eloquent sur les événements à venir.

```php
$query = Event::query()->with('category')->upcoming();

$events = $query
    ->when($filters['q'] ?? null, fn ($query, $term) =>
        $query->where('title', 'like', "%{$term}%"))
    ->when($filters['category'] ?? null, fn ($query, $id) =>
        $query->where('category_id', $id))
    ->when($filters['date'] ?? null, fn ($query, $date) =>
        $query->whereDate('date', $date))
    ->orderBy('date')->paginate(9)->withQueryString();
```

`when` n’ajoute le filtre que si une valeur est fournie. `with('category')` charge les catégories associées pour éviter une requête répétée par carte (problème N+1). `paginate(9)` limite la quantité affichée et `withQueryString()` conserve les filtres en changeant de page. La recherche est donc exécutée dans la requête backend, pas par un simple masquage JavaScript.

### Détail et administration des événements

J’ai créé les actions de `EventController` pour afficher un événement et administrer la collection :

- `index` prépare les événements, filtres, catégories et pagination ;
- `show` prépare l’événement, sa catégorie et son nombre d’inscriptions pour la vue ;
- `adminIndex`, `create` et `edit` préparent les écrans d’administration ;
- `store` crée un événement après validation ;
- `update` valide les nouvelles valeurs puis met à jour le modèle ;
- `destroy` refuse la suppression lorsque des inscriptions référencent l’événement.

La validation partagée contrôle la catégorie existante, les champs obligatoires, les longueurs, une date future et une capacité entière dans les limites définies. Elle protège le backend même si quelqu’un contourne les contrôles HTML du formulaire.

La route d’administration est protégée côté serveur par l’authentification et `can:manage-events`. Masquer les liens d’administration dans Blade améliore l’interface, mais n’est pas une mesure d’autorisation suffisante à elle seule.

### Seeders et factories

J’ai préparé des factories pour créer des événements/catégories pendant les tests et des seeders pour fournir des exemples de démonstration. `firstOrCreate` / `updateOrCreate` rendent les données réexécutables sans accumuler les mêmes exemples à chaque lancement. Le `DatabaseSeeder` et le compte admin doivent être distingués : le dépôt ne fournit pas un mot de passe admin public; le compte utilisé localement pour la démo est une configuration locale.

## 3. Mon changement dans le flux d’inscription

Le contrôleur complet d’inscription et la majeure partie du parcours utilisateur ont été développés par Afwane. Ma contribution ciblée dans `124f610` est la règle qui refuse l’inscription quand l’événement est déjà passé, plus le test qui prouve qu’aucune ligne n’est créée.

Dans `RegistrationController::store`, cette vérification est placée dans la transaction, après le rechargement et le verrouillage de la ligne événement :

```php
$event = Event::query()
    ->whereKey($event->getKey())
    ->lockForUpdate()
    ->firstOrFail();

if ($event->isPast()) {
    return 'past';
}
```

La position dans la transaction est importante : la décision s’appuie sur l’événement relu après obtention du verrou. Le contrôleur transforme ensuite ce résultat en redirection avec un message compréhensible. Cette modification s’ajoute aux contrôles de doublon et de capacité existants; je ne revendique pas l’écriture de toute la transaction.

Dans `RegistrationFlowTest`, j’ai ajouté un scénario qui crée un événement passé, soumet la requête d’inscription, vérifie le message d’erreur, puis confirme que la table `registrations` reste vide. C’est un test de régression : si la règle disparaît plus tard, le test doit échouer.

## 4. Tests que j’ai écrits ou complétés

`tests/Feature/EventsManagementTest.php`, ajouté avec le module événements, vérifie notamment :

- qu’un visiteur peut consulter les événements et filtrer par titre, catégorie et date ;
- qu’une fiche événement s’affiche et qu’un identifiant inexistant renvoie 404 ;
- qu’un utilisateur non administrateur ne peut pas ouvrir l’administration ;
- qu’un administrateur peut créer, modifier et supprimer un événement ;
- que les données invalides sont refusées.

J’ai aussi ajouté dans le test de flux existant le cas de refus d’inscription à une date passée. Un test Laravel de fonctionnalité prépare les données, envoie une vraie requête HTTP au routeur de l’application, puis vérifie la réponse ou l’état de la base. Ce n’est pas seulement un appel direct à une méthode PHP.

Au dernier état vérifié avant cette mise à jour, la suite entière donnait **12 tests réussis et 59 assertions**. Ce nombre couvre toute l’équipe et pas uniquement les tests que j’ai écrits. Il n’y a pas eu de test de charge multi-clients prouvant la concurrence réelle.

## 5. Documentation et configuration auxquelles j’ai contribué

- Dans `151eeb1` et `9eb799c`, j’ai documenté le projet, le module événements et la démonstration.
- Dans `8314fc1`, j’ai ajouté au README les deux commandes permettant de générer Swagger et de lancer Laravel, puis l’adresse de Swagger UI. La documentation technique Swagger elle-même et ses annotations API sont le travail de Ratel404.
- Dans `9eb799c`, j’ai changé la connexion Laravel par défaut de SQLite à MySQL, conformément à la contrainte du projet. `DB_CONNECTION` de `.env` reste la configuration locale à vérifier.
- Dans `4f05c59`, j’ai ajusté la contrainte PHP et `composer.lock` pour rendre l’installation cohérente avec PHP 8.4. Les vérifications `composer install`, `composer check-platform-reqs` et `composer validate` ont réussi dans l’environnement utilisé.
- J’ai ajouté le générateur PDF du guide et l’exclusion Git du PDF. Le fichier PDF personnel est livré dans Téléchargements et n’est pas versionné.

## 6. Ce que mon binôme a fait — pour bien délimiter ma part

Afwane SOULEYMANE (`Ratel404`) a principalement créé le parcours d’authentification et d’inscription : configuration Fortify, écrans et traitements de connexion/inscription/déconnexion, contrôleur et modèle Registration, migration associée, espace « mes inscriptions », annulation et plusieurs tests. Il a également préparé l’API `/api/v1`, Sanctum, les contrôleurs API et la documentation Swagger. Ses commits et la fusion de la PR #3 sont visibles dans l’historique Git.

Ces fonctionnalités utilisent les modèles, routes ou vues de l’ensemble du projet, mais ne doivent pas être présentées comme mes réalisations individuelles. Ma propre partie backend principale est la gestion des catégories/événements et leur administration, ainsi que le contrôle ciblé des événements passés dans le flux d’inscription.

## 7. Parcours de présentation de ma participation

1. **Montrer la liste `/events`** : expliquer les filtres validés, la requête Eloquent, le tri et la pagination.
2. **Choisir un événement** : montrer la relation avec Category et les valeurs de disponibilité qui alimentent la vue.
3. **Montrer les formulaires admin** avec un compte de démonstration préparé localement : relier la route, l’action du contrôleur, la validation et le modèle créé/modifié.
4. **Ouvrir `Event.php`** : expliquer `belongsTo`, `hasMany`, le cast date et le calcul des places restantes.
5. **Ouvrir la migration** : expliquer la clé étrangère, puis lancer `php artisan migrate:status` pour montrer l’état des migrations locales.
6. **Montrer le test de recherche ou celui de date passée** : lire la préparation, la requête envoyée et les assertions.
7. **Montrer Swagger brièvement en attribuant sa création à Afwane**; expliquer que ma contribution est la documentation d’utilisation ajoutée au README.

Les tests exécutés et vérifications documentés ont été réalisés après la date limite officielle. Ne pas présenter leurs dates comme antérieures au rendu.

## Questions possibles sur mon travail

**Pourquoi les filtres sont-ils dans le contrôleur ?** Le contrôleur valide la requête HTTP et compose une requête de lecture; la vue affiche seulement le résultat. La logique reste côté serveur.

**Pourquoi utiliser `with('category')` ?** Pour charger les catégories liées en groupe et éviter de lancer une requête SQL pour chaque événement affiché.

**Pourquoi calculer les places restantes ?** C’est une valeur dérivée de la capacité et des inscriptions. La calculer évite qu’un compteur stocké devienne incohérent.

**Que garantit une migration ?** Elle décrit une évolution reproductible du schéma SQL et permet à chaque membre de préparer la même structure avec Artisan.

**Quel changement est précisément ma contribution à l’inscription ?** Le refus lorsqu’un événement est passé, ajouté dans la transaction déjà présente, et son test de régression. La logique générale d’inscription/annulation est principalement attribuée à mon binôme.

**Qui a créé l’API et la documentation interactive ?** Ratel404 a développé les endpoints et leur documentation Swagger. J’ai ajouté au README les instructions pour les lancer et m’en servir.

## Commandes utiles pour revoir mes contributions

Depuis la racine `C:\Users\Zbook\projets\eventia` :

```powershell
# Voir mes commits dans l’historique
 git log --author="Prime Dior" --oneline

# Lancer les tests du projet
 vendor/bin/phpunit

# Voir les routes web chargées
 php artisan route:list --path=events

# Vérifier l’état des migrations locales
 php artisan migrate:status
```

La base de test doit être configurée séparément de la base de démonstration. Ne partagez pas `.env`, les mots de passe, les jetons ou les identifiants locaux.