# Déroulé de démonstration (5 à 7 minutes)

Ce parcours présente ce qui existe réellement sur `feature/events-admin`. Le module des inscriptions reste à réaliser par le membre B.

1. **Liste publique (1 min)** — ouvrir `/events`, chercher un titre et filtrer par catégorie/date. Expliquer que le contrôleur valide les paramètres, compose une requête Eloquent et conserve les filtres dans la pagination.
2. **Détail (45 s)** — ouvrir une fiche. Montrer catégorie, lieu, date, capacité et places restantes. Préciser que l’inscription participant n’est pas encore câblée.
3. **Administration (1 min)** — se connecter avec un compte dont `role` vaut `admin`, puis ouvrir `/admin/events`. Présenter le formulaire, les erreurs de validation, la mise à jour et la suppression protégée. Il n’existe pas encore de compte de démonstration fourni ; ne pas inventer d’identifiants.
4. **Modèle de données (1 min)** — expliquer `Category hasMany Event` et `Event belongsTo Category`. La clé étrangère et les index sont définis par les migrations. Le membre B ajoutera `Registration` et ses contraintes.
5. **Règle métier et tests (1 min)** — expliquer la validation du serveur, la protection `auth` et `can:manage-events`, et montrer `tests/Feature/EventsManagementTest.php`. Le nombre de places est calculé à partir des inscriptions dès que leur table existe ; le contrôle transactionnel simultané devra être ajouté au parcours d’inscription.
6. **Modification en direct (1 min)** — modifier un libellé Blade ou un événement du seeder, relancer le seeder si nécessaire, puis recharger. Expliquer la différence entre une vue et les données persistées dans MySQL.

Pour préparer la démo, installer le projet, configurer MySQL, exécuter les migrations et le seeder. Vérifier que les pages d’authentification du membre B sont intégrées avant de présenter l’accès administrateur.

Questions à préparer : à quoi sert le binding `{event}` ? Pourquoi séparer migration et modèle ? Que fait `withQueryString()` ? Pourquoi un rôle admin doit être vérifié côté serveur ? Comment protéger une capacité lorsqu’on inscrit deux personnes simultanément ?
