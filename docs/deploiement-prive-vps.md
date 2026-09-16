# Déploiement privé du 16 septembre 2026

Le site est installé sur le VPS `elsia-netcup` (`2.56.96.197`) à l'adresse `https://feret-peinture.fr`. HTTP et www redirigent vers cette adresse. Le certificat couvre les deux noms et son renouvellement automatique recharge Nginx.

## Accès temporaire

Identifiant partagé : `visite`. Le mot de passe reste dans le fichier privé d'accès.

La protection par mot de passe est appliquée par Nginx à l'ensemble du site, y compris les images, le formulaire, l'administration et l'API. Les accès sont conservés dans `.local-credentials-vps.txt`, exclu de Git, et dans le fichier privé `/srv/apps/feret-peinture/private/access.txt` sur le serveur. Aucun secret ne doit être copié dans le dépôt.

Cette protection est temporaire et indépendante des comptes WordPress. Elle n'est pas limitée à une adresse IP : Elisa peut consulter le site depuis une autre connexion avec ses identifiants.

Le site reste en environnement `staging`, avec `blog_public=0`, un en-tête `X-Robots-Tag: noindex, nofollow, noarchive` et une interdiction de cache partagé. Les emails sortants sont désactivés par une extension obligatoire réservée à cette prévisualisation. Aucune validation de publication ou de livraison des emails n'a été activée.

## Installation

- Dossier : `/srv/apps/feret-peinture`.
- Code : sous-dossier `code`, thème et plugin métier montés en lecture seule.
- Base et fichiers WordPress : sous-dossiers `database` et `wordpress`.
- Services isolés Podman : `feret-db.service` et `feret-wordpress.service`, gérés par les fichiers Quadlet de `/etc/containers/systemd` et démarrés automatiquement avec le serveur.
- WordPress n'écoute que sur `127.0.0.1:8086`. La base ne publie aucun port sur le VPS.
- Nginx : `/etc/nginx/sites-available/feret-peinture`.
- Commandes WordPress : `sudo /srv/apps/feret-peinture/wp ...`.
- Tâches WordPress : `/etc/cron.d/feret-peinture`, toutes les cinq minutes.
- Sauvegardes initiales base, fichiers et configuration : sous-dossier privé `backups`. Ces sauvegardes sont sur le même serveur et ne constituent pas une sauvegarde externalisée.

La base et les médias locaux ont été copiés. Les URL ont été remplacées après sauvegarde et simulation. Les mots de passe des deux comptes importés ont été renouvelés et leurs anciennes sessions invalidées. La base locale est conservée.

Les scripts `vps-prepare.py` et `vps-install-private.sh` servent uniquement à cette première installation sur un emplacement vide. Ne pas les relancer pour une mise à jour. Mettre à jour les seuls fichiers de code après sauvegarde, en conservant base, médias et secrets du serveur.

## Vérifications réalisées

- Certificat HTTPS valide pour les deux domaines.
- Refus HTTP 401 sans identifiants sur les pages, l'administration, l'API, les fichiers robots/sitemap et les ressources liées.
- Réponse 200 avec authentification sur accueil, entreprise, prestations, réalisations, zone, devis et connexion WordPress.
- Images, styles et scripts liés disponibles avec authentification et bloqués sans elle.
- Absence d'URL localhost dans les pages contrôlées.
- Redirections HTTP et www vers HTTPS sans www, avec protection conservée.
- Services actifs, démarrage automatique configuré et port applicatif limité à la boucle locale.

Vérification HTTP reproductible : `sudo python3 /srv/apps/feret-peinture/code/scripts/vps-verify.py`.

## Synchronisation des changements locaux

La mise à jour du 16 septembre comprend la page `/contact/`, la redirection de `/devis/`, les contenus validés, les exemples de chantiers réservés à la prévisualisation et le CAPTCHA autonome. La bibliothèque PHP ALTCHA et sa licence sont incluses dans Git ; les téléchargements et fichiers de travail `output/` sont exclus.

Les contrôles locaux du formulaire (31 assertions), du CAPTCHA (9 assertions), du bouton d'envoi et de la syntaxe PHP ont réussi avant le déploiement. Les comptes, secrets et données propres au serveur sont conservés. Les identifiants numériques des photos et chantiers diffèrent entre les deux installations et ne doivent pas être copiés tels quels.

## Retrait de la protection temporaire

Lorsqu'Elisa demande l'ouverture publique, suivre les validations de `publication.md`, préparer les emails réels et vérifier la livraison. Retirer les deux directives `auth_basic` et `auth_basic_user_file` du serveur HTTPS Feret Peinture, retirer les en-têtes de prévisualisation, passer WordPress en production et activer l'indexation seulement après validation. Supprimer l'extension obligatoire de blocage des emails lorsque le transport réel est prêt. Tester Nginx avant de le recharger, puis vérifier le site sans identifiants. Le mot de passe temporaire n'est pas intégré au thème et aucun changement de domaine n'est nécessaire.
