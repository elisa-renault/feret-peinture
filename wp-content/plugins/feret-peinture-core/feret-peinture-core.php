<?php
/**
 * Plugin Name: Feret Peinture — métier
 * Description: Contenus Pods, accès Christophe, garde-fous de publication et demandes de devis.
 * Version: 1.0.0
 * Requires at least: 6.8
 * Requires PHP: 8.2
 * Requires Plugins: pods
 * License: GPL-2.0-or-later
 * Text Domain: feret-peinture
 */
defined( 'ABSPATH' ) || exit;

define( 'FP_CORE_VERSION', '1.0.0' );
define( 'FP_CORE_PATH', __DIR__ );
require_once __DIR__ . '/includes/content.php';
require_once __DIR__ . '/includes/revisions.php';
require_once __DIR__ . '/includes/access.php';
require_once __DIR__ . '/includes/publication.php';
if ( is_file( __DIR__ . '/includes/form.php' ) ) {
    require_once __DIR__ . '/includes/form.php';
}

register_activation_hook( __FILE__, function () {
    fp_register_content();
    fp_install_roles();
    flush_rewrite_rules();
} );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
