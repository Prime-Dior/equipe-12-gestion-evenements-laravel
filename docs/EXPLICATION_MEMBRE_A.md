# Notes de présentation — Eventia, partie du membre A

**Projet d’initiation aux frameworks — IFRI-UAC, GL3, équipe 12 — 2026-2027**

Sujet B : gestion des événements et inscriptions. Enseignante : GAHOU Maryse.
Membres : Afwane SOULEYMANE et Primael KPATINDE.

Ce document explique le travail préparé dans la branche `feature/events-admin`. Il décrit l’état réel du code, pas une répartition artificielle de commits. La partie comptes et inscriptions reste au membre B.

## 1. Comment présenter l’architecture

Laravel suit MVC : les routes web reçoivent la requête, un contrôleur traite le cas d’usage, les modèles Eloquent lisent ou modifient MySQL et les vues Blade produisent le HTML. Les migrations décrivent le schéma de base de données versionné avec le code.

Dans cette branche, `routes/web.php` associe les URL au `EventController`. Les pages publiques sont `/events` et `/events/{event}`. Le groupe `/admin/events` applique d’abord le middleware `auth`, puis l’autorisation `can:manage-events`. Le second contrôle passe par le Gate défini dans `AppServiceProvider` : seul un utilisateur ayant `role = admin` peut gérer les événements.

Le formulaire d’administration utilise `@csrf` contre les requêtes intersites et `@method('PUT')` ou `@method('DELETE')` pour exprimer les méthodes HTTP que les formulaires HTML ne savent pas envoyer directement. Les erreurs Laravel sont affichées près des champs.

## 2. Modèle relationnel

### Catégories et événements

La migration `create_categories_table` crée une catégorie avec un nom unique. `create_events_table` crée les événements et leur clé étrangère `category_id`. `restrictOnDelete()` empêche de supprimer une catégorie encore utilisée. Les index accélèrent le classement et les recherches par date et catégorie.

`Category::events()` est une relation `hasMany` : une catégorie peut regrouper plusieurs événements. `Event::category()` est la relation inverse `belongsTo`. `Event::registrations()` prépare la relation attendue du membre B ; la classe et la table correspondantes ne sont pas encore présentes sur cette branche.

Les événements portent titre, description, date/heure, lieu et capacité. La capacité est stockée, mais pas le nombre restant : celui-ci dépend des inscriptions. Cette branche soustrait le nombre de lignes d’inscription si leur table existe ; sans cette table, elle affiche provisoirement la capacité complète. Si le membre B ajoute un statut d’annulation, il faudra ajuster ce comptage pour ne soustraire que les inscriptions encore actives.

La migration `add_role_to_users_table` ajoute le rôle nécessaire au Gate administrateur. Elle doit être coordonnée avec le membre B afin qu’il ne crée pas une deuxième migration pour la même colonne.

### Données d’exemple

`CategorySeeder` crée les catégories avec `firstOrCreate`. `EventSeeder` cherche une catégorie et utilise `updateOrCreate` sur le titre. Relancer `EventiaSeeder` met à jour les mêmes événements plutôt que d’en ajouter une nouvelle copie. Les dates sont calculées relativement au jour d’exécution.

## 3. Contrôleur et requêtes

### Liste, recherche et filtres

`EventController::index` valide `q`, `category` et `date`. Cela borne le texte, vérifie que la catégorie existe et impose un format de date. Le contrôleur démarre une requête Eloquent sur les événements à venir, charge leur catégorie (`with`) et ajoute un `where` seulement si le filtre a été fourni (`when`).

La recherche utilise une requête paramétrée Eloquent (`LIKE`) ; aucune valeur utilisateur n’est concaténée dans du SQL brut. `whereDate` compare le jour sans tenir compte de l’heure. `paginate(9)` limite la taille d’une page et `withQueryString()` conserve les filtres lorsqu’on passe à la page suivante.

### Détail et binding

`show(Event $event)` utilise le binding implicite de route : Laravel résout `{event}` à partir de l’identifiant. Si aucun événement n’existe, Laravel répond 404. La vue affiche son statut passé/complet/disponible et le nombre calculé de places.

### CRUD administrateur

`create` et `edit` préparent le formulaire et la liste des catégories. `store` et `update` partagent `validatedEvent`, qui contrôle catégorie existante, longueurs, champs requis, date future et capacité positive bornée. Laravel redirige vers le formulaire avec les erreurs si une donnée est invalide.

La mise à jour refuse une capacité inférieure au nombre d’inscriptions déjà enregistrées. La suppression est refusée si l’événement possède des inscriptions afin de préserver les références et l’historique. Le comptage correspondant n’est effectué que si la migration des inscriptions du membre B existe.

Ces protections ne remplacent pas la future transaction d’inscription : la vérification simultanée de capacité lors de deux inscriptions concurrentes appartient au membre B.

## 4. Fichiers à connaître

| Fichier | Rôle |
| --- | --- |
| `routes/web.php` | URL publiques et CRUD protégé |
| `app/Http/Controllers/EventController.php` | filtres, détail, validation et CRUD |
| `app/Models/Category.php` | modèle et relation vers les événements |
| `app/Models/Event.php` | champs, casts, catégorie, inscriptions prévues et places restantes |
| `app/Providers/AppServiceProvider.php` | Gate `manage-events` |
| `database/migrations/*categories*`, `*events*`, `*role*` | schéma MySQL de cette partie |
| `database/factories/` et `database/seeders/` | données utilisées pour tests et démonstration |
| `resources/views/events/` | liste et détail public |
| `resources/views/admin/events/` | tableau et formulaire CRUD |
| `resources/views/components/event-card.blade.php` | carte partagée de la liste |
| `tests/Feature/EventsManagementTest.php` | tests des pages, filtres, accès et CRUD |

## 5. Tests et base de données

Le projet utilise MySQL ; SQLite n’est pas utilisé. PHPUnit est configuré pour `eventia_testing`, séparée de la base locale `eventia`. Le test `RefreshDatabase` prépare le schéma de test puis les tests fabriquent leurs propres catégories, événements et utilisateurs.

Dernière exécution observée : **7 tests réussis, 35 assertions, 0 échec**. Les cas vérifiés comprennent la liste et les filtres, le détail et l’erreur 404, la protection de l’administration, le CRUD et la validation invalide. Aucun test d’inscription participant n’est présent car son code appartient au membre B.

Pour relancer :

```powershell
php artisan test
```

La connexion MySQL de test doit être disponible et la base `eventia_testing` créée. Ne faites pas pointer la configuration de test vers des données à conserver.

## 6. Parcours de démonstration

1. Afficher `/events`, rechercher un titre et combiner catégorie et date.
2. Ouvrir un événement et commenter date, lieu, catégorie et capacité.
3. Se connecter via l’authentification intégrée par le membre B avec un compte réellement configuré, puis montrer `/admin/events`.
4. Créer ou modifier un événement, provoquer une erreur de validation, puis montrer le message.
5. Ouvrir le test de filtrage et expliquer Arrange / Act / Assert : créer les données, appeler l’URL, vérifier le HTML.
6. Faire une modification simple d’un libellé Blade et recharger.

Ne pas annoncer de compte admin de démo tant qu’un seeder d’utilisateur avec mot de passe documenté n’a pas été effectivement ajouté.

## 7. Suite à réaliser par le membre B

- Ajouter l’authentification et s’assurer que `User` possède le champ `role` de cette branche.
- Créer `Registration`, sa migration, les clés étrangères et l’unicité retenue pour empêcher les doublons.
- Définir les statuts et qui peut annuler une inscription.
- Écrire les contrôleurs/routes d’inscription et d’annulation.
- Utiliser une transaction et un verrouillage MySQL approprié pour ne jamais dépasser la capacité.
- Relier le formulaire sur la page d’événement et compléter la page/liste des inscriptions.
- Tester doublon, complet, événement passé, annulation, accès interdit et concurrence autant que possible.

## 8. Commandes utiles

Après installation des dépendances et configuration `.env` vers MySQL :

```powershell
php artisan migrate
php artisan db:seed --class=EventiaSeeder
php artisan serve
```

Le travail est isolé dans une branche feature. La publication attendue suit le guide d’équipe : push de la branche, puis Pull Request vers `main`. Ne pas forcer le push ni pousser directement sur `main`.

## 9. Références Laravel

- [Routing](https://laravel.com/docs/13.x/routing)
- [Eloquent relationships](https://laravel.com/docs/13.x/eloquent-relationships)
- [Validation](https://laravel.com/docs/13.x/validation)
- [Pagination](https://laravel.com/docs/13.x/pagination)
- [Authorization](https://laravel.com/docs/13.x/authorization)
- [Testing](https://laravel.com/docs/13.x/http-tests)
