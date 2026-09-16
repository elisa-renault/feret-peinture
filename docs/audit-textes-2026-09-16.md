# Audit et correction des textes

16 septembre 2026. Corrections appliquées aux fichiers du projet et à la base WordPress locale. Le serveur privé n’a pas été mis à jour pendant cette intervention.

## Ligne éditoriale

Passe légère avec Humanizer et Copy-editing : supprimer le superflu, reformuler uniquement pour clarifier. Vouvoiement, ton professionnel sobre et aucun tiret cadratin. Pas de persona ajoutée, de promesse commerciale nouvelle ou de recherche de familiarité.

Le contenu métier est déjà documenté. Les principaux défauts sont des titres sans information, des introductions abstraites et des répétitions sur la prise de contact.

## Principales corrections

| Emplacement | Avant | Après |
| --- | --- | --- |
| Accueil, entreprise | Un nom. Un contact direct. Derrière Feret Peinture, Christophe Feret. | Christophe Feret |
| Accueil, demande | Commençons simplement. Vous n’avez pas besoin d’avoir déjà toutes les réponses pour prendre contact. | Les informations utiles |
| Prestations | De la couleur. De la matière. Votre projet. | Peinture et revêtements |
| Zone | Implanté à Écouen. À propos de votre chantier. | Zone d’intervention |
| Présentation enregistrée | Christophe Feret est implanté à Écouen, dans le Val-d’Oise. L’entreprise exerce une activité de travaux de peinture. | Christophe Feret est peintre en bâtiment à Écouen, dans le Val-d’Oise. |
| Fiche prestation, contact | Et si on en parlait ? | Demander un devis |
| Photo de peinture intérieure | Le geste et la matière. Photo d’illustration. | Photo d’illustration. |
| Page introuvable | Cette page reste à trouver. | Page introuvable |
| Pied de page | Un site conçu avec soin par Aliant | Site conçu par Aliant |

Autres retouches : consignes de devis plus directes, réponses de FAQ raccourcies, suppression de rappels redondants sur la définition des travaux dans le devis, descriptions pour les moteurs de recherche allégées. Le formulaire indisponible n’annonce plus une disponibilité « bientôt » sans date connue.

Les fiches extérieure, murs et sols ont reçu des retouches ciblées : « système retenu » et « périmètre exact » remplacés par des termes usuels, introduction murale raccourcie, dimensions des rouleaux de papier peint explicitées. La fiche intérieure conserve son texte métier.

## Éléments volontairement conservés

- Détails des supports, préparations, produits et finitions, y compris les réserves sur le dégât des eaux et l’humidité.
- Date de création et exemples de chantiers à Domont, Ézanville et Gouvieux. Ces lieux ne deviennent pas une zone d’intervention garantie.
- Boutons explicites, consignes nécessaires au formulaire, avertissements de prévisualisation et mentions identifiant les photos d’illustration.
- Textes légaux préparatoires : aucune validation juridique ni information manquante inventée. Leur finalisation reste distincte de cet audit rédactionnel.

## Vérification du sens

Les retouches conservent les faits, noms, dates et détails métier. Aucune gratuité, certification, garantie, disponibilité ou rapidité de réponse n’a été ajoutée. Les formulations conditionnelles utiles sont conservées. La typographie française et les accents sont préservés.

## Application et contrôles

Le script `scripts/update-editorial-content.php` reconnaît les textes de la version documentée antérieure avant de les modifier. Il conserve les anciens contenus et les révisions, et ne remplace pas une présentation personnalisée. Le bootstrap utilise les nouveaux textes pour les installations futures.

- Première exécution locale : trois fiches et la présentation simplifiées.
- Deuxième exécution : aucune modification.
- Syntaxe PHP : contrôles du projet réussis.
- Recette HTTP : 107 contrôles réussis, dont les pages, métadonnées et retours du formulaire.
- Tests JavaScript d’envoi : deux tests réussis.
- Huit pages vérifiées à 320 et 1440 pixels : un seul titre principal par page, aucun débordement horizontal ni paragraphe vide inattendu.
- Accueil relu dans le navigateur ; contrôle visuel mobile effectué.

Pour une installation déjà passée par la mise à jour documentée, utiliser ce nouveau script éditorial. Les coordonnées et les réglages de publication restent conservés.

## Seconde passe : site et backoffice

À la demande d’Elisa, nouvelle relecture du site et des trois rubriques de Christophe. Corrections appliquées en local, sans mise à jour du serveur privé.

Sur le site : suppression du texte qui répète les quatre prestations et de l’introduction qui annonce simplement leur liste. La FAQ conserve trois réponses ; les consignes de demande et la zone d’intervention sont déjà expliquées dans les sections précédentes. Le lien d’appel, le titre sur la préparation des supports et les consignes du formulaire sont allégés. Le message de longueur maximale indique désormais « 100 caractères maximum », conformément au contrôle existant.

Dans le backoffice :

- Titres adaptés à chaque rubrique : « Modifier le chantier », « Modifier la prestation », « Modifier mes informations ». Les libellés d’ajout de prestations ne parlent plus de chantier.
- « Email public validé » devient « Email affiché sur le site » ; « Communes d’intervention confirmées » devient « Communes desservies », avec une aide qui limite la saisie aux communes acceptées.
- Les aides précisent l’emplacement de la présentation, l’usage du téléphone fixe si le principal est vide et l’affichage du message temporaire.
- « Mettre en avant » explique le classement des chantiers et les trois emplacements sur l’accueil.
- Les consignes d’enregistrement apparaissent dans les fiches, pas dans les listes. Une fiche publiée invite à mettre à jour ; un brouillon invite à enregistrer et vérifier l’aperçu.
- Le guide Christophe reprend les nouveaux libellés et retire les explications techniques inutiles à l’édition.

Les limites des révisions, les autorisations des photos et les informations nécessaires à la publication sont conservées. Les champs, leurs valeurs et les droits d’accès ne changent pas. Aucun engagement commercial ajouté ; aucun tiret cadratin dans les textes modifiés.

Contrôles de cette seconde passe : syntaxe PHP réussie, 31 assertions du formulaire réussies, 32 contrôles authentifiés du backoffice réussis. Les tests ont restauré les coordonnées initiales et supprimé leurs données temporaires. Vérification supplémentaire des libellés dans quatre écrans authentifiés : liste des chantiers, nouveau chantier, prestation publiée et informations. Accueil, prestations et devis vérifiés à 320 pixels : un titre principal et aucun débordement horizontal.

## Déploiement sur le serveur privé

Les deux passes ont été déployées le 16 septembre 2026 à la demande d’Elisa. Sauvegarde préalable du code et de la base dans `/srv/apps/feret-peinture/backups/editorial-20260916T134912Z`. Dix-huit fichiers transférés, empreintes et syntaxe PHP vérifiées sur le serveur. Trois fiches prestations et la présentation mises à jour ; seconde exécution sans modification.

Les textes ont été contrôlés sur six pages du site et trois écrans authentifiés du compte Christophe. Les contrôles du déploiement privé ont réussi : pages et ressources accessibles avec authentification, refus sans mot de passe, HTTPS et absence d’indexation conservés. Le blocage des emails de prévisualisation et les paramètres d’ouverture publique n’ont pas été modifiés.
