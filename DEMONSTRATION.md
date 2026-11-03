# Déroulé de soutenance (5 à 7 minutes)

Le guide complet, avec explications du code, des migrations, des requêtes, des tests et des contributions, se trouve dans [`docs/EXPLICATION_MEMBRE_A.md`](docs/EXPLICATION_MEMBRE_A.md).

1. **Liste et filtres (1 min)** — ouvrir `/events`, rechercher un titre, choisir une catégorie et une date. Expliquer que `EventController::index` valide les paramètres et compose une requête Eloquent côté serveur.
2. **Détail et inscription (1 min)** — ouvrir un événement. Montrer date, lieu, places et états. Avec un compte utilisateur, s’inscrire puis ouvrir `/registrations`.
3. **Annulation (45 s)** — annuler l’inscription et expliquer que le contrôleur vérifie qu’elle appartient à l’utilisateur connecté avant de la supprimer.
4. **Administration (1 min)** — avec un compte préparé ayant le rôle admin, créer ou modifier un événement. Expliquer `auth`, `can:manage-events` et le Gate. Le dépôt ne fournit pas de mot de passe d’administrateur de démonstration.
5. **Architecture (1 min)** — suivre une route vers le contrôleur, le modèle Eloquent et la vue Blade ; présenter `Category hasMany Event` et `Event belongsTo Category`.
6. **Base et test (1 min)** — expliquer migrations, clé étrangère, unicité `(user_id, event_id)` et verrou `lockForUpdate()`. Montrer le test qui refuse une inscription à un événement passé.
7. **Petite modification (30 s)** — modifier un libellé Blade puis recharger, ou ajuster un événement du seeder et le réexécuter.

La dernière suite PHPUnit exécutée a réussi avec 12 tests et 59 assertions. La correction de la règle des événements passés et les derniers changements documentaires sont postérieurs à l’échéance annoncée du 2 novembre 2026.
