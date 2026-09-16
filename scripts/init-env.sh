#!/bin/sh
# Generate private development credentials; never overwrite an existing .env.
set -eu
cd "$(dirname "$0")/.."
if [ -e .env ]; then
  printf '%s\n' '.env existe déjà : aucune modification.'
  exit 0
fi
command -v openssl >/dev/null 2>&1 || { printf '%s\n' 'OpenSSL est requis pour générer les secrets.' >&2; exit 1; }
umask 077
set -C
{
  printf '%s\n' '# Développement local uniquement. Ne pas versionner ni réutiliser en production.'
  for key in DB_PASSWORD DB_ROOT_PASSWORD WP_ADMIN_PASSWORD CHRISTOPHE_PASSWORD; do
    printf '%s=%s\n' "$key" "$(openssl rand -hex 24)"
  done
  printf '%s\n' 'WP_ADMIN_USER=aliant' 'WP_ADMIN_EMAIL=aliant@example.test' 'CHRISTOPHE_EMAIL=christophe@example.test' 'WP_PORT=8080' 'WP_URL=http://localhost:8080' 'MAILPIT_PORT=8025'
} > .env
printf '%s\n' '.env créé. Les mots de passe locaux sont consultables dans ce fichier privé.'
