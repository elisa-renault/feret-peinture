<?php

defined( 'ABSPATH' ) || exit;



function fp_launch_errors(): array {

    $errors = [];

    foreach ( [ 'FP_LAUNCH_APPROVED', 'FP_CONTACT_APPROVED', 'FP_SERVICES_APPROVED', 'FP_LEGAL_APPROVED', 'FP_PRIVACY_APPROVED', 'FP_MAIL_DELIVERY_VERIFIED' ] as $flag ) {

        if ( ! fp_approved( $flag ) ) { $errors[] = $flag . ' : validation manquante.'; }

    }

    if ( ! function_exists( 'pods' ) || 3 !== count( fp_schema() ) ) { $errors[] = 'Configuration métier ou Pods indisponible.'; }

    if ( ! is_email( fp_info( 'public_email' ) ) ) { $errors[] = 'Email public valide manquant.'; }

    if ( ! function_exists( 'fp_quote_ready' ) || ! fp_quote_ready() ) { $errors[] = 'Configuration d’envoi et d’information sur les données incomplète.'; }

    foreach ( [ 'mentions-legales' ] as $slug ) {

        $page = get_page_by_path( $slug );

        if ( ! $page || 'publish' !== $page->post_status || ! get_post_meta( $page->ID, '_fp_legal_approved', true ) || strlen( wp_strip_all_tags( $page->post_content ) ) < 150 ) {

            $errors[] = 'Page ' . $slug . ' : version définitive validée manquante.';

        }

    }

    return array_values( array_unique( $errors ) );

}



function fp_public_content_allowed( WP_Post $post ): bool {

    if ( 'fp_service' === $post->post_type ) {

        return ( fp_is_preview() || fp_approved( 'FP_SERVICES_APPROVED' ) ) && '1' === (string) get_post_meta( $post->ID, 'visible', true );

    }

    if ( 'fp_project' === $post->post_type ) {

        return '1' === (string) get_post_meta( $post->ID, 'publication_authorized', true ) && wp_attachment_is_image( get_post_thumbnail_id( $post->ID ) ) && ( fp_is_preview() || ! metadata_exists( 'post', $post->ID, '_fp_demo' ) );

    }

    return 'fp_information' !== $post->post_type;

}



add_action( 'template_redirect', static function () {

    if ( ! fp_is_preview() && fp_launch_errors() ) {

        status_header( 503 );

        nocache_headers();

        header( 'Retry-After: 3600' );

        header( 'X-Robots-Tag: noindex, nofollow', true );

        echo '<!doctype html><html lang="fr"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Feret Peinture</title><style>body{background:#F6F3EC;color:#282E2D;font:1.2rem/1.6 system-ui;margin:0;padding:12vh 8vw}main{max-width:680px}h1{font:3rem Georgia,serif;color:#23423E}p{max-width:40ch}</style><main><h1>Feret Peinture</h1><p>Le site est en préparation. Merci de votre visite.</p></main></html>';

        exit;

    }

    if ( is_singular( [ 'fp_project', 'fp_service' ] ) ) {

        $post = get_queried_object();

        $editor_preview = is_preview() && current_user_can( 'edit_post', $post->ID );

        $production_demo = ! fp_is_preview() && metadata_exists( 'post', $post->ID, '_fp_demo' );

        if ( $production_demo || ( ! $editor_preview && ! fp_public_content_allowed( $post ) ) ) {

            global $wp_query;

            $wp_query->set_404(); status_header( 404 ); nocache_headers();

        }

    }

    if ( ( is_page( 'realisations' ) || is_post_type_archive( 'fp_project' ) ) && ! fp_has_projects() && ! fp_is_preview() ) {

        global $wp_query;

        $wp_query->set_404(); status_header( 404 ); nocache_headers();

    }

    if ( is_feed() || is_author() || is_date() || is_search() || is_attachment() ) {

        global $wp_query;

        $wp_query->set_404(); status_header( 404 );

    }

}, -100 );



add_action( 'send_headers', static function () {

    header( 'X-Content-Type-Options: nosniff' );

    header( 'Referrer-Policy: strict-origin-when-cross-origin' );

    if ( fp_is_preview() ) { header( 'X-Robots-Tag: noindex, nofollow', true ); }

} );



add_filter( 'wp_robots', static function ( $robots ) {

    if ( fp_is_preview() || ! fp_approved( 'FP_LAUNCH_APPROVED' ) || is_page( 'merci' ) ) {

        $robots['noindex'] = true;

        $robots['nofollow'] = true;

        unset( $robots['index'], $robots['follow'] );

    }

    return $robots;

} );



add_filter( 'wp_sitemaps_enabled', static fn( $enabled ) => $enabled && ! fp_is_preview() && empty( fp_launch_errors() ) );

// Virtual XML routes have no page record. Let the native renderer handle them
// instead of inheriting the main query's 404 on this static-page-only site.
add_filter( 'pre_handle_404', static function ( $preempt, $query ) {
    $sitemap = $query->get( 'sitemap' );
    $stylesheet = $query->get( 'sitemap-stylesheet' );
    if ( ! $sitemap && ! $stylesheet ) { return $preempt; }
    $server = wp_sitemaps_get_server();
    if ( ! $server->sitemaps_enabled() ) { return $preempt; }
    $known = $stylesheet
        ? in_array( $stylesheet, array( 'index', 'sitemap' ), true )
        : ( 'index' === $sitemap || ( is_string( $sitemap ) && $server->registry->get_provider( $sitemap ) ) );
    if ( ! $known ) { return $preempt; }
    $query->is_404 = false;
    status_header( 200 );
    // The renderer still returns 404 for an empty or out-of-range child sitemap.
    return true;
}, 10, 2 );

add_filter( 'wp_sitemaps_add_provider', static fn( $provider, $name ) => in_array( $name, [ 'users', 'taxonomies' ], true ) ? false : $provider, 10, 2 );

add_filter( 'wp_sitemaps_post_types', static function ( $types ) {

    return array_intersect_key( $types, array_flip( [ 'page', 'fp_service', 'fp_project' ] ) );

} );

add_filter( 'wp_sitemaps_posts_query_args', static function ( $args, $type ) {

    if ( 'page' === $type ) {

        $excluded = [];

        foreach ( [ 'merci', 'realisations', 'confidentialite' ] as $slug ) {

            if ( 'realisations' === $slug && fp_has_projects() ) { continue; }

            $page = get_page_by_path( $slug );

            if ( $page ) { $excluded[] = $page->ID; }

        }

        $args['post__not_in'] = $excluded;

    }

    if ( 'fp_project' === $type ) {

        $args['meta_query'] = [ [ 'key' => 'publication_authorized', 'value' => '1' ], [ 'key' => '_fp_demo', 'compare' => 'NOT EXISTS' ] ];

        $args['post__in'] = array_map( static fn( $post ) => $post->ID, fp_projects( 100 ) ) ?: [ 0 ];

    }

    if ( 'fp_service' === $type ) { $args['meta_query'] = [ [ 'key' => 'visible', 'value' => '1' ] ]; }

    return $args;

}, 10, 2 );



add_filter( 'robots_txt', static function ( $output ) {

    return fp_is_preview() || ! fp_approved( 'FP_LAUNCH_APPROVED' ) ? "User-agent: *\nDisallow: /\n" : $output;

} );



add_action( 'wp_head', static function () {

    // The project has no external SEO dependency. Disable its JSON-LD if an SEO

    // plugin is deliberately installed later, so Aliant can choose one owner.

    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || fp_is_preview() || ! empty( fp_launch_errors() ) ) { return; }

    $data = [

        '@context' => 'https://schema.org', '@type' => 'HousePainter',

        '@id' => home_url( '/#entreprise' ), 'name' => 'Feret Peinture',

        'legalName' => 'CHRISTOPHE FERET', 'url' => home_url( '/' ),

    ];

    $areas = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', fp_info( 'confirmed_area' ) ) ) ) );

    if ( $areas ) { $data['areaServed'] = $areas; }

    if ( fp_info( 'public_email' ) ) { $data['email'] = fp_info( 'public_email' ); }

    // Street-level address only if separately verified and visible in legal text.

    $address = defined( 'FP_BUSINESS_ADDRESS_JSON' ) ? FP_BUSINESS_ADDRESS_JSON : getenv( 'FP_BUSINESS_ADDRESS_JSON' );

    $address = is_string( $address ) ? json_decode( $address, true ) : null;

    if ( is_array( $address ) && ! empty( $address['streetAddress'] ) ) {

        $legal = get_page_by_path( 'mentions-legales' );

        if ( $legal && false !== mb_stripos( wp_strip_all_tags( $legal->post_content ), $address['streetAddress'] ) ) {

            $allowed = array_intersect_key( $address, array_flip( [ 'streetAddress', 'addressLocality', 'postalCode', 'addressCountry' ] ) );

            $data['address'] = array_merge( [ '@type' => 'PostalAddress' ], $allowed );

        }

    }

    echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>' . "\n";

}, 40 );



// Preserve direct links to the former standalone privacy page.

add_action('template_redirect', static function () {

    if (is_page('confidentialite')) {

        wp_safe_redirect(home_url('/mentions-legales/#confidentialite'), 301);

        exit;

    }

}, -20);
