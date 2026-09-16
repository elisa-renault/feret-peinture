"""One-time installation into an empty VPS directory, run as root."""
import pathlib
import secrets
import subprocess

base = pathlib.Path('/srv/apps/feret-peinture')
private = base / 'private'
private.mkdir(mode=0o700, parents=True, exist_ok=True)
if (private / 'wordpress.env').exists():
    raise SystemExit('Existing configuration found; refusing to rotate secrets.')

def save(path, content, mode=0o600):
    path.write_text(content, encoding='utf-8')
    path.chmod(mode)

db_password = secrets.token_hex(24)
admin_password = secrets.token_urlsafe(24)
editor_password = secrets.token_urlsafe(24)
preview_password = secrets.token_urlsafe(18)
save(private / 'database.env', 'MARIADB_DATABASE=feret\nMARIADB_USER=feret\n'
     f'MARIADB_PASSWORD={db_password}\nMARIADB_ROOT_PASSWORD={secrets.token_hex(32)}\n')
save(private / 'wordpress.env', 'WORDPRESS_DB_HOST=feret-db:3306\n'
     'WORDPRESS_DB_NAME=feret\nWORDPRESS_DB_USER=feret\n'
     f'WORDPRESS_DB_PASSWORD={db_password}\nWP_ENVIRONMENT_TYPE=staging\n')
save(private / 'accounts.env', f'FP_ADMIN_PASSWORD={admin_password}\nFP_EDITOR_PASSWORD={editor_password}\n')
save(private / 'access.txt', 'Site : https://feret-peinture.fr\n'
     f'Acces temporaire : visite\nMot de passe temporaire : {preview_password}\n\n'
     f'WordPress : https://feret-peinture.fr/wp-admin/\nCompte : aliant\nMot de passe : {admin_password}\n'
     f'Compte editeur : christophe\nMot de passe : {editor_password}\n')
hashed = subprocess.run(['openssl', 'passwd', '-6', '-stdin'], input=preview_password+'\n',
                        text=True, capture_output=True, check=True).stdout.strip()
auth = pathlib.Path('/etc/nginx/feret-peinture.htpasswd')
save(auth, f'visite:{hashed}\n', 0o640)
subprocess.run(['chown', 'root:www-data', str(auth)], check=True)
save(private / 'curl.conf', f'user = "visite:{preview_password}"\n')

wp = base / 'wordpress'
wp.mkdir(exist_ok=True)
config = '''<?php
define('DB_NAME', getenv('WORDPRESS_DB_NAME'));
define('DB_USER', getenv('WORDPRESS_DB_USER'));
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD'));
define('DB_HOST', getenv('WORDPRESS_DB_HOST'));
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');
$table_prefix = 'wp_';
define('WP_ENVIRONMENT_TYPE', 'staging');
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
define('DISALLOW_FILE_EDIT', true);
define('DISABLE_WP_CRON', true);
define('WP_POST_REVISIONS', 20);
define('FORCE_SSL_ADMIN', true);
define('FP_LAUNCH_APPROVED', false);
define('FP_MAIL_DELIVERY_VERIFIED', false);
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
'''
for key in ['AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY',
            'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT']:
    config += f"define('{key}', '{secrets.token_hex(48)}');\n"
config += "if (!defined('ABSPATH')) { define('ABSPATH', __DIR__ . '/'); }\nrequire_once ABSPATH . 'wp-settings.php';\n"
save(wp / 'wp-config.php', config, 0o640)
subprocess.run(['chown', '33:33', str(wp / 'wp-config.php')], check=True)
mu = wp / 'wp-content/mu-plugins'
mu.mkdir(parents=True, exist_ok=True)
save(mu / 'feret-private-preview.php', "<?php\n// No outbound email during the private preview.\nadd_filter('pre_wp_mail', '__return_false');\n", 0o644)

quadlets = pathlib.Path('/etc/containers/systemd')
quadlets.mkdir(parents=True, exist_ok=True)
save(quadlets / 'feret-db.container', f'''[Unit]
Description=Feret Peinture private database
[Container]
Image=docker.io/library/mariadb:10.11.19-jammy
ContainerName=feret-db
Network=feret-private
EnvironmentFile={private}/database.env
Volume={base}/database:/var/lib/mysql
[Service]
Restart=always
TimeoutStartSec=180
[Install]
WantedBy=multi-user.target
''', 0o644)
save(quadlets / 'feret-wordpress.container', f'''[Unit]
Description=Feret Peinture private WordPress
After=feret-db.service
Requires=feret-db.service
[Container]
Image=docker.io/library/wordpress:7.1.0-php8.3-apache
ContainerName=feret-wordpress
Network=feret-private
PublishPort=127.0.0.1:8086:80
EnvironmentFile={private}/wordpress.env
Volume={wp}:/var/www/html
Volume={base}/code/wp-content/themes/feret-peinture:/var/www/html/wp-content/themes/feret-peinture:ro
Volume={base}/code/wp-content/plugins/feret-peinture-core:/var/www/html/wp-content/plugins/feret-peinture-core:ro
Volume={base}/code/config/pods:/var/www/html/config/pods:ro
[Service]
Restart=always
TimeoutStartSec=180
[Install]
WantedBy=multi-user.target
''', 0o644)
(base / 'database').mkdir(mode=0o700, exist_ok=True)
print('Private configuration prepared. Secrets are stored in private/access.txt.')
