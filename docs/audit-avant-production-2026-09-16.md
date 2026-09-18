# Audit avant ouverture : Feret Peinture

État actuel : site ouvert au public, formulaire actif et protection HTTP retirée. Voir [le suivi de publication](publication.md). Les constats de prévisualisation ci-dessous sont historiques.

Audit du 16 septembre 2026, sur https://feret-peinture.fr avec authentification, complété par une inspection du serveur, du code et des décisions acquises d’Elisa.

## Corrections réalisées après l’audit

À la demande « corrige » d’Elisa, les sept fichiers divergents ont été synchronisés avec la version locale, après sauvegarde du serveur dans `backups/preprod-corrections-20260916T211047Z`. Le script de synchronisation légale a également été corrigé : le titre de la page fusionnée est maintenant « Mentions légales et confidentialité », y compris son titre de référencement. Le contenu WordPress a été resynchronisé localement et sur le VPS avec un instantané de retour arrière.

`FP_PRIVACY_RETENTION` est renseigné avec la durée déjà validée et `FP_CONTACT_APPROVED` est activé. `FP_MAIL_DELIVERY_VERIFIED` reste acquis. Les validations finales des prestations, de la page légale, de la confidentialité et de l’ouverture restent distinctes ; aucune autorisation n’a été inventée. L’environnement staging, le mot de passe, le noindex et le blocage des emails de visiteurs sont conservés.

Les attentes obsolètes de `tests/browser.spec.mjs` sont corrigées : mobile public si configuré, nouveau titre de FAQ, parcours `/contact/`, formulaire simplifié sans sélecteurs de prestation/période, contrôles des sections métier. La suite refuse une cible autre que localhost. Sa syntaxe a été vérifiée ; son lanceur Playwright n’a pas été exécuté pendant cette intervention. Une recette directe en navigateur a couvert accueil, prestation et formulaire actif aux six largeurs 320, 360, 390, 768, 1024 et 1440 pixels : 18 affichages sans débordement.

Un parcours navigateur complet a été réalisé sur la copie locale : coordonnées manquantes signalées près des champs, saisie conservée, puis envoi avec un email fictif, confirmation et réception constatée dans Mailpit. Le CAPTCHA a effectué sa vérification automatique normale. Les suites serveur locales ont réussi : 31 assertions formulaire, 9 contrôles CAPTCHA et 2 tests du bouton. Aucun nouvel email réel n’a été envoyé à Christophe Feret. La recette HTTP privée du VPS a réussi après déploiement.

Le suivi `publication.md` a été réécrit autour de l’état courant et le guide de Christophe Feret comporte désormais une procédure pratique de tri et de traitement des demandes relatives aux données. Le contrôle final du formulaire actif sur le VPS et de l’indexation publique reste à effectuer lors de la bascule autorisée. Les constats ci-dessous décrivent l’état initial de l’audit, avant ces corrections.

**Verdict initial : site consultable et parcours de navigation fonctionnels, mais bascule en production encore à préparer. Ne pas retirer simplement le mot de passe.** Aucune autorisation d’ouverture, aucun réglage de publication et aucun contenu du site n’ont été modifiés pendant l’audit initial. Les corrections ultérieures sont décrites ci-dessus.

## Points à traiter avant la bascule

| Priorité | Constat vérifié | Action |
| --- | --- | --- |
| Bloquant technique | `FP_PRIVACY_RETENTION` est vide. `fp_quote_ready()` l’exige en production même si le SMTP fonctionne. | Renseigner la durée déjà approuvée : trois ans maximum après le dernier échange pour les demandes sans suite. Aucune nouvelle question sur cette durée. |
| Étape de lancement obligatoire | `FP_LAUNCH_APPROVED`, `FP_CONTACT_APPROVED`, `FP_SERVICES_APPROVED`, `FP_LEGAL_APPROVED` et `FP_PRIVACY_APPROVED` sont faux. La page légale publiée porte `_fp_legal_approved=0`. | Traduire les accords acquis dans la configuration et ne valider les autres indicateurs qu’après accord correspondant. Le contrôle actuel renvoie sept erreurs ; un passage en production sans les résoudre déclencherait une réponse 503. L’identité et les coordonnées acquises ne sont pas à faire confirmer à nouveau. |
| Étape de lancement obligatoire | Le site est en `staging`, `blog_public=0`, protégé par authentification HTTP et noindex. L’extension obligatoire `feret-private-preview` bloque tout `wp_mail`. | Après autorisation finale, effectuer une bascule coordonnée : environnement, validations, retrait du blocage des emails, authentification, noindex et indexation. Le blocage des emails doit être retiré même si le transport SMTP a déjà été testé. |
| À corriger avant livraison | Sept fichiers locaux diffèrent du serveur, après neutralisation des fins de ligne. Le serveur ne possède notamment pas les derniers styles du CAPTCHA et certains ajustements d’espacement. | Comparer et synchroniser la version retenue avec sauvegarde, puis refaire les contrôles ciblés. Ne pas écraser aveuglément un côté par l’autre. |
| Recette à terminer | La page de contact affiche encore le message de préparation, sans formulaire, conformément au mode protégé. | Contrôler le parcours navigateur complet du formulaire actif après préparation de la configuration, en conservant la protection pendant cette recette. L’envoi serveur et la réception IONOS sont déjà acquis ; ils ne prouvent pas le parcours visuel complet. |

Références techniques : `includes/form.php`, fonction `fp_quote_ready()` ; `includes/publication.php`, fonctions `fp_launch_errors()` et contrôle de `template_redirect`.

## Résultats des vérifications

| Contrôle | Résultat |
| --- | --- |
| Navigation HTTP authentifiée | 14 pages atteignables, toutes en HTTP 200, sans lien interne cassé dans le périmètre parcouru. |
| Ressources liées | 13 URL de ressources contrôlées, sans erreur HTTP. Ce relevé ne couvre pas toutes les variantes de chaque `srcset`. |
| Ancres | Aucune ancre interne cassée dans les pages parcourues. |
| Bandeaux de préparation | Aucune occurrence rendue de `preview-strip`, `temporary-message` ou `validation-note` sur les 14 pages. Le message d’indisponibilité du formulaire reste volontairement présent. |
| Structure et référencement | Un H1, un titre, une description et une URL canonique sur chacune des 14 pages. L’indexation reste volontairement bloquée. |
| Affichage adaptatif | Huit pages contrôlées à 320, 768 et 1440 pixels, soit 24 contrôles : aucun débordement horizontal, un H1 par page, aucune image chargée en échec détectée. |
| Navigation mobile | Menu ouvert au clic, navigation visible, fermeture par Échap vérifiée à 320 pixels. |
| Parcours prestation vers contact | Les boutons de la peinture intérieure conservent `?prestation=peinture-interieure`. La sélection effective dans le formulaire reste à vérifier une fois celui-ci actif. |
| Console navigateur | Aucune erreur ni alerte retournée sur le parcours consulté. |
| Redirections | `/devis/` renvoie une 301 vers `/contact/`. `/confidentialite/` renvoie une 301 vers `/mentions-legales/#confidentialite`. HTTP et www redirigent vers HTTPS sans www. |
| Erreurs et confirmation | Une URL inexistante répond 404. `/merci/?sent=1` n’affiche pas de confirmation d’envoi fictive. |
| Confidentialité publiée | Le contenu de la page WordPress correspond au modèle légal du serveur. La page contient les sections légales et confidentialité ; son titre enregistré est encore « Mentions légales ». |
| Syntaxe PHP déployée | 63 fichiers vérifiés, aucune erreur de syntaxe. |
| État du bouton d’envoi | Deux tests locaux réussis : retour d’état et prévention de double soumission ; restauration après retour navigateur. |
| Services du serveur | Nginx, WordPress et base de données actifs. |
| HTTPS | Certificat valable du 16 septembre au 15 décembre 2026 ; minuterie certbot activée. Son futur renouvellement n’est pas simulé par cet audit. |
| Tâches planifiées | Exécution WordPress prévue toutes les cinq minutes ; nettoyage antispam horaire enregistré ; purge des sauvegardes planifiée quotidiennement à 03:17. L’exécution de chaque purge n’a pas été rejouée. |
| Protection actuelle | Accès anonyme refusé en HTTP 401 sur le périmètre testé, y compris API, ressources et robots/sitemap. |

Pages du contrôle adaptatif : accueil, prestations, peinture intérieure, contact, entreprise, zone d’intervention, mentions légales, réalisations. Relecture visuelle supplémentaire de la page de contact à 320 pixels.

## Réalisations et décisions déjà acquises

Les trois réalisations publiées sont marquées `_fp_demo`. Aucun vrai chantier publiable en production n’a été trouvé. Le code les exclut en production : la rubrique et ses liens conditionnels disparaîtront tant qu’aucun vrai chantier autorisé avec photo ne sera publié. Ce comportement est conforme à la décision de conserver les illustrations comme placeholders réservés à la prévisualisation. Les vrais chantiers peuvent être ajoutés ensuite ; ne pas transformer ce constat en demande de renommage de la rubrique.

Le SMTP IONOS est configuré en SSL sur le port 465, avec secret présent sans exposition. `FP_MAIL_DELIVERY_VERIFIED` est vrai. L’envoi et la réception réels du test de 20:27 UTC sont acquis selon `reponses-elisa-2026-09-16.md`. Aucun nouvel email envoyé dans cet audit.

L’accord final d’ouverture et la validation éditoriale finale sont encore en attente dans les décisions du projet. La restauration des sauvegardes et la récupération des accès ont été déclinées : elles ne sont ni déclarées réalisées, ni réintroduites comme conditions préalables. Le report de la médiation reste traité selon la décision consignée, sans nouvel arbitrage demandé.

## Écarts de livraison et entretien

Fichiers différents entre local et serveur :

- `wp-content/themes/feret-peinture/front-page.php` : titre de la section réalisations.
- `wp-content/themes/feret-peinture/functions.php` : formulation de la description de l’entreprise.
- `wp-content/themes/feret-peinture/single-fp_service.php` : formulation du bloc de contact.
- `wp-content/themes/feret-peinture/style.css` : personnalisation du CAPTCHA et espacements présents localement, absents du serveur.
- `wp-content/themes/feret-peinture/inc/privacy-draft.php` : « Christophe Feret » localement, « Christophe » sur le serveur ; pas de différence de durée détectée dans cette comparaison.
- `wp-content/plugins/feret-peinture-core/includes/access.php` : libellé de rôle et commentaire.
- `config/pods/types.php` : texte d’aide et commentaire.

La suite `tests/browser.spec.mjs` n’est plus alignée sur le site : elle attend zéro lien téléphonique, un ancien titre de FAQ, trois sous-titres sur la prestation et une destination `/devis/`. Elle n’a pas été exécutée comme preuve de conformité. Les contrôles navigateur ci-dessus ont été réalisés directement sur le site déployé. Mettre à jour ces attentes avant de réutiliser la suite comme critère de livraison.

Le document `publication.md` conserve des lignes historiques contradictoires avec son état courant. Utiliser les dernières décisions acquises, pas les anciennes lignes « email à vérifier », « restauration obligatoire » ou « pays à confirmer ». Le guide de Christophe contient des principes de suppression, mais la procédure pratique annoncée reste à finaliser.

## Limites et ordre de finalisation

Cet audit n’est pas une certification juridique, un audit de sécurité exhaustif ni une mesure de performance Lighthouse. Le fonctionnement du formulaire actif, le sitemap public et les données structurées en configuration de production n’ont pas été vérifiés en HTTP pendant cette intervention. Les tests qui écrivent des fixtures n’ont pas été lancés sur la base du serveur.

Ordre recommandé : synchroniser le code retenu, préparer la configuration avec les décisions acquises, terminer la recette du formulaire protégé, recueillir les validations finales encore nécessaires, puis ouvrir et contrôler immédiatement le contact, les emails, les redirections et l’indexation.
