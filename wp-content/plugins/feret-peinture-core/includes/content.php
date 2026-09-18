<?php
defined( 'ABSPATH' ) || exit;

function fp_schema(): array {
    static $schema;
    if ( null === $schema ) {
        $file = dirname( FP_CORE_PATH, 3 ) . '/config/pods/types.php';
        // Deploy the config directory alongside wp-content (see compose.yaml).
        $schema = is_file( $file ) ? require $file : [];
    }
    return $schema;
}

function fp_service_seed(): array {
    $file = dirname( FP_CORE_PATH, 3 ) . '/config/pods/services.php';
    return is_file( $file ) ? require $file : [];
}

function fp_service_block_template(): array {
    return [
        [ 'feret/service-content', [ 'lock' => [ 'move' => true, 'remove' => true ] ] ],
        [ 'feret/service-note', [ 'lock' => [ 'move' => true, 'remove' => true ] ] ],
    ];
}

function fp_register_content(): void {
    $types = [
        'fp_project' => [ 'Mes chantiers', 'Chantier', 'realisations', 'dashicons-format-gallery' ],
        'fp_service' => [ 'Mes prestations', 'Prestation', 'prestations', 'dashicons-admin-customizer' ],
        'fp_information' => [ 'Mes informations', 'Informations', false, 'dashicons-id-alt' ],
    ];
    foreach ( $types as $name => $labels ) {
        $is_info = 'fp_information' === $name;
        $item_labels = [
            'fp_project' => [ 'Ajouter un chantier', 'Modifier le chantier', 'Nouveau chantier', 'Rechercher un chantier', 'Aucun chantier trouvé.' ],
            'fp_service' => [ 'Ajouter une prestation', 'Modifier la prestation', 'Nouvelle prestation', 'Rechercher une prestation', 'Aucune prestation trouvée.' ],
            'fp_information' => [ 'Ajouter des informations', 'Modifier mes informations', 'Nouvelles informations', 'Rechercher des informations', 'Aucune fiche trouvée.' ],
        ][$name];
        register_post_type( $name, [
            'labels' => [
                'name' => $labels[0], 'singular_name' => $labels[1], 'menu_name' => $labels[0],
                'add_new' => 'Ajouter', 'add_new_item' => $item_labels[0],
                'edit_item' => $item_labels[1],
                'new_item' => $item_labels[2], 'view_item' => 'Voir sur le site',
                'search_items' => $item_labels[3], 'not_found' => $item_labels[4],
                'not_found_in_trash' => 'La corbeille est vide.', 'all_items' => $labels[0],
                'featured_image' => 'Photo principale', 'set_featured_image' => 'Choisir la photo principale',
                'remove_featured_image' => 'Retirer la photo principale', 'use_featured_image' => 'Utiliser cette photo',
            ],
            'public' => ! $is_info, 'publicly_queryable' => ! $is_info,
            'exclude_from_search' => true, 'show_ui' => true, 'show_in_rest' => 'fp_service' === $name,
            'show_in_menu' => ! $is_info, 'show_in_nav_menus' => ! $is_info, 'has_archive' => $is_info ? false : $labels[2],
            'rewrite' => $is_info ? false : [ 'slug' => $labels[2], 'with_front' => false ],
            'query_var' => ! $is_info, 'menu_icon' => $labels[3], 'menu_position' => 5 + array_search( $name, array_keys( $types ), true ),
            'capability_type' => [ $name, $name . 's' ], 'map_meta_cap' => true,
            'capabilities' => [ 'create_posts' => 'create_' . $name . 's' ],
            'supports' => $is_info ? [ 'title', 'revisions' ] : [ 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ],
            'template' => 'fp_service' === $name ? fp_service_block_template() : [],
            'template_lock' => 'fp_service' === $name ? 'all' : false,
        ] );
    }
}
add_action( 'init', 'fp_register_content', 5 );

function fp_register_pods(): void {
    if ( ! function_exists( 'pods_register_type' ) ) {
        return;
    }
    foreach ( fp_schema() as $name => $definition ) {
        pods_register_type( 'post_type', $name, [
            'name' => $name, 'label' => $definition['label'], 'type' => 'post_type',
            'object' => $name, 'storage' => 'meta', 'show_in_rest' => 0,
            'supports_revisions' => 1,
        ] );
        $fields = [];
        foreach ( $definition['fields'] as $field => $options ) {
            $fields[$field] = array_merge( [ 'name' => $field, 'weight' => count( $fields ), 'rest_read' => 0, 'rest_write' => 0 ], $options );
        }
        pods_register_group( [ 'name' => 'fp_details', 'label' => $definition['label'], 'weight' => 0 ], $name, $fields );
    }
}
add_action( 'init', 'fp_register_pods', 10 );

function fp_is_preview(): bool {
    return 'production' !== wp_get_environment_type();
}

function fp_approved( string $name ): bool {
    return filter_var( defined( $name ) ? constant( $name ) : getenv( $name ), FILTER_VALIDATE_BOOLEAN );
}

function fp_information_id(): int {
    $ids = get_posts( [ 'post_type' => 'fp_information', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC', 'suppress_filters' => true ] );
    return $ids ? (int) $ids[0] : 0;
}

function fp_info( string $key, $default = '' ) {
    $keys = array_keys( fp_schema()['fp_information']['fields'] ?? [] );
    if ( ! in_array( $key, $keys, true ) ) {
        return $default;
    }
    if ( ! fp_is_preview() && in_array( $key, [ 'phone_mobile', 'phone_landline', 'public_email' ], true ) && ! fp_approved( 'FP_CONTACT_APPROVED' ) ) {
        return $default;
    }
    $id = fp_information_id();
    $value = $id ? get_post_meta( $id, $key, true ) : '';
    return is_scalar( $value ) && '' !== (string) $value ? (string) $value : $default;
}

function fp_get_info( string $key, $default = '' ) { return fp_info( $key, $default ); }

// Mobile publication approved by Elisa; never fall back to the private landline.
function fp_phone_display(): string { return fp_info( 'phone_mobile' ); }

function fp_phone_uri(): string {
    $phone = preg_replace( '/[^0-9+]/', '', fp_phone_display() );
    if ( preg_match( '/^0[1-9][0-9]{8}$/', $phone ) ) {
        $phone = '+33' . substr( $phone, 1 );
    }
    return preg_match( '/^\+?[0-9]{8,15}$/', $phone ) ? 'tel:' . $phone : '';
}

// Use in Aliant's validated legal page instead of duplicating editable contacts.
add_shortcode( 'fp_contact_details', static function () {
    $links = [];
    if ( fp_phone_uri() ) { $links[] = '<a href="' . esc_url( fp_phone_uri() ) . '">' . esc_html( fp_phone_display() ) . '</a>'; }
    if ( is_email( fp_info( 'public_email' ) ) ) { $links[] = '<a href="mailto:' . esc_attr( fp_info( 'public_email' ) ) . '">' . esc_html( fp_info( 'public_email' ) ) . '</a>'; }
    return $links ? '<p>' . implode( '<br>', $links ) . '</p>' : '';
} );

function fp_services(): array {
    if ( ! fp_is_preview() && ! fp_approved( 'FP_SERVICES_APPROVED' ) ) {
        return [];
    }
    return get_posts( [ 'post_type' => 'fp_service', 'numberposts' => 4, 'post_status' => 'publish', 'orderby' => 'menu_order ID', 'order' => 'ASC', 'meta_query' => [ [ 'key' => 'visible', 'value' => '1' ] ] ] );
}

function fp_projects( int $limit = 6 ): array {
    $query = [
        'post_type' => 'fp_project', 'post_status' => 'publish', 'numberposts' => max( 1, min( $limit, 100 ) ),
        'orderby' => 'date', 'order' => 'DESC',
        'meta_query' => [ [ 'key' => 'publication_authorized', 'value' => '1' ] ],
    ];
    if ( ! fp_is_preview() ) {
        $query['meta_query'][] = [ 'key' => '_fp_demo', 'compare' => 'NOT EXISTS' ];
    }
    // Featured projects come first; missing legacy metadata must not hide a project.
    $query['numberposts'] = 100;
    $posts = get_posts( $query );
    $posts = array_values( array_filter( $posts, static fn( $post ) => wp_attachment_is_image( get_post_thumbnail_id( $post->ID ) ) ) );
    usort( $posts, static function ( $a, $b ) {
        $featured = (int) get_post_meta( $b->ID, 'featured', true ) <=> (int) get_post_meta( $a->ID, 'featured', true );
        return $featured ?: strcmp( $b->post_date, $a->post_date );
    } );
    return array_slice( $posts, 0, max( 1, min( $limit, 100 ) ) );
}

function fp_has_projects(): bool { return (bool) fp_projects( 1 ); }

function fp_gallery( int $post_id, string $key = 'gallery' ): array {
    if ( ! in_array( $key, [ 'gallery', 'before_photo', 'after_photo' ], true ) ) {
        return [];
    }
    $value = function_exists( 'pods' ) ? pods( 'fp_project', $post_id )->field( $key ) : get_post_meta( $post_id, $key, true );
    if ( ! $value ) { return []; }
    if ( is_numeric( $value ) ) { return wp_attachment_is_image( (int) $value ) ? [ (int) $value ] : []; }
    if ( isset( $value['ID'] ) ) { return wp_attachment_is_image( (int) $value['ID'] ) ? [ (int) $value['ID'] ] : []; }
    $ids = [];
    foreach ( (array) $value as $image ) {
        $id = is_array( $image ) ? (int) ( $image['ID'] ?? 0 ) : (int) $image;
        if ( $id && wp_attachment_is_image( $id ) ) { $ids[] = $id; }
    }
    return array_values( array_unique( $ids ) );
}

function fp_image_id( int $post_id, string $key ): int { return fp_gallery( $post_id, $key )[0] ?? 0; }

// Server-side sanitation also covers direct post-meta writes via trusted integrations.
foreach ( [ 'phone_mobile', 'phone_landline' ] as $fp_phone_key ) {
    add_filter( 'sanitize_post_meta_' . $fp_phone_key, static function ( $value ) {
        $value = preg_replace( '/[^0-9+ ().-]/u', '', (string) $value );
        $digits = preg_replace( '/[^0-9]/', '', $value );
        return strlen( $digits ) >= 8 && strlen( $digits ) <= 15 ? $value : '';
    } );
}
add_filter( 'sanitize_post_meta_public_email', 'sanitize_email' );
foreach ( [ 'presentation', 'confirmed_area', 'short_description', 'town' ] as $fp_text_key ) {
    add_filter( 'sanitize_post_meta_' . $fp_text_key, 'sanitize_textarea_field' );
}

add_filter( 'big_image_size_threshold', static fn() => 2200 );
add_filter( 'jpeg_quality', static fn() => 84 );
add_filter( 'wp_editor_set_quality', static fn() => 84 );
add_filter( 'upload_mimes', static function ( $mimes ) {
    if ( fp_is_christophe() ) {
        // These raster formats are verified by WordPress and the server image editor.
        return array_intersect_key( $mimes, array_flip( [ 'jpg|jpeg|jpe', 'png', 'webp' ] ) );
    }
    return $mimes;
} );
