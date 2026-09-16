# Feret Peinture — dossier de référence

Version de travail du 16 septembre 2026, heure de Paris (consultation web le 15 septembre 2026 vers 23 h UTC). Le brief fourni est daté du 16 septembre. Ce document distingue les éléments fournis, les contrôles effectivement possibles et les décisions à obtenir. Une donnée d'annuaire n'est pas une validation personnelle de Christophe.

## Mission et périmètre retenus

Acquérir des demandes de devis pertinentes et faciliter le contact direct avec Christophe Feret, peintre en bâtiment implanté à Écouen. « Feret Peinture » est un nom d'usage proposé, sans affirmation de marque déposée. Domaine acheté selon le brief : `feret-peinture.fr`.

WordPress classique, thème sur mesure, Pods gratuit et plugin métier léger. Elisa / Aliant conserve l'administration technique. Christophe modifie chantiers, prestations et informations dans WordPress. Aucun achat de licence, boutique, compte visiteur, chatbot ou blog vide. Le dépôt contient le code ; l'exécution nécessite PHP et une base de données. Aucune publication, modification DNS ou modification de fiche Google Business n'est autorisée par cette mission.

## Informations métier à valider

Le projet présente Christophe Feret, peintre implanté à Écouen. Les coordonnées, identifiants administratifs, justificatifs et sources nominatives détaillées sont à conserver dans un dossier privé, hors dépôt public.

Avant ouverture du site, obtenir et valider les téléphones, l’adresse administrative, les identifiants de société, la forme juridique, le capital, l’immatriculation et les informations fiscales. Saisir les coordonnées approuvées dans « Mes informations » et compléter les pages légales dans WordPress. Le dépôt ne préremplit aucun téléphone.

## Hypothèses de positionnement

- Cible prioritaire : particuliers préparant le rafraîchissement ou la rénovation d'un logement ; professionnels en second plan.
- Différenciation : interlocuteur nommé, implantation explicite, travaux décrits simplement, preuves photographiques authentiques lorsqu'elles existent.
- Écouen est l'implantation. Ézanville, Domont, Saint-Brice-sous-Forêt et Villiers-le-Bel restent des communes candidates, sans page SEO ni couverture promise avant accord.
- Le parcours répond au métier, à la localisation du chantier, aux preuves disponibles et au contact. Conversion principale : demande traitée côté serveur puis remise au transport email ; livraison réelle à contrôler séparément. Un clic téléphone n'est pas un appel abouti.

## Direction artistique et rédaction

Atelier contemporain, nuancier et matières abstraites, grandes marges, composition asymétrique et boutons simples. Sans photographie autorisée, le hero graphique est l'identité de la V1 ; il ne représente aucun chantier. Aucune galerie factice en production.

| Usage | Couleur de départ |
| --- | --- |
| Fond | `#F6F3EC` |
| Texte | `#282E2D` |
| Structure | `#23423E` |
| Appels à l'action | `#9F3F2E` |
| Surface secondaire | `#D8CBBB` |

Deux familles maximum : Newsreader pour les grands titres et Source Sans 3 pour les textes, uniquement si fichiers et licences locaux présents ; alternatives système sinon. Le sable ne sert pas de petit texte sur fond clair. Contrastes et responsive : résultats dans [recette.md](recette.md), aucune conformité supposée.

Vouvoyer. Nommer Christophe ou l'entreprise, sans équipe inventée. H1 d'accueil : « Peintre en bâtiment à Écouen. » Invitation : présenter les travaux et la commune pour préparer la demande. CTA : « Demander un devis », « Appeler Christophe ». Aucun superlatif creux, avis fictif, compteur ou citation inventée.

## Architecture et contenus

Accueil ; prestations et quatre fiches distinctes ; réalisations autorisées et fiches ; entreprise ; zone d'intervention ; contact orientant vers le formulaire unique `/devis/` ; mentions légales ; confidentialité ; remerciement non indexable ; 404 utile. Pas de multiplication artificielle des pages de villes. Les liens Réalisations doivent disparaître en production tant qu'aucune fiche authentique publiée n'est éligible.

Chaque fiche chantier doit disposer d'une commune sans adresse de client, d'un titre descriptif, d'une prestation, d'un texte court et de photos autorisées. « Avant/après » exige les deux prises réelles. Les fixtures ne sont qu'un outil de staging explicitement activé.

## Validations à obtenir avant ouverture

| Responsable | Élément à approuver | Comportement en attendant |
| --- | --- | --- |
| Christophe | Téléphone principal et fixe, email réellement opérationnel | Champs de coordonnées vides à l’installation ; pas de boîte inventée. |
| Christophe | Quatre familles, travaux acceptés, préparation/protection/nettoyage, communes desservies | Textes prudents ; aucune promesse de zone exhaustive. |
| Christophe | Présentation et éventuel portrait ; droits de chaque photo | Hero abstrait ; galerie masquée sans chantier autorisé. |
| Représentant de la société | Forme, siège, capital, RCS/TVA, éditeur et directeur de publication | Mentions en statut de travail ; ouverture bloquée. |
| Christophe / Aliant | Hébergeur exact et coordonnées, prestataire email, destinataire | Aucune identité de prestataire supposée. |
| Responsable du site | Médiateur applicable et coordonnées ; textes d'information ; durées | Aucun médiateur ni durée légale « universelle » inventé. |
| Aliant | SMTP, expéditeur autorisé et livraison réelle, sauvegarde/restauration, HTTPS | Mailpit local ; aucune sollicitation réelle automatique. |
| Elisa | Hébergement et autorisation de publication/DNS | Site local ou staging protégé uniquement. |

Non confirmés et donc non promis : devis gratuit, prix, délai de réponse, disponibilité, garantie décennale, RGE/Qualibat, peintures écologiques, absence de sous-traitance, taille d'équipe, nombre de clients, note d'avis, WhatsApp ou Google Business Profile.

Pour les données, textes juridiques de travail et ordre d'ouverture, voir [publication.md](publication.md). Pour les logiciels, licences et limites de provenance, voir [resources.md](resources.md).
