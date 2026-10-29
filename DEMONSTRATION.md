# Préparation de la démonstration (5 à 7 minutes)

Ce parcours distingue clairement l’interface préparée du backend restant à construire.

1. **Accueil (1 min)** — présenter navigation, cartes et filtres ; préciser que le filtrage agit sur des données fictives en mémoire.
2. **Détail (1 min)** — montrer les informations d’événement et le formulaire de réservation ; aucune inscription n’est enregistrée.
3. **Gestion (1 min)** — parcourir le tableau et le formulaire de création/modification ; les actions sont visuelles.
4. **Inscriptions (30 s)** — expliquer les colonnes et statuts d’exemple.
5. **Structure Laravel (1 min)** — présenter le layout Blade, le composant carte, les routes de prévisualisation et `EventiaPreview`.
6. **Suite pédagogique (1 min)** — décrire les futurs modèles, relations, migrations MySQL, contrôleurs et validations, sans prétendre qu’ils existent.
7. **Modification en direct (1 min)** — changer le texte du hero ou un titre dans `EventiaPreview`, puis rafraîchir la page. Cela ne modifie aucune base de données.

Questions de préparation : comment protéger la capacité contre deux inscriptions simultanées ? Comment conserver l’historique d’une annulation ? Comment protéger les opérations d’administration ? Quels tests démontreront ces règles ?
