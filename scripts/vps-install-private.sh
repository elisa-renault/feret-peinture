#!/bin/sh
# One-time private installation. Run as root after uploading the three archives.
set -eu
base=/srv/apps/feret-peinture
upload=/home/elsia/feret-deploy-20260916
test ! -e "$base"
test ! -e /etc/nginx/sites-available/feret-peinture
mkdir -p "$base/code"
tar xzf "$upload/feret-code.tgz" -C "$base/code"
python3 "$base/code/scripts/vps-prepare.py"
podman network create feret-private
systemctl daemon-reload
systemctl start feret-db.service
attempt=0
until podman exec feret-db healthcheck.sh --connect --innodb_initialized >/dev/null 2>&1; do
    attempt=$((attempt + 1))
    test "$attempt" -lt 60
    sleep 2
done
# This is a new, isolated database, never an existing hosted client database.
podman exec -i feret-db sh -c 'exec mariadb -uroot -p"$MARIADB_ROOT_PASSWORD"' < "$upload/feret-private.sql"
tar xzf "$upload/feret-media.tgz" -C "$base/wordpress/wp-content"
chown -R 33:33 "$base/wordpress"
systemctl start feret-wordpress.service
attempt=0
until test -e "$base/wordpress/wp-includes/version.php"; do
    attempt=$((attempt + 1))
    test "$attempt" -lt 60
    sleep 2
done
cat > "$base/wp" <<'WPCLI'
#!/bin/sh
exec podman run --rm --network feret-private --user 33:33 \
  --env-file /srv/apps/feret-peinture/private/wordpress.env \
  --env-file /srv/apps/feret-peinture/private/accounts.env \
  -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache \
  -v /srv/apps/feret-peinture/wordpress:/var/www/html \
  -v /srv/apps/feret-peinture/code/wp-content/themes/feret-peinture:/var/www/html/wp-content/themes/feret-peinture:ro \
  -v /srv/apps/feret-peinture/code/wp-content/plugins/feret-peinture-core:/var/www/html/wp-content/plugins/feret-peinture-core:ro \
  -v /srv/apps/feret-peinture/code/config/pods:/var/www/html/config/pods:ro \
  -v /srv/apps/feret-peinture/code:/project:ro \
  docker.io/library/wordpress:cli-2.12.0-php8.3 wp "$@"
WPCLI
chmod 700 "$base/wp"
mkdir -m 700 "$base/backups"
cp "$upload/feret-private.sql" "$base/backups/before-url-migration.sql"
"$base/wp" search-replace http://localhost:8080 https://feret-peinture.fr --all-tables-with-prefix --skip-columns=guid --dry-run
"$base/wp" search-replace http://localhost:8080 https://feret-peinture.fr --all-tables-with-prefix --skip-columns=guid
"$base/wp" eval-file /project/scripts/vps-rotate-accounts.php
"$base/wp" option update blog_public 0
"$base/wp" rewrite flush --hard
"$base/wp" core version
"$base/wp" plugin list

# No public site content is served while obtaining the certificate.
cat > /etc/nginx/sites-available/feret-peinture <<'NGINX'
server {
    listen 80;
    listen [::]:80;
    server_name feret-peinture.fr www.feret-peinture.fr;
    location ^~ /.well-known/acme-challenge/ { root /var/www/html; }
    location / { return 403; }
}
NGINX
ln -s /etc/nginx/sites-available/feret-peinture /etc/nginx/sites-enabled/feret-peinture
nginx -t
systemctl reload nginx
certbot certonly --webroot -w /var/www/html -d feret-peinture.fr -d www.feret-peinture.fr --non-interactive --agree-tos --register-unsafely-without-email
cp "$base/code/scripts/vps-nginx.conf" /etc/nginx/sites-available/feret-peinture
nginx -t
systemctl reload nginx
printf '%s\n' '*/5 * * * * root /srv/apps/feret-peinture/wp cron event run --due-now --quiet >/dev/null 2>&1' > /etc/cron.d/feret-peinture
chmod 644 /etc/cron.d/feret-peinture
printf '%s\n' 'Private HTTPS deployment installed.'
