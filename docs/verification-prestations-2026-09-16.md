# Vérification des prestations avec les documents sources

Vérification du 16 septembre 2026 des quatre fiches de `config/pods/services.php` avec le fonds privé « Documents Christophe Feret ». Selon la précision d’Elisa, un devis confirme une prestation proposée au même titre qu’une facture. La distinction devis/facture ne concerne que la présentation éventuelle d’un chantier comme réalisé.

## Résultat

Les quatre familles présentées sont documentées. Aucun intitulé de prestation actuellement décrit n’est contredit par les pièces consultées. La fiche des sols est toutefois incomplète : les devis comportent aussi du PVC collé et du carrelage.

| Prestation du site | Justificatif consulté | Conclusion |
| --- | --- | --- |
| Peinture des murs et plafonds, préparation, finitions mates ou satinées | Devis 11.2025.046 p. 2, 047 p. 1 et 048 p. 1 ; facture 11.2025.030 p. 1 | Confirmée. |
| Portes, huisseries, plinthes et radiateurs | Devis 11.2025.046 p. 1 et 2 | Confirmée. |
| Doublage et faux plafond en plaques de plâtre | Devis 11.2025.046 p. 1 et 2 | Confirmée ; doublage avec BA13 et faux plafond BA13 sur ossature métallique. |
| Reprise après dégât des eaux et toile de verre | Facture portant le numéro 07.2026.027, p. 1 | Confirmée. Le nom du fichier commence par 10.2026.027, mais la facture est datée du 10 juillet 2026. |
| Cage d’escalier | Devis 11.2025.048 p. 1 ; facture 09.2026.031 p. 1 | Confirmée. |
| Ravalement de maison et garage | Facture 07.2026.028 p. 1 et 2 | Confirmée, avec préparation, impression et finition. |
| Dessous de toit, bois des lucarnes et grilles métalliques | Facture 07.2026.028 p. 2 | Confirmée, y compris peinture antirouille. |
| Dépose du papier peint et préparation | Devis Word 09.2026.045 ; également 02.2026.007 et 013 | Confirmée par les passages textuels des originaux. |
| Pose de papier peint | Devis Word 02.2026.010 | Confirmée explicitement : préparation des fonds et pose de papier peint. |
| Sous-couche, stratifié ou PVC en pose flottante, plinthes et seuil | Devis Word 01.2026.002 | Confirmée, y compris barre de seuil à rattrapage de niveaux et rabotage de porte. |
| Dépose, préparation et ragréage | Devis Word 05.2026.033 | Confirmée, avec primaire et ragréage type P3. |

## Prestations documentées absentes de la fiche des sols

- PVC en pose collée : devis 05.2026.033, fourniture et pose collée d’un revêtement PVC, avec préparation du support. Le texte actuel ne mentionne que la pose flottante.
- Carrelage : devis 03.2026.025, pose avec plinthes, joints et barre de seuil. Le carrelage n’apparaît pas dans la fiche actuelle.

Ces deux prestations sont confirmées pour le catalogue par les devis. À la demande d’Elisa, elles ont ensuite été ajoutées à la fiche des sols et à son résumé le 16 septembre 2026, dans le code et dans WordPress sur la prévisualisation privée. Mise à jour via `scripts/update-flooring-content.php`, avec sauvegarde préalable de la base et du fichier de contenu, sauvegarde des anciens champs et révision WordPress. Une seconde exécution ne modifie rien. La fiche et la liste des prestations ont été contrôlées par HTTP : PVC collé et carrelage présents, réponses 200 avec authentification, protection 401 sans authentification et noindex conservés.

## Méthode et limites

Lecture visuelle de neuf pages utiles issues de sept PDF : devis 046 p. 1-2, 047 p. 1, 048 p. 1 ; factures 030 p. 1, 028 p. 1-2, 031 p. 1 et 027 p. 1. Recherche des prestations dans les 46 fichiers Word de devis de 2026, dont deux versions du numéro 043 ; lecture des passages textuels correspondants, sans validation de la mise en page Word ni des quantités ou prix. Le fonds historique complet n’a pas été audité.

La comparaison porte sur les textes du dépôt, pas sur une nouvelle lecture de la base WordPress de prévisualisation. Les conseils généraux de préparation d’une demande ne sont pas traités comme des prestations supplémentaires. Les noms et adresses clients ainsi que les montants restent hors du dépôt.
