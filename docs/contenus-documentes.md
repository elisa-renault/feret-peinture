# Contenus issus du fonds professionnel

Mise à jour du 16 septembre 2026. Les documents originaux restent dans le dossier de ressources privé « Documents Christophe Feret ». Aucun document client ni extrait nominatif ne doit être ajouté au dépôt ou aux médias WordPress.

## Éléments intégrés

- Accueil : positionnement peinture, décoration, revêtements de sols et murs ; préparation des supports ; explication du devis détaillé.
- Entreprise : préparation, finitions et description des travaux pièce par pièce.
- Peinture intérieure : murs, plafonds, portes, huisseries, radiateurs, reprise après dégât des eaux ; doublage et faux plafond comme travaux complémentaires à étudier.
- Peinture extérieure : ravalement, bois extérieurs et grilles métalliques.
- Revêtements muraux : dépose, préparation et toile de verre.
- Sols : préparation, sous-couche, ragréage et revêtements stratifiés ou PVC selon le projet.
- Localisation : Domont, Ézanville et Gouvieux comme exemples de chantiers documentés, sans extension automatique de la zone d’intervention confirmée.

## Preuves et limites

Précision d’Elisa lors de la vérification du 16 septembre 2026 : un devis confirme une prestation proposée au même titre qu’une facture. Il n’est pas nécessaire de trouver une facture pour valider sa présence dans le catalogue. La distinction ne concerne que l’affirmation qu’un chantier a été réalisé. Voir [la vérification des prestations](verification-prestations-2026-09-16.md), qui confirme aussi la pose de papier peint et relève le PVC collé et le carrelage absents du texte actuel des sols.

Les trois devis PDF de rénovation de novembre 2025 et quatre factures PDF de 2025-2026 ont été consultés visuellement pour leurs descriptions de travaux et lieux d’exécution. Les passages relatifs aux sols ont été repérés dans les documents Word de 2026 ; ils valident les prestations proposées, au même titre que les factures, conformément à la confirmation d’Elisa. Un relevé de sources nominatif reste dans le dossier de ressources privé, hors dépôt.

Les noms, adresses des clients, prix, signatures et documents bruts ne sont pas repris sur le site. Les conditions de paiement d’un devis ne deviennent pas des conditions générales. Aucune certification, garantie, disponibilité ou gratuité n’est déduite. Les documents ne valent pas autorisation de publication de photos ; aucune réalisation illustrée n’a été créée.

## Application

Les textes initiaux sont dans `config/pods/services.php`. Le bootstrap reste non destructif. Pour une installation existante, `wp eval-file scripts/update-documented-content.php` met à jour uniquement les quatre textes initiaux reconnus. Il refuse toute personnalisation inconnue avant écriture, sauvegarde les champs antérieurs dans une option non chargée automatiquement et conserve les révisions. Une seconde exécution ne modifie rien.

Les modèles d’accueil, d’entreprise et de zone sont modifiés directement. Les coordonnées, les autorisations éditoriales, le formulaire et les protections de prévisualisation ne sont pas changés.

## Vérifications et livraison

Mise à jour appliquée en local puis sur la prévisualisation privée le 16 septembre 2026, après sauvegarde de la base et des fichiers concernés. Syntaxe PHP vérifiée ; 107 contrôles HTTP locaux réussis ; sept pages contrôlées à 320, 390 et 1440 pixels sans débordement horizontal. Captures accueil mobile et ordinateur examinées. Deuxième passage de la migration : zéro modification. Contrôles du serveur privé réussis, y compris accès aux images et refus sans authentification. Les illustrations déjà présentes localement ont été transférées avec leurs styles et leur modèle de prestation pour compléter la prévisualisation.
