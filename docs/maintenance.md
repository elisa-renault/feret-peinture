# Exploitation par Aliant

Procédure préparée, à adapter au serveur choisi. Aucune commande de déploiement, certificat, DNS ou restauration de production n'a été exécutée pour cette livraison. Le démarrage Docker local figure dans le README ; Compose est un environnement de développement, pas une configuration publique prête à exposer.

## Responsabilités et composants

Christophe utilise les trois rubriques métier. Aliant gère comptes, mises à jour, messagerie, configuration privée, hébergement et restauration. Les médias et la base ne sont pas dans Git. Le thème contient la présentation ; `feret-peinture-core` conserve types, autorisations et fonctions métier ; `config/pods` versionne les champs.

Les versions locales fixées se trouvent dans `compose.yaml`, `.env.example` et les scripts d'installation. Pour Debian natif, contrôler d'abord la version de l'OS, les [versions PHP maintenues](https://www.php.net/supported-versions.php), les paquets disponibles et la compatibilité WordPress/Pods. Ne pas supposer PHP 8.3 disponible dans les dépôts de toute version de Debian. Utiliser le PHP-FPM maintenu de la distribution, puis rejouer la recette sur cette version ; une exécution locale avec PHP 8.3 ne vérifie pas automatiquement un serveur en PHP 8.4.

La recette native et l'environnement Compose sont distincts. Notamment, un service Apache de l'image Docker ne valide pas la configuration Nginx de la cible. L'absence de Docker sur une machine de recette doit rester indiquée dans les résultats, sans présenter la seule analyse YAML comme un lancement réussi.

Prévoir Nginx, PHP-FPM, client MariaDB, MariaDB, WP-CLI et les modules PHP nécessaires : MySQLi, cURL, XML/DOM, mbstring, zip, intl, GD ou Imagick, fileinfo, EXIF et OPcache selon disponibilités. Examiner `php -m`, la page Santé du site et les extensions réellement installées. JPEG/PNG constituent le parcours photographique de référence ; HEIC/AVIF/WebP dépendent du serveur et doivent être testés avant d'être annoncés.

## Arborescence et secrets

Exemple d'organisation à créer uniquement sur le serveur retenu :

| Chemin indicatif | Contenu et droits |
| --- | --- |
| `/srv/feret-peinture/current/` | WordPress, thème et extensions ; lecture par le pool PHP ; écriture du code réservée au déploiement. |
| `/srv/feret-peinture/current/config/pods/` | Définition versionnée des champs, copiée depuis le dépôt ; chemin requis par le plugin métier. |
| `/srv/feret-peinture/current/wp-content/uploads/` | Médias ; écriture nécessaire au pool PHP dédié. |
| `/etc/feret-peinture/` | Configuration privée, hors racine web ; accès limité à l'opérateur et au processus qui en a besoin. |
| `/var/backups/feret-peinture/` | Sauvegardes privées, jamais servies par Nginx. |
| `/srv/feret-peinture-restore/` | Instance séparée de test de restauration ; autre base et autre configuration. |

Le fichier `wp-config.php` doit lire des secrets privés disponibles aussi en WP-CLI. Vérifier le traitement des variables d'environnement par PHP-FPM (`clear_env` et déclarations du pool) : ne pas supposer que les variables du shell arrivent dans les requêtes web. Pour une configuration PHP privée incluse, permissions minimales et aucun accès HTTP. Ne jamais copier les identifiants locaux de `.env` en production.

Utiliser un utilisateur SQL dédié limité à sa base, un compte d'administration Aliant nominatif et le rôle `fp_christophe` pour Christophe. Réinitialisation et email du compte doivent être opérationnels avant remise. Ne pas donner `manage_options` au rôle Christophe. Désactiver l'éditeur de fichiers via `DISALLOW_FILE_EDIT`; garder erreurs détaillées hors réponses publiques, secrets et contenu des devis hors logs de debug. Utiliser un compte/pool distinct des autres sites lorsque le serveur le permet.

## Préparer Nginx et HTTPS

1. Installer WordPress et les dépendances exactes depuis leurs sources officielles ; copier le thème, le plugin **et `config/pods/` à la racine WordPress** depuis le commit retenu ; configurer la base et les secrets hors Git. Copier aussi les scripts de bootstrap nécessaires pendant l'installation, hors exposition HTTP, puis exécuter l'initialisation selon le README. Les champs Pods ne sont pas enregistrés si leur fichier de définition manque.
2. Préparer un hôte de staging distinct, HTTPS et authentification avant import de contenu. Le DNS/certificat se met en place seulement après autorisation correspondante. Ne pas activer un nom d'hôte inventé dans la configuration publique.
3. Adapter le modèle ci-dessous au document root et au socket PHP constatés ; vérifier `nginx -t` avant tout rechargement. Les certificats doivent déjà exister ; ce fragment ne les demande pas.

```nginx
# Extrait de server HTTPS, chemins à adapter au serveur réellement choisi.
root /srv/feret-peinture/current;
index index.php;
client_max_body_size 12m;

location / {
    try_files $uri $uri/ /index.php?$args;
}

# Placer ces interdictions avant le bloc PHP générique.
location ~* ^/wp-content/uploads/.*\.(php[0-9]?|phtml|phar)$ {
    deny all;
}
location ~* ^/(wp-config\.php|\.env|\.git)(/|$) {
    deny all;
}
location ~* \.(sql|sqlite|log|bak)$ {
    deny all;
}
location ~ /\.(?!well-known/) {
    deny all;
}
location = /xmlrpc.php {
    deny all;
}
location ~ \.php$ {
    try_files $uri =404;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_param HTTPS on;
    fastcgi_pass unix:/run/php/phpX.Y-fpm.sock;
}
```

`phpX.Y` doit être remplacé par le socket réel ; le fragment n'est pas installable tel quel. Aligner `upload_max_filesize`, `post_max_size` et la limite Nginx pour les photos administrateur ; le formulaire public n'accepte aucun fichier. Ne pas mettre en cache les requêtes POST, `/wp-admin/`, la connexion, les aperçus ou les réponses du formulaire ; si un cache est ajouté, exclure les pages à jeton ou vérifier son renouvellement.

Le bootstrap de contenus se lance avec WP-CLI via `wp eval-file /chemin-prive-du-depot/scripts/bootstrap.php`, depuis la racine WordPress configurée. Il ne remplace pas l'installation initiale de WordPress ou la création de la base. Activer Pods puis `feret-peinture-core`, activer le thème `feret-peinture`, puis exécuter le bootstrap avec la configuration de staging. Le script Docker `install.sh` refuse volontairement les environnements non locaux : ne pas contourner sa vérification pour déployer. Créer les comptes de production avec des identités/mots de passe nouveaux et le rôle métier prévu, plutôt que réutiliser le script de comptes locaux.

En staging seulement, ajouter dans le bloc `server` HTTPS :

```nginx
auth_basic "Prévisualisation Feret Peinture";
auth_basic_user_file /etc/nginx/feret-staging.htpasswd;
add_header X-Robots-Tag "noindex, nofollow, noarchive" always;
```

Créer le fichier de mots de passe hors webroot, avec `htpasswd` interactif et permissions de lecture minimales. Tester sans identifiant que les pages, médias, REST et sitemap exigent une authentification. Le [module officiel Nginx](https://nginx.org/en/docs/http/ngx_http_auth_basic_module.html) permet cette protection ; le noindex ne protège pas des accès.

Après autorisation, le site public utilise un hôte HTTPS sans www. Les hôtes http et HTTPS www doivent rediriger vers `https://feret-peinture.fr$request_uri`; le certificat doit couvrir les noms effectivement utilisés. Ne pas activer HSTS tant que certificats, renouvellement et redirections ne sont pas validés. La [validation Let's Encrypt HTTP-01](https://letsencrypt.org/docs/challenge-types/) exige un accès au chemin de challenge ; prévoir cette seule exception si nécessaire, sans ouvrir le staging. Tester le renouvellement selon le client ACME choisi et surveiller l'expiration.

Références de configuration : [WordPress avec Nginx](https://developer.wordpress.org/advanced-administration/server/web-server/nginx/) et [durcissement WordPress](https://developer.wordpress.org/advanced-administration/security/hardening/). Les fragments sont des recettes à adapter, pas la preuve d'une configuration serveur testée.

## Sauvegarde cohérente de la base et des médias

Politique proposée à approuver : sauvegarde quotidienne et avant chaque mise à jour, copie chiffrée hors serveur, historique limité à une durée décidée avec le responsable du site. Vérifier chaque sauvegarde et tester périodiquement la restauration. Conserver séparément les éléments nécessaires pour reconstruire : commit du site, versions WordPress/Pods, configuration privée et configuration Nginx/PHP, procédure d'accès. La base seule ne contient pas les fichiers photo.

Exemple Bash pour une installation native existante. À lancer avec un compte opérateur autorisé à écrire les sauvegardes et à exécuter WP-CLI sous l'utilisateur du pool PHP. Adapter les deux variables ; suspendre temporairement l'édition des contenus pendant base + médias pour obtenir une paire cohérente. Ne pas saisir de mot de passe SQL en ligne de commande.

```bash
set -euo pipefail
umask 077
fp_root='/srv/feret-peinture/current'
fp_php_user='www-data'
fp_backup="/var/backups/feret-peinture/$(date -u +%Y%m%dT%H%M%SZ)"
test -f "$fp_root/wp-config.php"
test -d "$fp_root/wp-content/uploads"
mkdir -p "$fp_backup"
sudo -u "$fp_php_user" wp --path="$fp_root" maintenance-mode activate
trap 'sudo -u "$fp_php_user" wp --path="$fp_root" maintenance-mode deactivate >/dev/null 2>&1 || true' EXIT
sudo -u "$fp_php_user" wp --path="$fp_root" db export - --single-transaction --quick | gzip > "$fp_backup/database.sql.gz"
tar -C "$fp_root" -czf "$fp_backup/uploads.tar.gz" wp-content/uploads
gzip -t "$fp_backup/database.sql.gz"
tar -tzf "$fp_backup/uploads.tar.gz" >/dev/null
(
    cd "$fp_backup"
    sha256sum database.sql.gz uploads.tar.gz > SHA256SUMS
)
sudo -u "$fp_php_user" wp --path="$fp_root" maintenance-mode deactivate
trap - EXIT
```

Les permissions du dossier parent doivent permettre à l'opérateur d'écrire ; utiliser `sudo` pour sa création initiale seulement si nécessaire. La maintenance rend le site temporairement indisponible et n'empêche pas tous les processus externes d'écrire : convenir d'une pause éditoriale et suspendre les tâches modifiant les contenus. `--single-transaction` vise la cohérence des tables transactionnelles ; contrôler le moteur des tables. Une archive validée structurellement ne remplace pas une restauration réussie. Chiffrer/transférer la paire et `SHA256SUMS` vers l'emplacement privé choisi ; ne pas envoyer ces fichiers dans Git ou un répertoire web.

Les commandes suivent les références officielles [sauvegarde WordPress](https://developer.wordpress.org/advanced-administration/security/backup/) et [wp db export](https://developer.wordpress.org/cli/commands/db/export/).

## Restauration sur instance isolée

Préparer **avant** l'import une copie de WordPress et des dépendances au commit/version sauvegardés, un `wp-config.php` pointant vers une **base de test dédiée vide**, un utilisateur SQL de test, un hôte protégé et le transport de test. Définir `WP_ENVIRONMENT_TYPE=staging`, désactiver approbations de lancement et tâches sortantes ; ne jamais réutiliser les secrets SMTP réels. Lire le nom et l'hôte de base dans la configuration privée localement, sans publier son contenu.

Le script refuse un chemin hors répertoire de restauration. Il ne prépare ni la base ni le code et n'efface aucun média existant : la cible médias doit être absente. Renseigner `fp_backup` avec une sauvegarde privée de confiance, jamais une archive arbitraire.

```bash
set -euo pipefail
fp_restore='/srv/feret-peinture-restore/site'
fp_php_user='www-data'
fp_backup='/var/backups/feret-peinture/DATE_VALIDEE'
case "$fp_restore" in /srv/feret-peinture-restore/*) ;; *) exit 1 ;; esac
test -f "$fp_restore/wp-config.php"
test ! -e "$fp_restore/wp-content/uploads"
test -s "$fp_backup/database.sql.gz"
(
    cd "$fp_backup"
    sha256sum -c SHA256SUMS
)
# La base configurée ci-dessus doit être une base de restauration dédiée.
gzip -dc "$fp_backup/database.sql.gz" | sudo -u "$fp_php_user" wp --path="$fp_restore" db import -
tar -C "$fp_restore" --no-same-owner -xzf "$fp_backup/uploads.tar.gz"
sudo chown -R "$fp_php_user:$fp_php_user" "$fp_restore/wp-content/uploads"
```

Une fois cette copie isolée créée, remplacer les URL avec WP-CLI, qui gère les données sérialisées. Adapter les deux URL avant exécution. Le premier passage n'écrit rien ; lire son bilan, puis retirer `--dry-run` uniquement sur cette copie.

```bash
sudo -u "$fp_php_user" wp --path="$fp_restore" search-replace \
  'https://feret-peinture.fr' 'https://staging.example.invalid' \
  --all-tables-with-prefix --skip-columns=guid --dry-run
```

Ne pas conserver `staging.example.invalid` : c'est un repère documentaire, pas un domaine prévu. Vérifier options `home`/`siteurl`, permaliens, chantiers, coordonnées, miniatures, galeries dans leur ordre, avant/après, droits Christophe et réception dans la boîte de test. La restauration des fichiers photo d'origine doit être vérifiée, pas uniquement leurs miniatures. Enregistrer date, archive, commit/versions, durée mesurée, résultat et limites hors données clients. Références : [wp db import](https://developer.wordpress.org/cli/commands/db/import/) et [wp search-replace](https://developer.wordpress.org/cli/commands/search-replace/).

Une restauration de production remplace des données : elle nécessite une fenêtre décidée, une sauvegarde de l'état courant et vérification de l'absence de nouvelles demandes à perdre. Utiliser la procédure éprouvée sur la copie, après décision d'Aliant ; aucune commande de restauration destructrice automatisée n'est livrée.

## Mises à jour et surveillance

- Avant mise à jour : sauvegarde vérifiée, changelog de sécurité/compatibilité, staging avec données de test. Mettre à jour les versions fixées dans le dépôt, pas seulement sur le serveur.
- Après mise à jour : lint PHP, parcours devis et panne SMTP, rôle Christophe, ajout photo, révisions, rendu mobile, sitemap/JSON-LD. Le bootstrap ne doit pas réinitialiser ses contenus.
- Vérifier les fichiers WordPress officiels avec [`wp core verify-checksums`](https://developer.wordpress.org/cli/commands/core/verify-checksums/) ; la commande ne valide pas le code sur mesure. Ne pas utiliser `--insecure` pour masquer un problème de certificat.
- Surveiller échecs SMTP, disponibilité, espace disque, sauvegardes et certificats, avec accès restreint et sans journaliser les descriptions/contact des demandes. Définir qui reçoit ces alertes.
- Garder l'expéditeur de test sur staging. Mailpit, base de données et ports de développement ne doivent pas être exposés à Internet.
- En incident : bloquer si nécessaire la fonction concernée, conserver les preuves techniques privées, restaurer un état testé ou corriger ; informer le responsable du site avec l'impact constaté. Ne pas prétendre une livraison email sur le seul retour de l'API.

## Réversibilité

Le dossier remis doit contenir l'export SQL, les médias, le code et son commit, les versions de dépendances, les accès et configurations transmis par canal privé, la procédure de restauration et les autorisations photo. Les révisions WordPress sont une aide éditoriale ; elles ne remplacent pas la sauvegarde de la base et des médias. Faire une remise effective des accès en fin de prestation sans exposer les secrets dans la PR.
