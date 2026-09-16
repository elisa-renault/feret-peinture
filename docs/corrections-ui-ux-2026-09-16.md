# Corrections UI / UX : 16 septembre 2026

Suite à l’audit et à l’accord de mise en œuvre. Modifications appliquées au projet local ; aucune publication.

## Résultat

- FAQ corrigée à 320 px : mots séparés et grille réductible, sans masquer un débordement.
- Page devis raccourcie ; formulaire avant les conseils dans le document et à l’écran. Alternative téléphone compacte en tête, conseils et coordonnées détaillées à droite sur ordinateur et après le formulaire sur mobile.
- Premier champ à **572 px au lieu d’environ 899 px** sur une fenêtre de 390 × 844 : il est maintenant visible dès le premier écran, y compris avec les avertissements de prévisualisation.
- Accueil mobile allégé : illustration et monogramme moins hauts, familles de prestations annoncées dans l’introduction. Section Prestations vers **996 px au lieu de 1 118 px** à 390 px.
- Lien de zone renommé pour annoncer sa destination informative.
- Tous les liens de devis d’une fiche prestation, en-tête et pied de page compris, conservent la prestation ; le choix reste modifiable dans le formulaire.
- Téléphone et email regroupés sous une consigne commune. En cas d’absence des deux, le résumé mène au groupe et les deux champs sont associés à la même explication.
- Bouton « Envoi en cours… », annonce accessible et protection contre une seconde soumission. Le POST serveur et la validation native du navigateur sont conservés. Le script rétablit le bouton lors d’un événement de restauration de page.
- Confirmation rédigée simplement, sans demander systématiquement d’appeler pour vérifier la réception. Aucun délai de réponse ou de lecture promis.
- Navigation clarifiée : Zone d’intervention remplace le doublon Contact ; bouton « Demander un devis » explicite ; repère de rubrique sur les fiches prestations. Menu compact jusqu’à 1 000 px, avec fermeture au clavier.
- Image de partage PNG originale, 1 200 × 630, environ 19 ko, reliée aux métadonnées Open Graph. Composition typographique et aplats de couleur, sans représentation d’un chantier.
- Version du thème portée à 1.0.1 pour renouveler les styles et le script en cache.

## Contrôles exécutés

| Contrôle | Résultat |
| --- | --- |
| Syntaxe PHP de tous les fichiers couverts par `scripts/lint.sh` | Réussie dans Docker local |
| Syntaxe du script du site et de la suite navigateur | Réussie |
| `tests/form-integration.php` | **31 assertions réussies**, dont les nouvelles associations d’erreurs aux coordonnées |
| `npm run test:ui` | **2 tests réussis** : état d’envoi / seconde soumission ; restauration de l’interface sur `pageshow` |
| Six pages × six largeurs, 320 / 360 / 390 / 768 / 1024 / 1440 px | **36 contrôles**, un H1 et aucune largeur de document supérieure à la largeur utile |
| FAQ à 320 px, réponse ouverte | Texte « Quelques réponses utiles. », largeur du document et largeur utile égales à 305 px |
| Menu tablette à 768 px | Entrée ouvre, Tab atteint Prestations, Échap ferme et remet le focus sur Menu |
| Quatre liens de devis de la fiche peinture intérieure | Tous portent la prestation ; présélection vérifiée après navigation |
| Soumission navigateur locale sans coordonnées | Résumé d’erreur focalisé, saisie conservée, lien vers le groupe, descriptions accessibles sur téléphone et email |
| Correction de la demande avec une adresse fictive `.test` | Envoi vers Mailpit et nouvelle confirmation observée |

La première soumission automatisée, trop rapide, a rencontré la protection minimale de deux secondes ; elle a conservé la saisie et permis une nouvelle tentative. La protection n’a pas été modifiée.

La suite Playwright du dépôt a été enrichie (320 px, menu tablette, séparation du titre FAQ, consignes des coordonnées, visibilité du premier champ), mais **n’a pas été lancée intégralement**. Les 36 vérifications ont été effectuées séparément dans le navigateur intégré. Le test JavaScript sans navigateur a été ajouté à la CI ; la CI distante n’a pas été exécutée ici.

Le retour historique vers une réponse POST a rencontré `ERR_CACHE_MISS` dans le navigateur intégré. Le comportement `pageshow` est couvert par le test JavaScript, mais le retour arrière après POST reste à vérifier dans Chrome/Safari usuels. Aucun stockage des données du formulaire n’a été ajouté pour contourner cette limite.

## Mesures et captures

[Mesures complètes après corrections](audit-ui-ux/mesures-responsive-apres.json).

| Largeur de fenêtre | Haut du premier champ, après correction |
| --- | --- |
| 320 px | 655 px |
| 360 px | 592 px |
| 390 px | 572 px |
| 768 px | 561 px |
| 1024 px | 601 px |
| 1440 px | 607 px |

*Capture conservée localement, exclue du dépôt public car elle peut contenir des coordonnées non validées.*

*Capture conservée localement, exclue du dépôt public car elle peut contenir des coordonnées non validées.*

*Capture conservée localement, exclue du dépôt public car elle peut contenir des coordonnées non validées.*

## Éléments nécessaires à la suite

Les constats éditoriaux 3 et 4 de l’audit restent ouverts : demander à Christophe les photos autorisées, la nature réelle des travaux, les communes et quelques descriptions de chantiers. Préciser les prestations directement dans les contenus WordPress existants une fois les réponses obtenues ; modifier uniquement le fichier d’initialisation ne mettrait pas à jour ces contenus.

Restent aussi la recette de l’administration avec Christophe, le contrôle sur appareils et navigateurs réels, Lighthouse, la livraison vers la vraie boîte email et les conditions d’ouverture déjà listées dans `docs/publication.md`. L’image de partage est prête, mais son affichage sur les plateformes externes devra être vérifié après publication autorisée.
