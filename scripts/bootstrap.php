<?php
/** Run with `wp eval-file /project/scripts/bootstrap.php`; never serves HTTP. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
if ( ! function_exists( 'pods' ) || ! function_exists( 'fp_register_pods' ) || 3 !== count( fp_schema() ) ) {
    WP_CLI::error( 'Activez Pods et Feret Peinture - métier et rendez config/pods/types.php accessible avant le bootstrap.' );
}

fp_install_roles();

/** Never overwrite an existing item, even after its title/slug was edited. */
if ( ! function_exists( 'fp_seed_post' ) ) {
function fp_seed_post( string $seed_key, array $post ): array {
    $map = get_option( 'fp_seed_ids', [] );
    if ( isset( $map[$seed_key] ) ) { return [ (int) $map[$seed_key], false ]; }
    $existing = get_posts( [ 'post_type' => $post['post_type'], 'name' => $post['post_name'], 'post_status' => [ 'publish', 'draft', 'private', 'pending', 'future', 'trash' ], 'numberposts' => 1, 'suppress_filters' => true ] );
    if ( $existing ) {
        $map[$seed_key] = $existing[0]->ID;
        update_option( 'fp_seed_ids', $map, false );
        return [ (int) $existing[0]->ID, false ];
    }
    $admins = get_users( [ 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ] );
    $post['post_author'] = $admins ? (int) $admins[0] : get_current_user_id();
    $post['comment_status'] = 'closed';
    $post['ping_status'] = 'closed';
    $id = wp_insert_post( wp_slash( $post ), true );
    if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
    $map[$seed_key] = (int) $id;
    update_option( 'fp_seed_ids', $map, false );
    update_post_meta( $id, '_fp_seed', $seed_key );
    return [ (int) $id, true ];
}
}

$pages = [
    'accueil' => 'Peintre en bâtiment à Écouen',
    'entreprise' => 'Christophe Feret - Peintre à Écouen',
    'zone-intervention' => 'Votre projet et sa localisation',
    'devis' => 'Demander un devis',
    'mentions-legales' => 'Mentions légales',
    'confidentialite' => 'Confidentialité',
    'merci' => 'Votre demande de devis',
];
foreach ( $pages as $slug => $title ) {
    [ $id, $new ] = fp_seed_post( 'page:' . $slug, [ 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $title, 'post_content' => '' ] );
    if ( 'accueil' === $slug ) { $front_id = $id; }
    if ( $new && in_array( $slug, [ 'mentions-legales', 'confidentialite' ], true ) ) { update_post_meta( $id, '_fp_legal_approved', 0 ); }
}

$services = fp_service_seed();
foreach ( $services as $slug => $copy ) {
    [ $id, $new ] = fp_seed_post( 'service:' . $slug, [ 'post_type' => 'fp_service', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $copy['title'], 'post_excerpt' => $copy['excerpt'], 'post_content' => $copy['content'], 'menu_order' => array_search( $slug, array_keys( $services ), true ) ] );
    if ( $new ) { pods( 'fp_service', $id )->save( [ 'visible' => 1 ] ); }
}

[ $information_id, $new ] = fp_seed_post( 'information', [ 'post_type' => 'fp_information', 'post_status' => 'publish', 'post_name' => 'mes-informations', 'post_title' => 'Mes informations', 'post_content' => '' ] );
if ( $new ) {
    pods( 'fp_information', $information_id )->save( [
        'phone_mobile' => '', 'phone_landline' => '',
        'public_email' => '',
        'presentation' => 'Christophe Feret est implanté à Écouen, dans le Val-d’Oise. L’entreprise exerce une activité de travaux de peinture.',
        'confirmed_area' => '', 'temporary_message' => '',
    ] );
}

if ( ! get_option( 'fp_bootstrap_done' ) ) {
    update_option( 'blogname', 'Feret Peinture' );
    update_option( 'blogdescription', 'Christophe Feret - Peintre en bâtiment à Écouen' );
    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_id );
    update_option( 'permalink_structure', '/%postname%/' );
    update_option( 'timezone_string', 'Europe/Paris' );
    update_option( 'date_format', 'j F Y' );
    update_option( 'time_format', 'H:i' );
    update_option( 'default_comment_status', 'closed' );
    update_option( 'default_ping_status', 'closed' );
    update_option( 'WPLANG', 'fr_FR' );
    update_option( 'blog_public', 0 );
    // Do not delete a pre-existing WordPress post or page: an existing site may
    // contain real content. The thème has no blog routes; review migration first.
    update_option( 'fp_bootstrap_done', FP_CORE_VERSION, false );
}

if ( filter_var( getenv( 'FP_IMPORT_DEMO' ), FILTER_VALIDATE_BOOLEAN ) ) {
    if ( ! fp_is_preview() ) { WP_CLI::error( 'Les données de démonstration sont interdites en production.' ); }
    [ $demo_id, $new ] = fp_seed_post( 'demo:chantier', [
        'post_type' => 'fp_project', 'post_status' => 'draft', 'post_name' => 'demonstration-chantier',
        'post_title' => 'DÉMONSTRATION : exemple de fiche chantier',
        'post_content' => '<p>Fiche de démonstration réservée au développement. Elle ne représente pas un chantier de Christophe Feret. Utilisez l’aperçu WordPress pour tester la fiche ; remplacez les données par des travaux authentiques avant toute publication.</p>',
    ] );
    if ( $new ) {
        update_post_meta( $demo_id, '_fp_demo', 1 );
        pods( 'fp_project', $demo_id )->save( [ 'town' => 'COMMUNE DE TEST', 'short_description' => 'Données de test : aucun chantier réel.', 'publication_authorized' => 0, 'featured' => 0 ] );
    }
    WP_CLI::log( 'Brouillon de démonstration disponible dans Mes chantiers. Aucune photographie fictive n’a été ajoutée.' );
}

flush_rewrite_rules();
WP_CLI::success( 'Bootstrap terminé : contenu existant conservé, Pods configurés et rôle Christophe installé. Site prêt à prévisualiser ; validations de publication toujours requises.' );
