# Guide de soutenance Eventia — travail de Primael KPATINDE

**Projet d’initiation aux frameworks — IFRI-UAC, GL3, équipe 12 — année 2026-2027**

Enseignante : GAHOU Maryse · Sujet B : Gestion des événements et inscriptions
Binôme : Afwane SOULEYMANE et Primael KPATINDE

Ce guide sert à expliquer le vrai code du dépôt, à distinguer les contributions du binôme et à se préparer aux questions. Les commandes et résultats ci-dessous correspondent à l’état vérifié le 3 novembre 2026. La date de rendu annoncée dans le sujet était le 2 novembre 2026 à 23 h 59, heure du Bénin ; les changements terminés après cette date sont signalés comme postérieurs à l’échéance.

## 1. Présenter le projet en une minute

Eventia est une application Laravel en français pour découvrir des événements, chercher et filtrer les prochaines rencontres, s’y inscrire avec un compte, gérer ses inscriptions, et administrer les événements. Les vues sont écrites en Blade et partagées par un layout commun. Les données persistantes sont stockées dans MySQL 8. SQLite n’est pas utilisé.

Une requête suit le parcours Laravel classique :

```text
Navigateur → route web → middleware → contrôleur → modèle Eloquent → MySQL
                                                ↓
                                      vue Blade → réponse HTML
```

Par exemple, `GET /events` arrive dans `EventController::index`. Le contrôleur valide les filtres, construit une requête Eloquent, récupère les événements et catégories, puis transmet ces données à `resources/views/events/index.blade.php`.

## 2. Contributions réelles et historique

L’historique Git permet de dire honnêtement qui a apporté quoi. Les commits dont l’auteur est **Prime Dior** correspondent au compte du membre A, Primael KPATINDE. Ceux dont l’auteur est **Ratel404** correspondent au compte du membre B, Afwane SOULEYMANE. Les merges sont visibles dans l’historique et ne sont pas présentés comme du travail individuel.

### Travail attribué au membre A — Primael KPATINDE

- `6b81027` — initialise Eventia et prépare le frontend Blade.
- `4aac146` — relie les premières vues de prévisualisation aux routes Laravel.
- `9903a49` — développe les catégories et événements, leur CRUD, les filtres, les migrations, les seeders et les tests de cette partie.
- `151eeb1` — documente le module événements et son parcours de démonstration.
- `7d01788` — simplifie l’affichage des pages publiques d’événement.
- `124f610` — refuse les inscriptions aux événements passés et ajoute le test associé.
- `9eb799c` — rédige le premier guide de soutenance.
- `8314fc1` — documente les commandes Swagger dans le README.
- `4f05c59` — aligne les dépendances Composer sur PHP 8.4. Ces derniers changements ont été préparés après la date limite annoncée.

Le travail de Member A se trouve principalement dans `EventController`, `Category`, `Event`, les vues d’événement et d’administration, leurs migrations et factories/seeders, ainsi que `EventsManagementTest`.

### Travail attribué au membre B — Afwane SOULEYMANE

L’historique attribue à Ratel404 l’installation et la configuration de Fortify, les actions d’authentification, les pages de connexion/inscription, `AuthController`, `RegistrationController`, le modèle `Registration`, sa migration, les routes liées aux comptes et aux inscriptions, la page « mes inscriptions » et des tests du parcours. La PR nº 2 a intégré ce travail à `main`.

Le partage des responsabilités aide à raconter le projet, mais le code final est un travail d’équipe. Ne dites pas que vous avez écrit personnellement les fichiers dont l’historique attribue la création à l’autre membre.

## 3. Pages et routes

Les routes sont définies dans `routes/web.php` :

| Méthode et URL | Nom Laravel | Rôle |
| --- | --- | --- |
| `GET /` | — | Redirige vers `/events` |
| `GET /events` | `events.index` | Liste, recherche et filtres |
| `GET /events/{event}` | `events.show` | Détail, disponibilité et inscription |
| `GET /login`, `POST /login` | `login`, `login.store` | Formulaire et traitement de connexion |
| `GET /register`, `POST /register` | `register`, `register.store` | Création de compte |
| `POST /logout` | `logout` | Déconnexion authentifiée |
| `GET /registrations` | `registrations.index` | Inscriptions de l’utilisateur connecté |
| `POST /events/{event}/registrations` | `registrations.store` | Inscription à un événement |
| `DELETE /registrations/{registration}` | `registrations.destroy` | Annulation de l’inscription propre à l’utilisateur |
| `GET /admin/events` | `admin.events.index` | Tableau d’administration |
| `GET /admin/events/create`, `POST /admin/events` | `admin.events.create`, `admin.events.store` | Formulaire et création |
| `GET /admin/events/{event}/edit`, `PUT /admin/events/{event}` | `admin.events.edit`, `admin.events.update` | Modification |
| `DELETE /admin/events/{event}` | `admin.events.destroy` | Suppression protégée |

Les routes des événements administratifs sont groupées avec `auth` et `can:manage-events`. Le nom `admin.events.*` est un préfixe ; les noms complets permettent aux vues d’appeler `route('admin.events.create')` au lieu d’écrire une URL en dur.

Laravel réalise le binding de `{event}` et `{registration}` vers les modèles correspondants. Si l’identifiant n’existe pas, le binding répond 404 avant que l’action du contrôleur ne continue.

## 4. Base relationnelle et migrations

La configuration d’exemple et le défaut de connexion du projet utilisent MySQL. L’environnement de test utilise également MySQL, dans une base distincte appelée `eventia_testing`.

Schéma principal :

```text
users 1 ─────── * registrations * ─────── 1 events * ─────── 1 categories
```

- `users` contient les comptes, leurs courriels et mots de passe hachés. La migration `add_role_to_users_table` ajoute `role` (`user` par défaut, ou `admin`).
- `categories` contient un nom unique.
- `events` contient `category_id`, titre, description, date/heure, lieu et capacité. La clé étrangère empêche de rattacher un événement à une catégorie inexistante. Une catégorie utilisée ne peut pas être supprimée (`restrictOnDelete`). Les index aident les tris et filtres par date/catégorie.
- `registrations` contient une clé étrangère vers un utilisateur et un événement. La contrainte unique `(user_id, event_id)` empêche une double inscription même si une validation applicative était contournée. Les deux clés étrangères utilisent la suppression en cascade.

Une migration est une version exécutable du schéma. `up()` applique les changements ; `down()` les annule. La migration des événements dépend de l’existence des catégories, et celle des inscriptions dépend des tables `users` et `events`. Les horodatages des noms de fichiers déterminent l’ordre d’exécution.

Ne pas confondre la migration `add_role_to_users_table` créée pour l’autorisation admin et les migrations ajoutées ensuite par le membre B pour Fortify (deux facteurs/passkeys). Elles répondent à des besoins distincts.

## 5. Modèles Eloquent et relations

- `Category::events(): HasMany` : une catégorie possède plusieurs événements.
- `Event::category(): BelongsTo` : chaque événement référence une catégorie.
- `Event::registrations(): HasMany` : un événement regroupe les inscriptions associées.
- `Registration::event()` et `Registration::user(): BelongsTo` : chaque inscription appartient à un événement et un compte.
- `User::registrations(): HasMany` : le compte connecté peut consulter ses propres inscriptions.

Dans `Event`, le cast `date => datetime` transforme la valeur SQL en objet date Carbon. Cela permet d’appeler `isPast()`, `format()` ou `translatedFormat()`. Le cast `capacity => integer` évite de traiter la capacité comme une chaîne.

La capacité restante n’est pas stockée séparément : elle est calculée à partir de la capacité et du nombre d’inscriptions. Cela évite deux compteurs qui pourraient diverger. Aujourd’hui l’annulation supprime l’inscription ; si l’équipe décidait de conserver un statut annulé, il faudrait compter seulement les inscriptions actives.

## 6. Contrôleur d’événement : recherche et filtres

`EventController::index` valide les paramètres d’URL :

```php
'q'        => nullable, chaîne, longueur maximale 120
'category' => nullable, entier, identifiant existant dans categories
'date'     => nullable, format AAAA-MM-JJ
```

Ensuite, Eloquent prépare une requête sur les événements à venir. `with('category')` charge les catégories liées en une requête groupée et évite de répéter une requête pour chaque carte (problème N+1). `when(...)` ajoute chaque filtre uniquement s’il est renseigné. `whereDate` compare le jour choisi, tandis que la recherche du titre utilise un `LIKE` transmis par le Query Builder avec paramètres protégés.

`orderBy('date')` trie chronologiquement. `paginate(9)` limite la liste à neuf résultats par page. `withQueryString()` garde les filtres dans les liens de pagination. La vue reçoit aussi la liste des catégories pour remplir le menu.

Le code utile à expliquer est donc : « valider les entrées, construire la requête selon les filtres, ordonner, paginer, puis transmettre le résultat à Blade ». La recherche a lieu côté serveur ; JavaScript ne masque pas simplement des cartes déjà chargées.

## 7. CRUD d’événements, validation et autorisation

`create` fournit un nouvel objet Event et les catégories au formulaire. `store` appelle `validatedEvent`, puis crée le modèle. `edit` prépare les données existantes ; `update` les valide puis les modifie. `destroy` refuse de supprimer un événement qui possède encore des inscriptions, pour ne pas perdre leur historique ou casser leurs références.

Les règles partagées contrôlent :

- catégorie obligatoire et existante (`exists:categories,id`) ;
- titre et lieu obligatoires avec longueur maximale ;
- description obligatoire et limitée ;
- date valide et postérieure à maintenant (`after:now`) ;
- capacité entière entre 1 et 100 000.

Laravel redirige automatiquement vers le formulaire si la validation échoue, conserve les anciennes valeurs et met les erreurs en session. Les vues les affichent près des champs avec `@error` et réaffichent les saisies avec `old(...)`.

Le Gate `manage-events` dans `AppServiceProvider` autorise uniquement l’utilisateur authentifié avec `role === 'admin'`. Le middleware `auth` vérifie d’abord qu’une session existe ; `can:manage-events` vérifie ensuite le droit précis. Cacher un bouton dans Blade ne sécurise pas une action : la route doit aussi refuser l’accès côté serveur.

Pour la capacité, `update` refuse une nouvelle capacité inférieure au nombre d’inscriptions existantes. La suppression est bloquée dès qu’une inscription référence l’événement.

## 8. Authentification, inscription et annulation

Le membre B a installé/configuré Fortify et ajouté les écrans nécessaires. Le projet possède toutefois des routes de formulaire traitées par `AuthController` :

- Création de compte : validation du nom, courriel unique et mot de passe confirmé d’au moins huit caractères ; le compte est créé avec le cast `hashed` du modèle User, puis la session est ouverte et régénérée.
- Connexion : vérifie courriel/mot de passe avec `Auth::attempt`; en cas de succès, régénère la session pour empêcher la fixation de session. En cas d’échec, renvoie un message sans révéler si le courriel existe.
- Déconnexion : invalide la session et régénère le jeton CSRF.
- Limitation des essais : la route login applique `throttle:login`; Fortify définit une limite par adresse/courriel et adresse IP.

`RegistrationController::store` exécute les contrôles et l’insertion dans une transaction :

1. Recharge l’événement et le verrouille avec `lockForUpdate()`.
2. Refuse l’événement passé avec `Event::isPast()`.
3. Refuse une inscription déjà présente pour ce même utilisateur.
4. Compare le nombre d’inscriptions à la capacité.
5. Crée l’inscription si les contrôles passent.

Le verrouillage InnoDB de la ligne événement fait attendre une deuxième requête concurrente. Après le premier commit, la deuxième relit le nombre mis à jour avant de décider. La contrainte unique `(user_id, event_id)` constitue une protection de base supplémentaire contre le doublon.

Le contrôle de date ajouté vérifie la date **dans cette même transaction, après le verrou** : une requête ne peut donc pas utiliser un état lu avant le verrou pour contourner la règle. Si l’événement est passé, aucune ligne n’est créée et un message flash explique que les inscriptions sont closes.

Pour annuler, `destroy` vérifie que l’inscription appartient au compte connecté avant de la supprimer. Changer simplement l’identifiant d’une autre personne renvoie 403. L’annulation libère une place parce que le comptage porte sur les lignes encore présentes.

Limite à expliquer honnêtement : l’annulation est une suppression définitive ; le projet ne garde pas d’historique d’annulation. Un statut `cancelled_at`/`status` serait une extension si l’enseignante demandait de conserver cet historique.

## 9. Blade, formulaires et CSS

`resources/views/layouts/app.blade.php` contient le squelette HTML, la navigation, les messages flash et le pied de page. Les vues étendent ce layout avec `@extends`, définissent leur titre avec `@section`, puis insèrent leur contenu dans `@section('content')`.

Les composants Blade comme `components/event-card.blade.php` évitent de recopier le HTML d’une carte. La page de liste boucle sur les résultats avec `@foreach`. Les formulaires utilisent `@csrf`, et les opérations de modification/suppression utilisent `@method('PUT')` ou `@method('DELETE')` pour exprimer la méthode HTTP attendue.

Les valeurs affichées par `{{ ... }}` sont échappées automatiquement par Blade, ce qui réduit le risque d’injection HTML. Le CSS principal est dans `public/css/eventia.css`, servi directement par Laravel ; Vite n’est pas nécessaire au rendu de ces pages. Les vues sont responsive et gardent une présentation simple. Les formulaires, messages d’erreur et états « passé/complet/inscrit » proviennent de données et d’états réellement traités par Laravel.

## 10. Seeders et données de démonstration

`CategorySeeder` crée des catégories avec `firstOrCreate`, donc une deuxième exécution ne duplique pas leur nom. `EventSeeder` appelle le seeder de catégories puis utilise `updateOrCreate` par titre pour rendre les exemples répétables. Les dates de démonstration sont relatives à la date d’exécution et changent donc au fil du temps.

`DatabaseSeeder` appelle le seeder d’événements et crée un compte de test ordinaire. Il ne crée pas de compte administrateur. Pour une démonstration CRUD, un compte admin doit être préparé localement avec `role=admin` par l’équipe, sans publier son mot de passe ni utiliser une donnée personnelle réelle.

## 11. Tests exécutés et ce qu’ils prouvent

Les tests sont des tests HTTP Laravel avec PHPUnit et `RefreshDatabase`. Ils utilisent MySQL dans la base séparée `eventia_testing`, jamais la base locale conservant les données de démonstration.

La commande exécutée sur l’état actuel :

```powershell
vendor/bin/phpunit
```

Résultat constaté : **12 tests réussis, 59 assertions**. Le test ciblé sur l’événement passé a également réussi seul : 1 test, 4 assertions.

Les tests couvrent la navigation et le filtrage, le détail et les 404, les accès admin, la création/modification/suppression, les données invalides, les variations de navigation avec session, le parcours d’inscription, le doublon, la limite de capacité et le refus d’inscription à un événement passé. Le test confirme aussi qu’aucune inscription n’est créée quand l’événement est passé.

Un test suit souvent trois étapes : **Arrange** (préparer utilisateur, événement et catégorie), **Act** (envoyer une requête HTTP), **Assert** (vérifier réponse, session ou état MySQL). Les tests n’essaient pas de simuler un véritable trafic concurrent en parallèle ; la stratégie de verrouillage est donc expliquée à partir du code et du comportement InnoDB.

## 12. Installation et lancement pour la présentation

Prérequis : PHP 8.4 avec `pdo_mysql`, Composer, MySQL 8. Node/npm ne sont pas nécessaires au CSS direct. Dans PowerShell :

```powershell
cd C:\Users\Zbook\projets\eventia
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Créer deux bases MySQL locales : `eventia` pour l’application et `eventia_testing` pour les tests. Dans `.env`, garder `DB_CONNECTION=mysql` et compléter nom, utilisateur et mot de passe localement. Ne jamais transmettre `.env`.

Puis :

```powershell
php artisan migrate
php artisan db:seed
php artisan serve
```

Pour tester :

```powershell
vendor/bin/phpunit
```

Pour voir les routes :

```powershell
php artisan route:list
```

## 13. Parcours conseillé de soutenance (5 à 7 minutes)

1. **Accueil (1 min)** : ouvrir `/events`, montrer la liste puis combiner titre, catégorie et date.
2. **Détail (1 min)** : montrer la catégorie, la date, les places et le bouton d’inscription ; expliquer ce qui apparaît pour un visiteur connecté ou non.
3. **Inscription (1 min)** : se connecter avec un compte local préparé, s’inscrire, constater l’inscription et montrer la page `/registrations` ; annuler et expliquer la vérification de propriété.
4. **Administration (1 min)** : utiliser un compte préparé avec rôle admin, créer/modifier un événement, puis montrer qu’un utilisateur ordinaire reçoit un refus.
5. **Code (1 à 2 min)** : montrer `routes/web.php`, suivre `/events` dans `EventController`, présenter `belongsTo/hasMany`, puis ouvrir la migration et expliquer la clé étrangère ou l’unicité des inscriptions.
6. **Test et changement live (1 min)** : lancer un test ciblé ou changer un libellé Blade et rafraîchir la page.

Ne pas improviser un mot de passe admin ni prétendre que les tests vérifient des scénarios de concurrence simultanée.

## 14. Questions possibles de l’enseignante

**Pourquoi une migration ?** Elle versionne la structure SQL afin que chaque membre de l’équipe puisse créer la même base avec une commande.

**Pourquoi Eloquent plutôt que SQL concaténé ?** Eloquent exprime les relations et filtre les valeurs via le Query Builder, ce qui garde les requêtes lisibles et évite de construire le SQL avec les entrées utilisateur.

**À quoi sert une relation `belongsTo` ?** Elle indique qu’une ligne enfant porte la clé étrangère : par exemple `events.category_id` désigne sa catégorie.

**Comment éviter les inscriptions au-delà de la capacité ?** La transaction verrouille la ligne événement, recompte avant insertion ; InnoDB sérialise les modifications concurrentes à cet événement. Une contrainte unique protège le doublon à la base.

**Comment empêcher un utilisateur d’annuler l’inscription d’un autre ?** Le contrôleur vérifie que l’identifiant d’inscription se trouve dans la relation de l’utilisateur connecté avant la suppression.

**Pourquoi vérifier aussi côté serveur si le formulaire cache le bouton ?** Un utilisateur peut envoyer directement une requête HTTP. La règle métier doit donc être appliquée par le contrôleur, indépendamment du HTML.

**Que reste-t-il comme limite ?** Les annulations effacent la ligne au lieu d’en conserver un historique. Le test automatisé couvre les règles fonctionnelles principales, mais pas un essai de charge multi-clients.

## 15. Références Laravel officielles

- [Routing](https://laravel.com/docs/13.x/routing)
- [Eloquent relationships](https://laravel.com/docs/13.x/eloquent-relationships)
- [Validation](https://laravel.com/docs/13.x/validation)
- [Database transactions](https://laravel.com/docs/13.x/database#database-transactions)
- [Pessimistic locking](https://laravel.com/docs/13.x/queries#pessimistic-locking)
- [Authorization](https://laravel.com/docs/13.x/authorization)
- [HTTP tests](https://laravel.com/docs/13.x/http-tests)

## 16. API REST et Swagger

Eventia propose deux interfaces dans le même projet Laravel : les pages web Blade, qui renvoient du HTML, et une API versionnée, qui renvoie du JSON. Les routes API sont déclarées dans `routes/api.php` sous le préfixe `v1`, par exemple `/api/v1/events`. La version facilite l’évolution future du contrat sans modifier immédiatement les clients existants.

| Méthode et URL | Accès | Fonction |
| --- | --- | --- |
| `POST /api/v1/auth/register` | Public | Créer un compte |
| `POST /api/v1/auth/login` | Public, limité à 5 essais par minute | Se connecter et recevoir un jeton Sanctum |
| `GET /api/v1/auth/me`, `POST /api/v1/auth/logout` | Jeton Sanctum | Consulter le compte ou révoquer le jeton courant |
| `GET /api/v1/events`, `GET /api/v1/events/{event}` | Public | Lister ou consulter un événement |
| `POST /api/v1/events`, `PUT /api/v1/events/{event}`, `DELETE /api/v1/events/{event}` | Administrateur authentifié | Gérer les événements |
| `GET/POST /api/v1/registrations` | Jeton Sanctum | Consulter ses inscriptions ou demander une inscription |
| `GET/DELETE /api/v1/registrations/{registration}` | Jeton Sanctum | Consulter ou annuler une inscription autorisée |

Le middleware `auth:sanctum` exige une identité authentifiée. `can:manage-events` ajoute le contrôle du rôle administrateur aux routes de gestion. Le client transmet le jeton dans l’en-tête `Authorization: Bearer <jeton>`. Ce jeton ne doit pas être partagé ni committé.

Les contrôleurs API sont dans `app/Http/Controllers/Api/V1/`. Ils renvoient des réponses JSON adaptées aux clients HTTP. Ils coexistent avec les contrôleurs web : les deux interfaces font partie de Laravel, mais produisent des formats de réponse différents.

L’historique attribue la mise en place de l’API, de Sanctum et de Swagger à Afwane SOULEYMANE (Ratel404), notamment le commit `b98e5e1`. La PR #3 a intégré cette branche dans `main`. Ma contribution à cette partie est l’ajout des instructions Swagger au README (`8314fc1`) et l’alignement de Composer avec PHP 8.4 (`4f05c59`), sans modifier le travail API de mon binôme.

### Générer et utiliser Swagger UI

Depuis la racine du projet, après `composer install` et la configuration de `.env` :

```powershell
php artisan l5-swagger:generate
php artisan serve
```

Ouvrir `http://127.0.0.1:8000/api/documentation`. La première commande génère le document OpenAPI à partir des attributs PHP présents dans `app/OpenApi/ApiSpec.php` et les contrôleurs API. Swagger UI lit ce document pour présenter les routes, paramètres, réponses et schémas. La spécification générée se trouve sous `storage/api-docs/`; les attributs PHP versionnés restent sa source.

Les routes publiques peuvent être essayées directement. Pour les routes protégées, appeler d’abord l’endpoint de connexion, puis saisir le jeton obtenu dans **Authorize**. `401` correspond généralement à une authentification absente ou invalide, `403` à une identité sans autorisation suffisante, et `422` à des données refusées par la validation.

`php artisan route:list --path=api` permet de voir les routes réellement chargées avec leurs contrôleurs et middleware. Swagger facilite les essais manuels, mais ne remplace pas les tests automatisés.

## 17. Dépendances et compatibilité PHP

`composer.json` déclare les paquets et les versions de PHP compatibles; `composer.lock` fixe les versions installées. Le commit `4f05c59` aligne la contrainte PHP du projet sur l’environnement PHP 8.4 et actualise le verrouillage nécessaire. J’ai vérifié l’installation et la cohérence avec :

```powershell
composer install
composer check-platform-reqs
composer validate
```

Ces commandes ont réussi avec PHP 8.4.0 et les extensions requises. Elles confirment respectivement que les dépendances peuvent être installées, que la plateforme dispose des prérequis et que les fichiers Composer sont cohérents. Elles ne remplacent pas les tests fonctionnels.

## 18. État du dépôt et vérifications

Avant cette mise à jour documentaire, la branche locale `main` était alignée avec `origin/main` sur le commit de fusion `2be2d82` (PR #3), avec un arbre de travail propre. La version principale contient donc le travail API de Ratel404 et les changements de compatibilité Composer de Prime-Dior.

Les vérifications réalisées sur le projet comprennent 12 tests PHPUnit réussis avec 59 assertions, la génération de Swagger, une réponse HTTP 200 pour Swagger UI et une réponse HTTP 200 pour la liste publique des événements. Cela ne signifie pas qu’un test de charge ou une simulation de plusieurs inscriptions simultanées a été effectué.

Le compte administrateur discuté dans nos échanges est configuré dans la base locale de démonstration. Il n’est pas fourni par un seeder versionné; ses identifiants sont omis de ce guide. Les mots de passe, jetons et secrets restent dans l’environnement local.

À retenir pour expliquer le projet :

1. Blade et l’API sont deux interfaces du même Laravel : HTML d’un côté, JSON de l’autre.
2. Les routes dirigent les requêtes et les middleware contrôlent l’accès.
3. Eloquent relie les modèles aux tables; les migrations décrivent le schéma MySQL reproductible.
4. La validation explique les erreurs à l’utilisateur; les contraintes SQL protègent aussi l’intégrité des données.
5. Les tests vérifient des scénarios reproductibles; Swagger documente et facilite l’essai manuel des endpoints.

## 19. Références

- [Laravel Routing](https://laravel.com/docs/13.x/routing)
- [Eloquent relationships](https://laravel.com/docs/13.x/eloquent-relationships)
- [Validation](https://laravel.com/docs/13.x/validation)
- [Transactions et verrouillage](https://laravel.com/docs/13.x/database#database-transactions)
- [Authorization](https://laravel.com/docs/13.x/authorization)
- [HTTP tests](https://laravel.com/docs/13.x/http-tests)
- [Laravel Sanctum](https://laravel.com/docs/13.x/sanctum)
- [Laravel Fortify](https://laravel.com/docs/13.x/fortify)
- [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)
- [Spécification OpenAPI](https://spec.openapis.org/oas/latest.html)