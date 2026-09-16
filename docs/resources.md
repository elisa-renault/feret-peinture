# Sources, dépendances et licences

Contrôles effectués pendant la réalisation du 16 septembre 2026 en heure de Paris (15 septembre 2026, fin de soirée UTC). Les liens externes restent des sources, pas des composants chargés par le site public. Une source inaccessible est signalée ; aucune photo d'annuaire n'a été réutilisée.

## Code et identité visuelle

Le code original du thème et du plugin, les compositions CSS/SVG originales et le favicon sont livrés sous **GPL-2.0-or-later**, voir [`LICENSE`](../LICENSE). Le nom d'usage « Feret Peinture » n'est pas présenté comme une marque déposée. Les dépendances conservent leur propre licence ; la licence du code n'autorise pas la réutilisation de futures photographies de clients sans leur accord.

| Ressource | Origine / statut | Utilisation |
| --- | --- | --- |
| Nom composé Feret Peinture | Identité typographique provisoire réalisée pour le projet | En-tête et pied de page ; aucun logo historique prétendu. |
| Favicon SVG | Dessin vectoriel original du thème | Repère graphique, sans badge de qualification. |
| Hero de nuancier / matières | Composition graphique originale CSS/SVG | Illustration abstraite ; ne représente ni un chantier ni Christophe. |
| Textes du site | Rédaction originale depuis le brief et les faits référencés | Texte de prévisualisation jusqu'aux validations métier ; aucun lorem ipsum. |
| Photos professionnelles | Aucune photo authentifiée fournie pour cette V1 | Aucune fausse réalisation en production. |
| Fixtures éventuelles | Échantillons explicitement marqués, import facultatif de développement | Test technique uniquement ; exclusion du rendu public de production. |

## Polices locales

Deux familles sont servies localement, sans appel à Google Fonts au chargement du site. Les fichiers WOFF2 livrés sont versionnés dans `wp-content/themes/feret-peinture/assets/fonts/`, avec `font-display: swap` et alternatives système Georgia / Arial. Ils totalisent **241 216 octets**. Conserver les fichiers OFL lors d’une redistribution.

| Police / fichier livré | Auteurs, transformation et licence embarquée | Taille | SHA-256 du fichier livré |
| --- | --- | --- | --- |
| Newsreader : `assets/fonts/newsreader.woff2` | Newsreader Project Authors / Production Type ; sous-ensemble converti en WOFF2 ; `OFL-Newsreader.txt`, SIL OFL 1.1 | 175 548 octets | `36491e9d777a0c176acfc6f529685beea26d7296ac68d084c5e38e4d460bdf7f` |
| Feret Sans : `assets/fonts/feret-sans.woff2` | Dérivé de Source Sans 3 par Adobe ; sous-ensemble converti en WOFF2 et renommé ; `OFL-Source-Sans-3.txt`, SIL OFL 1.1 | 65 668 octets | `b58ccad46210eb7a9f43ea97045f3a83470fd933986e43d22f1c8534e3685ea0` |

Les fichiers amont ont été téléchargés le 15 septembre 2026 UTC depuis le dépôt Google Fonts :

- Newsreader : [fichier source variable](https://raw.githubusercontent.com/google/fonts/main/ofl/newsreader/Newsreader%5Bopsz,wght%5D.ttf) et [licence exacte](https://raw.githubusercontent.com/google/fonts/main/ofl/newsreader/OFL.txt).
- Source Sans 3 : [fichier source variable](https://raw.githubusercontent.com/google/fonts/main/ofl/sourcesans3/SourceSans3%5Bwght%5D.ttf) et [licence exacte](https://raw.githubusercontent.com/google/fonts/main/ofl/sourcesans3/OFL.txt).

La conversion avec fontTools et Brotli conserve les axes variables et un sous-ensemble couvrant U+0020–U+024F, U+2000–U+206F, le symbole euro et les flèches gauche/droite. Les accents français et apostrophes typographiques sont inclus. Les fichiers TTF amont ne sont pas livrés ; les empreintes du tableau identifient les WOFF2 finaux, pas les téléchargements originaux.

La licence Adobe réserve le nom **Source**. Le dérivé modifié porte donc le nom **Feret Sans** dans les métadonnées de famille, les identifiants de police concernés et le CSS ; les copyrights et le texte de licence Adobe sont conservés. Newsreader conserve son nom. Les polices ne sont pas vendues séparément et aucune licence premium n’est requise. Voir aussi [`assets/fonts/README.md`](../wp-content/themes/feret-peinture/assets/fonts/README.md).

Projets auteurs : [Newsreader](https://github.com/productiontype/Newsreader) et [Source Sans](https://github.com/adobe-fonts/source-sans).

## Logiciels et versions de référence

Les tags effectifs sont ceux de `compose.yaml`. L'environnement de recette natif éventuel n'est pas assimilé à une exécution Docker ; les écarts constatés figurent dans [recette.md](recette.md).

| Composant | Version fixée pour l'environnement local | Source primaire de vérification |
| --- | --- | --- |
| WordPress | 7.1 ; image `wordpress:7.1.0-php8.3-apache` | [Archives officielles](https://wordpress.org/download/releases/) ; [registre des images officielles](https://raw.githubusercontent.com/docker-library/official-images/master/library/wordpress). |
| WP-CLI | Image `wordpress:cli-2.12.0-php8.3` | [Notes de version 2.12.0](https://make.wordpress.org/cli/2025/05/07/wp-cli-v2-12-0-release-notes/) ; registre officiel ci-dessus. |
| Pods | 3.3.9.2 | [Fiche de l'extension](https://wordpress.org/plugins/pods/) ; définitions locales dans `config/pods`. |
| MariaDB | 10.11.19, variante fixée dans Compose | [Registre officiel des images MariaDB](https://raw.githubusercontent.com/docker-library/official-images/master/library/mariadb). |
| Mailpit | v1.31.1 | [Publication officielle](https://github.com/axllent/mailpit/releases/tag/v1.31.1). |
| PHP de l'image locale | Branche 8.3 | [Calendrier officiel de maintenance PHP](https://www.php.net/supported-versions.php). Le tag de l'image fixe la branche ; les correctifs de l'image peuvent évoluer. |
| Playwright, uniquement pour la recette facultative Node | `@playwright/test` 1.63.0 et fichier lock | Version vérifiée auprès du [registre npm du paquet officiel](https://registry.npmjs.org/@playwright/test). Aucun navigateur installé ou exécuté par cette suite pendant la réalisation. |

WordPress et Pods sont distribués sous GPL ; MariaDB Server sous GPLv2 ; WP-CLI et Mailpit disposent de leurs licences de projet. Les runtimes ne sont pas recopiés dans ce dépôt. Consulter les licences embarquées des versions téléchargées pour une redistribution de ces composants. Les noms/version de tags ne constituent pas des garanties d'immuabilité : des digests peuvent être figés après téléchargement si le déploiement l'exige.

## Sources métier

Les coordonnées et pièces justificatives doivent être validées avec le responsable du site et conservées dans un dossier privé. Le dépôt public ne contient pas les références nominatives détaillées des annuaires ni les données administratives non approuvées.

Les pages juridiques restent des documents de travail à compléter dans WordPress avant ouverture. Voir [publication.md](publication.md).

## Photos à demander et registre à tenir

Demander plusieurs chantiers : vue d'ensemble, détails de finition, commune, travaux effectués et, seulement si disponibles, les deux photos avant/après. Portrait de Christophe facultatif. Demander pour chaque ressource : auteur, date approximative, autorisation du photographe et du client/personnes représentées si nécessaire, portée de publication autorisée, éventuelles limites/retrait.

Tenir ce registre dans un espace privé géré par Aliant, associé à l'identifiant de la réalisation. La case d'autorisation dans WordPress signale la validation éditoriale ; elle ne crée pas elle-même des droits. Retirer les métadonnées sensibles des originaux destinés à la publication, notamment la géolocalisation, et examiner les éléments visibles. Ne pas déposer les contrats/consentements ni les données clients dans Git.
# Complément : image de partage

`wp-content/themes/feret-peinture/assets/partage.png` : composition originale créée pour ce projet le 16 septembre 2026, à partir de texte et de trois aplats de la palette du site. Format 1 200 × 630 px. Typographies système Georgia et Arial rendues dans l’image ; aucun fichier de police supplémentaire distribué. Aucun chantier, portrait ou photo tiers utilisé.
