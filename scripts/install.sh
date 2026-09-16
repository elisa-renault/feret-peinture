#!/bin/sh
# Invoked inside the wpcli container. Seeds content once, never resets editors' data.
set -eu
[ "${WP_ENVIRONMENT_TYPE:-}" = local ] || { printf '%s\n' 'Ce script initialise uniquement un environnement local.' >&2; exit 1; }
: "${WP_ADMIN_PASSWORD:?Private admin password is required}"
: "${CHRISTOPHE_PASSWORD:?Private Christophe password is required}"
: "${WP_URL:?Local WordPress URL is required}"

attempt=0
while [ ! -f wp-config.php ]; do
  attempt=$((attempt + 1))
  [ "$attempt" -lt 30 ] || { printf '%s\n' 'WordPress non initialisé : vérifier docker compose logs wordpress.' >&2; exit 1; }
  sleep 2
done

if ! wp core is-installed >/dev/null 2>&1; then
  # WP-CLI echoes prompted arguments even through a pipe; discard installation
  # output so the private password never lands in a CI log.
  if ! printf '%s\n' "$WP_ADMIN_PASSWORD" | wp core install \
    --url="$WP_URL" --title='Feret Peinture' \
    --admin_user="${WP_ADMIN_USER:-aliant}" \
    --admin_email="${WP_ADMIN_EMAIL:-aliant@example.test}" \
    --skip-email --prompt=admin_password >/dev/null 2>&1; then
    printf '%s\n' 'Installation WordPress impossible. Vérifier la base, les comptes et les paramètres locaux.' >&2
    exit 1
  fi
  # This branch runs only after a new WordPress installation. Preserve all
  # content when adopting an existing installation or repeating the bootstrap.
  wp post list --post_type=post,page --post_status=any --format=ids | xargs -r wp post delete --force
fi

if ! wp plugin is-installed pods; then
  wp plugin install pods --version=3.3.9.2
fi
test "$(wp plugin get pods --field=version)" = 3.3.9.2 || {
  printf '%s\n' 'Version Pods différente de 3.3.9.2 : vérifier la migration avant de continuer.' >&2
  exit 1
}
wp plugin activate pods feret-peinture-core
wp theme activate feret-peinture
wp language core install fr_FR --activate
wp language plugin install pods fr_FR || printf '%s\n' 'Traduction Pods indisponible : les libellés métier restent en français.'
wp eval-file /project/scripts/bootstrap.php
wp eval-file /project/scripts/create-users.php
wp rewrite flush --hard
printf '%s\n' 'Installation terminée. Les contenus et comptes déjà présents ont été conservés.'
