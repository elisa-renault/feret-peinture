# Feret Peinture : première version WordPress

Les [corrections de l’audit UI / UX](docs/corrections-ui-ux-2026-09-16.md) sont appliquées à la version locale : parcours devis, responsive à 320 px, navigation et retours d’envoi. Ce compte rendu contient les captures et la portée des vérifications récentes.

Site vitrine français pour Christophe Feret à Écouen : thème classique sur mesure, Pods gratuit et petit plugin métier. Le parcours principal mène à une demande de rendez-vous sur place avant devis ; les appels disposent de liens `tel:`. Aucun service analytique externe n’est activé.

**Site ouvert au public le 16 septembre 2026.** [feret-peinture.fr](https://feret-peinture.fr) est accessible sans mot de passe, en HTTPS, avec formulaire actif et indexation autorisée. Voir [le suivi de publication](docs/publication.md) pour les contrôles et la sauvegarde de bascule.

## Démarrer

Prérequis : Docker avec Compose v2, un shell POSIX et OpenSSL. Sur Windows, utiliser Docker Desktop avec WSL 2 et exécuter ces commandes dans le terminal WSL à la racine du projet.

```sh
sh scripts/init-env.sh
docker compose up -d --wait
docker compose run --rm --entrypoint sh wpcli /project/scripts/install.sh
```

- Site : <http://localhost:8080>
- Administration : <http://localhost:8080/wp-admin/>
- Emails de test : <http://localhost:8025>

Le fichier privé `.env` contient les mots de passe générés pour `aliant` et `christophe`. Ne pas le committer ni le partager. Les comptes et destinataires locaux utilisent le domaine réservé `.test`. Les ports ne sont publiés que sur l’ordinateur local.

Relancer l’installation conserve les contenus, les réglages éditoriaux et les comptes déjà présents. Arrêter avec `docker compose down` conserve les volumes. Ne pas ajouter `--volumes` : cette option détruirait les données locales.

## Ce que Christophe Feret peut modifier

Son compte ouvre « Mes chantiers ». Il dispose de trois rubriques :

| Rubrique | Actions |
| --- | --- |
| Mes chantiers | Créer, modifier, publier, mettre à la corbeille et récupérer une fiche ; commune, prestation, description, photo principale, galerie ordonnée, avant/après facultatif, mise en avant et autorisation des photos. |
| Mes prestations | Ajouter, modifier ou supprimer une prestation, avec deux blocs sur mesure verrouillés pour le texte et une image facultative. |
| Mes informations | Une page dédiée pour modifier les téléphones, l’email public, la présentation et la zone d’intervention. Les changements alimentent les pages et les liens. |

Le rôle n’a pas `manage_options`, ni accès aux extensions, thèmes, utilisateurs, pages légales ou réglages SMTP. Les autorisations sont vérifiées côté serveur. Le [guide Christophe Feret](docs/guide-christophe.md) explique brouillons, photos, aperçu et récupération.

Les réalisations n’apparaissent que lorsqu’elles possèdent une photo principale et une autorisation de publication. Une galerie vide est masquée. Aucun chantier fictif n’est installé par défaut. Pour créer volontairement un brouillon de démonstration local :

```sh
docker compose run --rm -e FP_IMPORT_DEMO=1 wpcli wp eval-file /project/scripts/bootstrap.php
```

## Vérifier

Trois [chantiers d’exemple avec photos stock](docs/chantiers-exemples.md) ont été ajoutés volontairement à la prévisualisation le 16 septembre 2026. Ils utilisent les fiches existantes et sont exclus de la production.

Ces suites utilisent uniquement une **base locale jetable** et des données fictives. Les tests d’intégration créent puis nettoient leurs propres fixtures. Le test du formulaire dépose des messages dans Mailpit.

```sh
docker compose run --rm --entrypoint sh wpcli /project/scripts/lint.sh
docker compose run --rm -e FP_RUN_DESTRUCTIVE_TESTS=1 wpcli wp eval-file /project/tests/integration.php
docker compose run --rm -e FP_RUN_DESTRUCTIVE_TESTS=1 -e WP_ENVIRONMENT_TYPE=production wpcli wp eval-file /project/tests/integration.php
docker compose run --rm wpcli wp eval-file /project/tests/form-integration.php
docker compose run --rm -e FP_RUN_DESTRUCTIVE_TESTS=1 -e WP_ENVIRONMENT_TYPE=production wpcli wp eval-file /project/tests/publication-integration.php
python3 tests/http-smoke.py
python3 tests/admin-http.py --env-file .env
```

Le test CLI en mode `production` vérifie les garde-fous sur la base locale ; il ne déploie rien et ne change pas le serveur web local. Ne jamais lancer les suites d’intégration sur une base client.

Les conditions de mesure et les contrôles visuels restant à exécuter figurent dans [docs/recette.md](docs/recette.md). Aucun score Lighthouse n’est présumé.

Pour lancer ensuite la recette navigateur dans votre environnement local (Node requis uniquement pour ces tests) :

```sh
npm ci
npx playwright install chromium
npm run test:browser
```

Cette suite prépare les captures à 360, 390, 768 et 1440 px dans `test-results/`. Elle a été fournie mais n’a pas pu être exécutée pendant cette livraison.

## Architecture

- `wp-content/themes/feret-peinture/` : présentation, navigation, styles, polices locales, pages, titres et descriptions.
- `wp-content/plugins/feret-peinture-core/` : contenus, permissions, révisions, publication, JSON-LD et traitement du formulaire.
- `config/pods/` : types et champs Pods versionnés, contenus initiaux des prestations. À déployer aussi à la racine de WordPress.
- `scripts/` : installation, bootstrap idempotent et commandes locales.
- `tests/` : recette exécutable ; `.github/workflows/ci.yml` : mêmes contrôles dans GitHub Actions.

Le moteur WordPress, Pods, la base et les médias ne sont pas commités. Pas de constructeur de pages ni de licence premium. Les visuels du hero sont des compositions graphiques originales de nuanciers, sans faux chantier. Sources et licences : [docs/resources.md](docs/resources.md).

## Formulaire et configuration privée

Le formulaire accepte un téléphone ou un email, sans pièce jointe. Validation serveur, nonce, jeton signé limité dans le temps, champ antispam et quota atomique de 5 tentatives par 15 minutes sont appliqués. Les compteurs ne stockent qu’une empreinte salée de l’adresse réseau, jamais le message ; ils expirent et sont nettoyés par WP-Cron. Configurer un cron système en exploitation.

Un succès correspond à l’acceptation par le transport SMTP. Il ne certifie pas la présence dans la boîte de réception. Une erreur reste affichée avec la saisie. Sans configuration d’envoi, le formulaire est désactivé. Un retour manuel sur la page de remerciement ne produit pas de succès.

| Paramètre privé | Usage |
| --- | --- |
| `FP_QUOTE_TO`, `FP_MAIL_FROM` | Destinataire consulté et expéditeur autorisé. |
| `FP_SMTP_HOST`, `FP_SMTP_PORT`, `FP_SMTP_SECURE` | Transport SMTP ; `tls` ou `ssl` selon le fournisseur. |
| `FP_SMTP_USER`, `FP_SMTP_PASS` | Identifiants SMTP, hors dépôt et hors interface Christophe Feret. |
| `FP_CONTACT_APPROVED`, `FP_SERVICES_APPROVED` | Validation des coordonnées et prestations. |
| `FP_LEGAL_APPROVED`, `FP_PRIVACY_APPROVED` | Validation des informations légales et du traitement des données. |
| `FP_PRIVACY_RETENTION` | Texte approuvé précisant la durée de conservation et son point de départ. |
| `FP_MAIL_DELIVERY_VERIFIED` | Test de livraison réelle effectué volontairement par Aliant. |
| `FP_LAUNCH_APPROVED` | Autorisation finale ; ne remplace pas les autres contrôles. |
| `FP_BUSINESS_ADDRESS_JSON` | Adresse administrative vérifiée, également visible dans les mentions ; objet JSON PostalAddress sans coordonnées GPS inventées. |

Les paramètres peuvent être des constantes dans `wp-config.php` privé ou des variables d’environnement du service PHP. Les indicateurs acceptent un booléen ou `1`. Les pages légales doivent aussi être publiées, remplies et marquées `_fp_legal_approved=1` par Aliant ; les cases seules ne rendent pas une page vide publiable. Le Compose fourni reste volontairement local.

## Avant publication

Suivre [docs/publication.md](docs/publication.md) et [docs/maintenance.md](docs/maintenance.md) : validations factuelles, mentions complètes, SMTP et livraison, recette visuelle, staging protégé, HTTPS sans www. La décision actuelle ne prévoit pas de sauvegarde automatique : GitHub ne couvre que le code et les brouillons conservés localement ne couvrent pas les contenus déjà publiés. L’hébergement final doit exécuter PHP/MariaDB ; GitHub Pages ne convient pas.

Après autorisation et validation, régler les URL publiques et `wp option update blog_public 1`. Le staging conserve son authentification et son noindex ; la page de remerciement reste non indexable. Aucun branchement aux comptes Twenty/PostHog d’Aliant n’est prévu.

## Dépôt public

Le dépôt contient le thème, le plugin, les scripts, les tests et la documentation à jour. Un historique public neuf est utilisé : l’ancienne archive de livraison reste privée et ne doit pas être importée ni poussée, car elle contient des coordonnées non validées.

Les fichiers `.env`, `LIRE-AVANT.txt`, les archives Git, les captures historiques, la base et les médias sont exclus. Ne pas publier une archive brute du dossier local. Pour distribuer les seuls fichiers versionnés, utiliser `git archive`.

Les téléphones sont vides à la première installation. Le mobile renseigné est affiché pour prendre rendez-vous ; le fixe reste privé. Renseigner l’email public approuvé dans « Mes informations » et compléter les mentions légales dans WordPress. Relancer l’installation conserve les données déjà saisies ; leur validation reste nécessaire sur les installations existantes.

La publication du code sur GitHub est distincte de l’ouverture du site : les conditions de [publication](docs/publication.md) restent applicables. Aucun dépôt distant n’est configuré par ces scripts.

Code : GPL-2.0-or-later. Polices : SIL Open Font License 1.1.
