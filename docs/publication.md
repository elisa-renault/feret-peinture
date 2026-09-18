# Prévisualisation et ouverture au public

État courant après les corrections du 16 septembre 2026. Ce document remplace les anciennes listes de questions et de blocages ; l’historique des confirmations reste dans [les réponses d’Elisa](reponses-elisa-2026-09-16.md).

Ouverture publique effectuée le 16 septembre 2026 à la suite de la demande d’Elisa de retirer la protection. Le site est en production, sans authentification HTTP, avec indexation autorisée et formulaire actif. Sauvegarde préalable : `backups/public-launch-20260916T213221Z`. Les indicateurs de publication de la version courante sont activés ; cela ne constitue pas une certification juridique indépendante.

## Décisions acquises

| Sujet | État courant |
| --- | --- |
| Identité et coordonnées | Identité juridique validée, mobile 06 83 82 45 16 public, fixe privé, contact@feret-peinture.fr active. Ne pas redemander ces informations. |
| Hébergement | netcup, serveur à Nuremberg en Allemagne, coordonnées de l’hébergeur validées. |
| Messagerie | IONOS Email Basic ; SMTP SSL sur le port 465. Test réel reçu dans INBOX le 16 septembre à 20:27 UTC. `FP_MAIL_DELIVERY_VERIFIED` est activé. |
| Conservation des demandes sans suite | Trois ans maximum après le dernier échange, avec suppression possible plus tôt. `FP_PRIVACY_RETENTION` est désormais renseigné sur le serveur. |
| Accès aux données | Christophe Feret traite les demandes ; accès ponctuel d’Aliant pour la maintenance. Aucun comptable ajouté aux destinataires. |
| IONOS | Sept jours maximum de copies résiduelles après suppression des messages, sans accès hors EEE pour cette messagerie : confirmé par Elisa. |
| Journaux et sauvegardes du site | Journald limité à trente jours, rotation Nginx quotidienne sur quatorze archives, purge quotidienne des sauvegardes de déploiement âgées de plus de trente jours. |
| Pages légales | Mentions légales et confidentialité fusionnées sur `/mentions-legales/`, contenu WordPress synchronisé avec le modèle du thème. Ancienne URL redirigée en 301 vers la section confidentialité. |
| Coordonnées en configuration | `FP_CONTACT_APPROVED` activé en application de l’accord déjà obtenu. |
| Photos d’exemple | Les trois placeholders restent réservés à la prévisualisation. Ils sont exclus de la production ; la galerie restera masquée tant qu’aucun vrai chantier autorisé n’est publié. |
| Restauration et récupération des accès | Vérifications déclinées par Elisa, non réalisées, pas de condition préalable à réintroduire. |
| Médiation | Report acté par Elisa, hors conditions bloquantes du projet. Ce report ne vaut pas conformité juridique. |

Le formulaire conserve sa version simplifiée : nom, commune ou code postal, description, téléphone ou email. Il ne comporte plus de sélecteur de prestation ou de période et ne demande aucune pièce jointe. La possibilité future de prospection n’active aucune campagne et ne transforme pas la demande en consentement commercial.

## État technique et recette

Les sept fichiers divergents identifiés par l’audit ont été synchronisés, y compris les styles du CAPTCHA et les espacements. Le script de synchronisation légale conserve désormais le titre « Mentions légales et confidentialité ». Chaque modification sur le serveur a été précédée d’une sauvegarde ; les secrets restent hors dépôt.

La recette locale du 16 septembre couvre les erreurs du formulaire, la conservation de saisie, sa confirmation après envoi, 31 assertions de traitement serveur, 9 contrôles CAPTCHA et 2 tests du bouton d’envoi. Les résultats et limites complémentaires sont consignés dans [l’audit avant production](audit-avant-production-2026-09-16.md).

Le formulaire est actif en production. Le test réel précédent `FP-ACTIVATION-20260916-2122` a été reçu dans INBOX IONOS à 21:23:56 UTC. Lors de l’ouverture, `FP_PREVIEW_FORM_ENABLED` a été désactivé et l’extension obligatoire de blocage des emails de prévisualisation a été retirée. Son modèle reste disponible dans `config/feret-private-preview.php` pour une future prévisualisation privée.

## Contrôles de l’ouverture

`fp_launch_errors()` renvoie une liste vide et `fp_quote_ready()` est vrai. Les pages principales, le formulaire et les plans de site répondent 200 sans authentification. Les URL inexistantes, les projets d’exemple et les sous-plans de site inconnus ou hors limites répondent 404. Les restrictions d’indexation de prévisualisation sont levées ; la page de remerciement reste exclue du plan de site. L’ancienne URL de confidentialité redirige vers la page fusionnée et est exclue du plan de site.

Le statut HTTP incorrect du plan de site a été corrigé : WordPress conservait un statut 404 malgré un XML valide. Le correctif est limité aux routes de plans de site connues et conserve les erreurs des routes invalides. Vérification PHP réussie et contrôles HTTP publics effectués. Trois assertions de régression ont été ajoutées à la suite de publication ; cette suite à fixtures n’a pas été exécutée sur la base réelle.

Les validations `FP_SERVICES_APPROVED`, `FP_LEGAL_APPROVED`, `FP_PRIVACY_APPROVED`, `FP_LAUNCH_APPROVED` et la métadonnée `_fp_legal_approved` reflètent la publication de la version courante demandée par Elisa.

Les principes et étapes de traitement des demandes de suppression figurent dans [le guide de Christophe Feret](guide-christophe.md). Leur rédaction ne constitue pas la preuve d’une suppression déjà effectuée.

## Procédure de bascule pour référence

1. Sauvegarder base, médias, code et configuration privée dans l’emplacement protégé du site. Cette sauvegarde de changement ne réintroduit pas le test de restauration décliné.
2. Vérifier les validations restantes et activer uniquement celles dont l’accord a été obtenu. Renseigner `_fp_legal_approved` sur la page fusionnée.
3. Conserver l’authentification HTTP pendant la préparation finale. Passer WordPress en production avec toutes les validations nécessaires, puis retirer l’extension obligatoire qui bloque les emails.
4. Vérifier que `fp_launch_errors()` renvoie une liste vide et que `fp_quote_ready()` est vrai. Sans cela, le site peut répondre 503 ou le formulaire rester inactif.
5. Contrôler le formulaire actif sur le VPS protégé, sa confirmation et la réception du message de recette. La livraison IONOS est déjà acquise ; ce contrôle porte sur l’ensemble final déployé.
6. Activer l’indexation (`blog_public=1`), retirer les en-têtes noindex de prévisualisation et les directives d’authentification HTTP, tester Nginx avant rechargement. Les URL sont déjà celles de production ; aucune migration de domaine n’est nécessaire.
7. Vérifier sans session : accueil, prestations, contact, mentions légales, 404, redirections, sitemap, canonicals et données structurées. Les exemples doivent être absents, la page de remerciement doit rester non indexable, et une URL `?sent=1` ne doit jamais simuler un envoi.
8. En cas d’échec, rétablir la protection et la configuration sauvegardée avant de poursuivre.

## Après ouverture

Search Console et la fiche Google Business ne sont à raccorder ou modifier qu’avec l’autorisation du compte concerné. Ne pas inventer d’avis, d’horaires d’accueil ou de coordonnées géographiques.

Les événements locaux `click_phone`, `click_quote`, `form_start`, `form_success` et `form_error` ne transmettent pas de données à un service de mesure. Aucun outil analytique externe n’est activé par cette préparation. Toute connexion future demande une décision distincte, dans un projet propre au client.
