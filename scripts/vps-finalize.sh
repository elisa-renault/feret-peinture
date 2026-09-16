#!/bin/sh
set -eu
base=/srv/apps/feret-peinture
umask 077
stamp=$(date -u +%Y%m%dT%H%M%SZ)
podman exec feret-db sh -c 'exec mariadb-dump -uroot -p"$MARIADB_ROOT_PASSWORD" --single-transaction feret' | gzip > "$base/backups/$stamp.sql.gz"
tar czf "$base/backups/$stamp-files.tgz" -C "$base" wordpress/wp-content wordpress/wp-config.php private code
mkdir -p /etc/letsencrypt/renewal-hooks/deploy
cat > /etc/letsencrypt/renewal-hooks/deploy/feret-nginx <<'HOOK'
#!/bin/sh
set -eu
/usr/sbin/nginx -t
/usr/bin/systemctl reload nginx
HOOK
chmod 755 /etc/letsencrypt/renewal-hooks/deploy/feret-nginx
systemctl is-active certbot.timer
test -L /run/systemd/generator/multi-user.target.wants/feret-db.service
test -L /run/systemd/generator/multi-user.target.wants/feret-wordpress.service
ss -lnt | grep 8086
printf '%s\n' 'Backup, certificate reload hook and boot dependencies verified.'
