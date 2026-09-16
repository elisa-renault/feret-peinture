# Chantiers d’exemple du 16 septembre 2026

Trois fiches ont été ajoutées à la demande d’Elisa dans la base locale et la prévisualisation privée :

- `/realisations/exemple-sejour-lumineux/`
- `/realisations/exemple-salon-escalier/`
- `/realisations/exemple-sol-aspect-bois/`

Elles utilisent les fiches existantes : photo principale, description courte, prestation associée, texte et galerie. Titres, commune et contenu indiquent leur caractère fictif. Les galeries montrent des ambiances différentes, pas le même chantier. Aucun faux avant/après n’est présenté.

Le script `scripts/seed-stock-projects.php` importe les images dans la médiathèque avec les tailles WordPress et conserve les fiches déjà importées lors d’une nouvelle exécution. Il refuse les environnements hors prévisualisation.

```sh
docker compose run --rm wpcli wp eval-file /project/scripts/seed-stock-projects.php
```

Chaque fiche porte `_fp_demo=1`, utilisé par les contrôles existants pour l’exclure des listes et de l’accès direct en production. `publication_authorized=1` sert ici au mécanisme de visibilité de la prévisualisation uniquement ; il ne constitue pas une attestation de photos de travaux réels. Les exemples restent non mis en avant et peuvent être mis à la corbeille depuis Mes chantiers. Avant livraison publique, retirer ces fiches et leurs médias de démonstration et créer de nouvelles fiches pour les travaux authentiques.

## Sources des photographies

Photos utilisées sous [licence Pexels](https://www.pexels.com/license/), consultée le 16 septembre 2026. Sources également conservées dans chaque pièce jointe et créditées dans les fiches :

- [Séjour vide, Curtis Adams](https://www.pexels.com/photo/an-empty-living-room-7027840/).
- [Salon et escalier, Allyson SALNESS](https://www.pexels.com/photo/a-modern-living-room-with-wooden-floor-and-stairs-8288962/).
- [Salon avec sol bois, Curtis Adams](https://www.pexels.com/photo/living-room-with-wooden-floor-7027720/).
