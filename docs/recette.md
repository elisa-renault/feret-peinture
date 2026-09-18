# Recette de la V1

**Complément du 16 septembre 2026 :** les corrections issues de l’audit UI / UX et leur nouvelle recette locale sont détaillées dans [Corrections UI / UX](corrections-ui-ux-2026-09-16.md). Ce complément apporte notamment 36 contrôles responsive en navigateur, des captures et 31 assertions de formulaire réussies. Les résultats ci-dessous décrivent la livraison initiale.

Réalisation du 16 septembre 2026, heure de Paris. Le site a été exécuté dans un WordPress réel, avec une base MariaDB et Mailpit. Aucun hébergement public, DNS ou compte métier externe n’a été modifié. Les données de test sont synthétiques.

## Environnement réellement exécuté

| Composant | Recette native isolée | Environnement Compose fourni |
| --- | --- | --- |
| WordPress | 7.1 | 7.1, image PHP 8.3 Apache |
| Pods | 3.3.9.2 | 3.3.9.2 |
| PHP | 8.3.6, paquet Ubuntu avec correctifs ; GD | Branche 8.3 de l’image WordPress |
| MariaDB | 10.11.14 | 10.11.19 |
| WP-CLI | 2.12.0 | 2.12.0 |
| Mailpit | 1.31.1 | 1.31.1 |
| Langue | WordPress et Pods en français | Installation des traductions par le script |

Docker n’est pas disponible dans cette session. Ses fichiers et scripts ont été contrôlés statiquement ; les images n’ont pas été lancées ici. Les services natifs ont été démarrés ensemble dans le même environnement réseau isolé. Ces résultats ne constituent pas une certification du futur hébergeur.

## Résultats exécutés

| Suite | Résultat | Portée |
| --- | --- | --- |
| `tests/integration.php`, environnement local | 117 assertions réussies | Types et champs Pods, capacités, REST, contenus, révisions, médias, corbeille, bootstrap. |
| Même suite, mode production sur base jetable | 119 assertions réussies | Garde-fous de publication, exclusion des fixtures et des chantiers non autorisés. |
| `tests/form-integration.php` | 27 assertions réussies | Validation, nonce, jeton signé, quota, SMTP réel vers Mailpit, panne SMTP, absence de configuration, conservation de saisie. |
| `tests/publication-integration.php` | 12 assertions réussies | Configuration approuvée simulée : JSON-LD unique et parsable, contacts cohérents, origine de production, adresse visible, indexation et sitemap. |
| `tests/http-smoke.py` | 107 contrôles réussis | Douze pages, titres et descriptions uniques, canonicals, noindex local, 404, liens téléphone, vrais POST invalides/spam/valide et remerciement signé. |
| `tests/admin-http.py` | 32 contrôles HTTP natifs réussis | Connexion Christophe Feret, neuf refus d’accès direct, trois rubriques, coordonnées, upload PNG, brouillon, publication, modification, corbeille et restauration. |

Les commandes de reproduction sont dans le README et les scripts de test. Les vérifications de l’administration utilisent les formulaires, cookies et nonces réels de WordPress ; les valeurs des champs Pods initialisés par JavaScript sont lues dans les données JSON produites par Pods. Ce contrôle HTTP ne remplace pas une vérification visuelle du formulaire hydraté dans un navigateur.

Les suites doivent être lancées sur une base jetable. Le quota est partagé par les requêtes provenant de la même adresse : plusieurs passages HTTP consécutifs en moins de quinze minutes peuvent volontairement bloquer le dernier envoi. Repartir d’une base de recette propre ou attendre la fenêtre ; aucun endpoint de remise à zéro public n’est fourni.

## Ce qui a été vérifié précisément

- Le rôle Christophe Feret ne reçoit pas `manage_options`, ni les capacités de gérer utilisateurs, extensions, thèmes, pages ou articles WordPress génériques. Les réglages et secrets SMTP ne font pas partie de ses champs.
- Les changements de coordonnées alimentent le téléphone affiché et l’URI `tel:`. Le shortcode `[fp_contact_details]` évite de recopier manuellement les coordonnées dans les pages juridiques.
- Une révision WordPress restaure réellement le titre, le texte, les champs simples Pods, la prestation associée, la photo principale, les deux photos avant/après, les identifiants et l’ordre de la galerie. La corbeille conserve ces relations. Les fichiers effacés de la médiathèque nécessitent la sauvegarde des médias : une révision ne les recrée pas.
- Un JPEG de 3 200 × 1 600 pixels est réduit à 2 200 × 1 100 ; les formats dérivés sont générés. Un JPEG portant une orientation EXIF 6 est effectivement tourné de 90 × 60 à 60 × 90. Ces tests portent sur GD installé ; aucun support HEIC/AVIF n’est annoncé.
- Le bootstrap répété ne duplique pas les contenus et conserve les textes, coordonnées et galeries modifiés. Les exemples de WordPress ne sont supprimés que pendant une nouvelle installation créée par le script, jamais lors de l’adoption d’un site existant.
- Les demandes sont acceptées avec seulement un email ou seulement un téléphone. Aucun fichier public n’est accepté. Les échecs ne produisent pas de faux succès ; un accès manuel à `/merci/?sent=1` ne confirme aucun envoi.
- Les emails de recette ont été réellement remis au transport SMTP local. Cela ne prouve pas une livraison chez un fournisseur réel : Aliant doit réaliser ce test avant ouverture.
- Sans les validations privées et des pages légales remplies et approuvées, le mode production reste fermé. Les fixtures ne deviennent pas des réalisations publiques.

## Défauts détectés puis corrigés

La recette a notamment corrigé la restauration de la relation Pods vers une prestation, l’exposition d’un endpoint Pods au rôle métier, la validation trop tardive de l’expéditeur email local et la collision du champ public `name` avec une variable de routage réservée de WordPress. Les noms HTML du formulaire sont maintenant préfixés `fp_`.

L’accès natif aux prestations et informations a également été corrigé : WordPress supprimait leur sous-menu unique puis évaluait l’accès comme celui des articles classiques. Un lien utile « Voir sur le site » conserve le rattachement natif des écrans, sans ajouter de capacité au rôle Christophe Feret. Les neuf écrans interdits renvoient toujours HTTP 403. Le téléphone modifié pour le test, le chantier et l’image de recette ont été restaurés ou nettoyés après le passage réussi.

L’URL de l’installation native avait conservé `/wordpress` : ses options `home` et `siteurl` ont été corrigées pour la recette. Les douze pages produisent ensuite les canonicals attendues. Le script Compose installe explicitement l’URL locale fournie.

Pods émet un avertissement de chargement anticipé des traductions dans son initialisation **CLI**. Il a été tracé à la dépendance, sans modifier cette dernière ; il n’est pas présent dans les pages HTTP contrôlées et n’empêche pas les tests. Les diagnostics PHP restent masqués dans les pages publiques.

## Présentation et accessibilité : portée réelle

La palette a été calculée : contraste texte principal 12,48:1 ; CTA 6,46:1 ; texte secondaire 5,50:1 ; bordures de champs 3,56:1. Les modèles prévoient focus visible, lien d’évitement, navigation au clavier, préférences de mouvement réduit et mise en page responsive. Il n’y a pas de barre fixe recouvrant les champs.

**Aucune recette visuelle exécutée ni capture disponible dans cette session.** Le navigateur disponible ne peut pas joindre le serveur isolé et sa politique de sécurité a refusé l’ouverture des fichiers locaux. Aucun autre moyen de contrôle du navigateur n’a été utilisé pour contourner ce refus.

`tests/browser.spec.mjs` est fourni, mais non exécuté : parcours accueil → prestation → devis, menu et clavier, absence de débordement à 360/390/768/1440 px, événements sans données personnelles et captures. Il doit être lancé dans l’environnement local avant validation graphique. Les styles et scripts ont passé leurs vérifications syntaxiques.

**Aucun score Lighthouse mesuré.** Les objectifs performance ≥ 90 et accessibilité automatique ≥ 95 restent à vérifier. Les calculs de contraste, la présence d’attributs et les tests HTTP ne prouvent pas une conformité globale WCAG 2.2 AA.

La restauration complète d’une sauvegarde sur un hébergeur, les certificats HTTPS, l’authentification du staging, les redirections DNS, l’outil Google de validation et la délivrabilité réelle ne sont pas exécutés ici. Les procédures et conditions figurent dans `maintenance.md` et `publication.md`.
