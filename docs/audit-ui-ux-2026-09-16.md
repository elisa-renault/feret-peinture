# Audit UI / UX : Feret Peinture

16 septembre 2026 : audit du projet local et de sa prévisualisation sur `http://localhost:8080/`.

## Avis général

**La direction visuelle est convaincante et le parcours de contact fonctionne dans les cas contrôlés. La priorité est de rendre la demande plus immédiate et l’offre plus concrète.** Une refonte graphique complète n’est pas justifiée. Les principaux investissements utiles sont la hiérarchie mobile, les preuves authentiques et les contenus métier validés.

Un défaut de mise en page est confirmé à 320 px : la FAQ provoque un défilement horizontal. Le formulaire commence trop bas sur mobile et tablette. Les contenus expliquent soigneusement comment préparer une demande, mais donnent encore peu de raisons concrètes de choisir Christophe Feret.

L’ouverture publique reste soumise aux validations déjà prévues dans le brief. La prévisualisation, ses avertissements et ses textes préparatoires ne doivent pas être assimilés à une version de production défectueuse.

## Objectifs de référence

Source : `docs/brief.md`, complétée par `README.md`, `docs/publication.md`, `docs/recette.md` et `docs/guide-christophe.md`.

| Objectif | Diagnostic | Priorité |
| --- | --- | --- |
| Obtenir des demandes de devis pertinentes | Champs adaptés et prestation présélectionnée depuis la fiche ; accès au premier champ trop tardif | Haute |
| Faciliter le contact direct avec Christophe Feret | Nom explicite, liens téléphone et alternative au formulaire présents | Conserver |
| Rassurer sur le métier et l’implantation | Métier et Écouen immédiatement identifiables ; preuves de réalisations absentes dans l’état observé | Haute, éditoriale |
| Qualifier la zone et les travaux acceptés | Prudence cohérente avec les faits non confirmés ; qualification encore reportée sur le contact | Haute, métier |
| Donner de l’autonomie à Christophe Feret | Trois rubriques et guide adaptés ; utilisabilité réelle de l’administration non vérifiée ici | À valider |
| Garder un site sobre, accessible et maintenable | Bonne base sémantique, composants simples ; défaut de reflow à 320 px | Correction ciblée |

## Périmètre et méthode

Lecture du thème, du formulaire, des règles de visibilité et des écrans métier ; inspection du rendu et des interactions dans le navigateur intégré. Les [Web Interface Guidelines](https://raw.githubusercontent.com/vercel-labs/web-interface-guidelines/main/command.md) ont servi de grille complémentaire pour les contrôles d’interface, sans transposer leurs conventions rédactionnelles anglaises au français.

**Exécuté pendant cet audit :**

- 24 contrôles structurels : accueil, liste des prestations, peinture intérieure, devis, entreprise et zone d’intervention, chacun à 360, 390, 768 et 1440 px. Un H1 et aucun débordement horizontal sur ces contrôles.
- Contrôle complémentaire à 320 px : accueil en débordement ; devis et peinture intérieure sans débordement.
- Inspection des trois autres fiches prestation, de la galerie vide, des mentions, de la confidentialité, de l’accès manuel au remerciement et d’une page inexistante à 768 px.
- Parcours peinture intérieure → « Présenter mon projet » → formulaire : présélection correcte de la prestation.
- Lien d’évitement au clavier : focus sur `#contenu`. Menu mobile : ouverture avec Entrée, tabulation vers Prestations, fermeture avec Échap et retour du focus sur Menu. Ouverture d’une réponse de FAQ.
- Soumission locale volontairement invalide, sans téléphone ni email : erreur explicite, saisie conservée, focus sur `#form-errors`. Aucun message envoyé à Christophe Feret.
- Inspection visuelle des premiers écrans ordinateur, mobile et tablette. Aucun message d’erreur dans le relevé console effectué en fin de parcours.

**Limites :** pas d’envoi valide effectué pendant cet audit, pas de test de livraison email réelle, pas d’administration authentifiée ni de publication de chantier, pas de lecteur d’écran, de téléphone physique ou de comparaison Safari/Firefox. Pas de Lighthouse ni d’audit automatisé complet d’accessibilité. La suite Playwright du dépôt n’a pas été lancée : les contrôles ci-dessus sont distincts. Les résultats d’intégration de `docs/recette.md` sont historiques, non réexécutés ici. Aucune certification WCAG/RGAA ni performance réelle de production n’est déduite de ces observations.

Les coordonnées, la date de création et les informations juridiques sont évaluées comme contenus du projet, sans nouvelle vérification administrative.

## Constats et corrections prioritaires

P1 : correction prioritaire ou contenu déterminant pour l’objectif commercial. P2 : amélioration utile. P3 : finition. Ces niveaux expriment une priorité de travail, pas un taux de perte de conversion mesuré.

### 1. P1 : Débordement horizontal de l’accueil à 320 px

**Constat observé.** Le document atteint 325 px pour une fenêtre de 320 px ; la zone utile hors barre verticale est d’environ 305 px. Les enfants de `.faq-grid` mesurent environ 307 px et atteignent l’abscisse 325. Une barre de défilement horizontale est visible.

**Cause identifiée dans le code.** Le titre `Quelques<br>réponses utiles.` n’a pas d’espace autour du saut de ligne. Sur mobile, `.faq-grid h2 br{display:none}` joint « Quelques » et « réponses ». La largeur minimale du contenu force alors la grille.

**Correction.** Conserver une séparation textuelle lors de la suppression du saut de ligne, rendre la colonne réductible et contrôler le retour des titres. Ne pas masquer le symptôme avec un débordement caché sur toute la page.

**Acceptation.** À 320, 360 et 390 px, FAQ ouverte et fermée, aucune barre horizontale ; titre lisible et aucune partie des questions coupée.

Références : `wp-content/themes/feret-peinture/front-page.php:13` ; `wp-content/themes/feret-peinture/style.css`, règles `.faq-grid` et `.faq-grid h2 br`.

### 2. P1 : Le premier champ du devis est repoussé sous l’introduction et le téléphone

**Constat observé.** Début du champ Nom à environ 905 px à 360 px de large, 899 px à 390 px, 897 px à 768 px et 753 px à 1440 px. À 390 × 844, aucun champ de saisie n’est visible dans le premier écran. Le bloc « Vous préférez appeler ? » précède le formulaire sur mobile et tablette.

**Impact probable.** Le visiteur qui vient de choisir « Demander un devis » doit encore faire défiler des informations déjà vues avant de commencer. Les avertissements de prévisualisation contribuent à la hauteur ; ces mesures ne sont pas une prédiction exacte de la future production. La priorité donnée au bloc téléphone subsistera néanmoins.

**Correction.** Réduire l’introduction de cette page ; présenter le formulaire en premier dans l’ordre de lecture mobile ; conserver une alternative téléphone compacte. Viser un début de saisie visible dans le premier écran de 390 × 844, à police normale, puis vérifier que le zoom reste utilisable.

Référence : `wp-content/themes/feret-peinture/page.php:5` ; `style.css`, `.quote-layout`, `.quote-aside`, `.page-intro`.

### 3. P1 éditorial : L’identité graphique ne remplace pas les preuves de travail

**Constat observé.** Aucun chantier affiché, pas de photo métier ni de portrait dans les pages inspectées ; la galerie locale indique son état vide. L’accueil repose sur une composition de nuanciers et un grand monogramme. L’absence de faux chantier respecte parfaitement le brief.

**Impact probable.** Le visiteur comprend le métier, mais ne peut pas apprécier les finitions ni reconnaître un chantier comparable au sien. Le monogramme occupe une place importante sans apporter cette preuve.

**Correction.** Obtenir quelques réalisations réelles avec autorisation, commune, nature du travail et photos exploitables. Une ou deux fiches solides valent mieux qu’une galerie remplie artificiellement. Ajouter éventuellement un portrait autorisé ; conserver le hero abstrait si aucun visuel métier pertinent n’est disponible.

**Acceptation.** Depuis une preuve de l’accueil, on peut consulter une fiche informative et demander un devis ; seules les photos autorisées sont publiques. À tester après ajout de contenus réels.

Références : `wp-content/themes/feret-peinture/front-page.php:9` et `:10` ; `single-fp_project.php` ; `archive-fp_project.php`.

### 4. P1 éditorial : Les prestations restent surtout des instructions de prise de contact

**Constat observé.** Les quatre fiches détaillent les informations à fournir. La peinture extérieure demande de préciser les surfaces sans indiquer clairement quels éléments Christophe Feret accepte effectivement de peindre. Les sols restent formulés de manière large. La préparation, la protection et les finitions sont presque uniquement présentées comme des points à confirmer.

**Impact probable.** Le site prépare des messages, mais aide moins à décider si l’entreprise correspond au besoin. Il peut attirer des demandes hors périmètre.

**Correction.** Faire valider, pour chaque famille, les travaux acceptés, les exclusions utiles et la méthode réellement pratiquée. Commencer la fiche par « ce que Christophe Feret réalise », puis conserver les conseils pour le devis. Ne pas ajouter de promesses de gratuité, délai, garantie ou qualification sans validation.

**Acceptation.** Un lecteur peut déterminer en moins d’une minute si son besoin appartient à la prestation, sans devoir appeler pour comprendre le périmètre de base.

Référence : `config/pods/services.php`. Les contenus sont initialisés une seule fois : corriger les fiches WordPress existantes, pas seulement leur fichier d’initialisation.

### 5. P2 : Les prestations sont tardives dans la lecture mobile

**Constat observé.** À 390 px, la section Prestations commence vers 1 118 px et l’accueil mesure environ 5 781 px dans son état actuel. Après le texte d’introduction vient une illustration haute ; les prestations arrivent ensuite. Les deux contacts principaux sont toutefois accessibles avant l’illustration, ce qui est positif.

**Correction.** Réduire la hauteur de l’illustration sur mobile, condenser les répétitions « contact direct / préparer la demande / implantation », et annoncer les familles de travaux plus tôt. Ne pas raccourcir au détriment des explications nécessaires.

**Acceptation.** Le métier, la ville, les familles de travaux et une action de contact sont identifiables rapidement ; vérifier le nouveau parcours avec quelques personnes représentatives.

Référence : `wp-content/themes/feret-peinture/front-page.php:2` et sections suivantes ; `style.css`, `.hero-art`, `.art-frame`, `.section`.

### 6. P2 : « Préciser le lieu du chantier » ouvre une page informative

**Constat observé.** Le lien de l’accueil mène à `/zone-intervention/`, sans champ de commune sur cette page. Un deuxième clic est nécessaire pour rejoindre le devis. Aucune liste de communes confirmées n’est affichée actuellement.

**Correction.** Renommer le lien « Voir la zone d’intervention » si sa destination reste informative ; sinon mener directement au formulaire. Renseigner ensuite les communes réellement acceptées et garder une possibilité de questionner Christophe Feret pour les autres lieux.

**Acceptation.** Le libellé annonce correctement l’action et aucune couverture géographique n’est inventée.

Référence : `wp-content/themes/feret-peinture/front-page.php:12` ; `page.php`, branche `zone-intervention`.

### 7. P2 : Tous les boutons d’une fiche prestation ne conservent pas le contexte

**Constat vérifié par parcours et code.** « Présenter mon projet » transmet correctement `?prestation=peinture-interieure`. Le bandeau final « Demander un devis » appelle le bouton sans prestation ; son formulaire redemande donc un choix déjà implicite.

**Correction.** Transmettre la prestation courante depuis les CTA de cette fiche, tout en permettant de modifier le choix dans le formulaire.

**Acceptation.** Depuis le bouton latéral comme depuis le bandeau final, la bonne prestation est sélectionnée. Une demande mixte reste possible.

Références : `wp-content/themes/feret-peinture/single-fp_service.php:4` et `:5` ; `functions.php`, `fp_theme_contact_band()`.

### 8. P2 : La règle « téléphone ou email » n’est pas reliée aux deux champs

**Constat observé et code.** La règle existe dans un paragraphe général ; les champs n’y sont pas reliés par `aria-describedby`. Quand les deux sont vides, seule l’entrée téléphone reçoit l’erreur. L’erreur est explicite et le focus sur son résumé fonctionne : il faut préserver ces acquis.

**Correction.** Regrouper les deux moyens de contact sous une instruction commune, relier celle-ci aux deux champs et exprimer l’erreur au niveau du groupe. Ne pas rendre les deux champs obligatoires.

**Acceptation.** Un téléphone seul ou un email seul suffit ; au clavier et au lecteur d’écran, la règle est accessible au moment de saisir les coordonnées.

Référence : `wp-content/plugins/feret-peinture-core/includes/form.php:227` et définition des champs immédiatement après.

### 9. P2 : Aucun état d’envoi propre au formulaire

**Constat statique.** Le script gère le menu, les événements et le focus d’erreur, mais aucun état après soumission valide. Le serveur prévoit jusqu’à dix secondes d’attente pour le transport email et protège les doubles traitements.

**Risque non reproduit sur réseau lent.** Le visiteur peut cliquer plusieurs fois faute de retour local suffisamment explicite.

**Correction.** Après validation navigateur, afficher « Envoi en cours… », annoncer l’état et empêcher une seconde soumission pendant la requête. Conserver le fonctionnement sans JavaScript et le retour exploitable en cas d’échec.

**Acceptation.** Sous latence simulée, l’état est compréhensible ; un échec restaure un formulaire utilisable avec sa saisie.

Références : `wp-content/themes/feret-peinture/assets/site.js:29` ; `wp-content/plugins/feret-peinture-core/includes/form.php`.

### 10. P2 : La confirmation parle du système d’envoi et invite à revérifier

**Constat statique, branche de succès non exécutée.** Le texte est « Votre demande a été transmise au service d’envoi », suivi d’une invitation à téléphoner pour vérifier la réception. C’est prudent techniquement, mais peu naturel pour un particulier et susceptible de susciter des appels de vérification.

**Correction.** Après validation de la délivrabilité, employer une confirmation simple correspondant exactement au statut connu, par exemple « Merci. Votre demande a été envoyée. » Puis indiquer la suite réellement convenue avec Christophe Feret, sans délai inventé ni affirmation de lecture du message. Le téléphone reste une alternative, pas une étape imposée.

Référence : `wp-content/themes/feret-peinture/page.php:14`.

### 11. P3 : Deux entrées de navigation conduisent au même endroit

« Contact » et « Devis » mènent toutes deux à `/devis/`. Ce n’est pas une impasse, mais cela occupe de la place et suggère deux parcours distincts. Conserver un bouton « Demander un devis » et, si utile, un accès direct à l’appel. Le menu actif ne reconnaît par ailleurs pas les fiches individuelles comme appartenant aux prestations : conserver un repère de rubrique approprié sans annoncer faussement l’archive comme la page courante.

Références : `wp-content/themes/feret-peinture/header.php:11` ; `functions.php:40` et `:46`.

### 12. P3 : Présentation des liens partagés incomplète

Le thème fournit un titre et une description Open Graph, mais ne définit pas d’image de partage. Le rendu dans les messageries n’a pas été testé. Prévoir une image sobre et reconnaissable avec le nom et l’activité, puis vérifier un aperçu réel après publication autorisée.

Référence : `wp-content/themes/feret-peinture/functions.php:106`.

## Qualité UI et accessibilité : acquis à conserver

La palette crème, terre cuite et bleu-vert, les titres sérif et la composition de nuanciers donnent une identité cohérente avec le brief. Le titre nomme immédiatement l’activité et la commune. Les boutons restent simples, distincts et explicites. Les grands espacements sont agréables sur ordinateur ; leur coût en hauteur mérite surtout une adaptation sur les pages de conversion.

Contrastes recalculés sur les couleurs actuelles du code, sans prétendre couvrir toutes les superpositions ou tous les états :

| Couple | Rapport |
| --- | --- |
| Texte principal `#282e2d` / fond `#f6f3ec` | 12,48:1 |
| Texte secondaire `#5c6668` / fond `#f6f3ec` | 5,33:1 |
| Texte `#fffefb` / bouton `#9f3f2e` | 6,46:1 |
| Texte bleu-vert `#155e69` / fond `#f6f3ec` | 6,68:1 |
| Bordure de champ `#71888b` / intérieur `#fffefb` | 3,72:1 |

Les champs mobiles inspectés sont à 16 px. Labels explicites, vrais liens et boutons, FAQ native, styles de focus et réduction du mouvement sont prévus. Le menu reste visible sans la classe JavaScript selon le CSS. Il n’y a pas de barre fixe recouvrant le formulaire. Le faux succès par accès manuel à `/merci/?sent=1` n’apparaît pas.

À compléter : contrôle de tous les états au lecteur d’écran, zoom réel, reflow étendu, focus des liens sur chaque fond, messages dynamiques, contenus longs, images réelles et textes alternatifs. Les tests à largeur réduite ne remplacent pas un test de zoom sur navigateur et téléphone réels.

## Autonomie de Christophe Feret

La séparation « Mes chantiers / Mes prestations / Mes informations », les libellés métier, les notices et le guide constituent une bonne base. La gestion des droits photographiques et l’absence d’accès aux réglages techniques répondent au besoin.

Cette partie est une revue du code et du guide, pas une recette de l’interface connectée. Avant validation, faire réaliser à Christophe Feret un scénario complet : ajouter une photo depuis son téléphone, décrire le chantier, choisir la prestation, prévisualiser, publier, modifier puis repasser en brouillon. Vérifier qu’il comprend pourquoi une réalisation peut rester invisible ; contrôler le réordonnancement des photos au clavier et au tactile, les erreurs d’import et la récupération d’un brouillon. Le HEIC n’étant pas garanti, tester ses photos habituelles plutôt que présumer un parcours fluide.

## Performance, acquisition et mesure

Polices locales, script limité et absence de service analytique externe sont cohérents avec la sobriété attendue. Aucun score de performance n’a été mesuré : les objectifs historiques de Lighthouse restent à vérifier, surtout après ajout de vraies photos. Ne pas déduire une rapidité de production du serveur local.

L’implantation, les titres descriptifs et les pages de prestations forment une base de découverte locale cohérente. Le noindex de prévisualisation est intentionnel. La qualité des demandes dépendra surtout de la clarté des prestations et de la zone validée.

Les événements locaux existants ne constituent pas un tableau de bord persistant. Pour évaluer les changements, commencer par suivre le nombre de demandes reçues et la proportion de demandes pertinentes, hors tests et spam. Distinguer un clic téléphone d’un appel abouti, et un envoi accepté d’un email livré. Un éventuel suivi analytique nécessite une décision séparée ; aucun outil externe n’a été branché.

## Plan d’action proposé

| Ordre | Travail | Responsable proposé | Validation |
| --- | --- | --- | --- |
| 1 | Corriger la FAQ à 320 px et ajouter ce cas à la recette | Aliant | Aucun débordement, titre correctement séparé |
| 2 | Rapprocher le formulaire du début de page ; compacter le mobile | Aliant | Premier champ visible plus tôt, clavier et zoom utilisables |
| 3 | Unifier la présélection des CTA, le groupe de coordonnées et l’état d’envoi | Aliant | Parcours cohérent, saisie conservée en erreur |
| 4 | Valider offre, zone et quelques preuves authentiques | Christophe Feret avec Aliant | Contenus concrets, droits confirmés, aucune promesse ajoutée sans accord |
| 5 | Clarifier confirmation, navigation et lien de zone | Aliant avec Christophe Feret | Libellés conformes aux actions et suite du contact compréhensible |
| 6 | Réaliser la recette d’administration et les tests finaux | Christophe Feret et Aliant | Scénario autonome, accessibilité et performances vérifiées |
| 7 | Lever les conditions d’ouverture déjà documentées | Responsables du brief | Coordonnées, textes définitifs, livraison email et publication validés |

## Pièces de contrôle

- [Mesures des 24 contrôles responsive](audit-ui-ux/mesures-responsive.json).
- Captures ci-dessous, réalisées pendant cet audit. Les dimensions indiquent la fenêtre simulée, pas un appareil physique.

### Accueil : 1440 × 900

*Capture conservée localement, exclue du dépôt public car elle peut contenir des coordonnées non validées.*

### Accueil : 390 × 844

*Capture conservée localement, exclue du dépôt public car elle peut contenir des coordonnées non validées.*

### Devis : 390 × 844

*Capture conservée localement, exclue du dépôt public car elle peut contenir des coordonnées non validées.*

### FAQ : 320 × 800

*Capture conservée localement, exclue du dépôt public car elle peut contenir des coordonnées non validées.*

Le livrable ajoute uniquement cet audit, ses captures et ses mesures. Aucun fichier fonctionnel du site n’a été modifié.
