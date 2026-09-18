<?php
defined( 'ABSPATH' ) || exit;

function fp_is_christophe( ?int $user_id = null ): bool {
    $user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
    return $user && in_array( 'fp_christophe', (array) $user->roles, true ) && ! in_array( 'administrator', (array) $user->roles, true );
}

function fp_install_roles(): void {
    $caps = [ 'read' => true, 'upload_files' => true ];
    foreach ( [ 'fp_project', 'fp_service', 'fp_information' ] as $type ) {
        foreach ( [ 'edit_', 'edit_others_', 'edit_published_', 'publish_', 'read_private_', 'edit_private_' ] as $prefix ) {
            $caps[$prefix . $type . 's'] = true;
        }
    }
    $caps['create_fp_projects'] = true;
    foreach ( [ 'delete_', 'delete_others_', 'delete_published_', 'delete_private_' ] as $prefix ) {
        $caps[$prefix . 'fp_projects'] = true;
    }
    if ( ! get_role( 'fp_christophe' ) ) { add_role( 'fp_christophe', 'Christophe Feret - contenus du site', $caps ); }
    $role = get_role( 'fp_christophe' );
    foreach ( array_keys( $role->capabilities ) as $cap ) {
        if ( ! isset( $caps[$cap] ) ) { $role->remove_cap( $cap ); }
    }
    foreach ( $caps as $cap => $allowed ) { $role->add_cap( $cap, $allowed ); }
    $admin = get_role( 'administrator' );
    if ( $admin ) {
        foreach ( [ 'fp_project', 'fp_service', 'fp_information' ] as $type ) {
            $object = get_post_type_object( $type );
            if ( $object ) {
                foreach ( (array) $object->cap as $cap ) {
                    if ( 'do_not_allow' !== $cap ) { $admin->add_cap( $cap ); }
                }
            }
        }
    }
    update_option( 'fp_role_version', FP_CORE_VERSION, false );
}
add_action( 'init', static function () {
    if ( FP_CORE_VERSION !== get_option( 'fp_role_version' ) ) { fp_install_roles(); }
}, 30 );

// Enforce editing limits independently from hidden menus, including forged requests.
add_filter( 'map_meta_cap', static function ( $caps, $cap, $user_id, $args ) {
    if ( ! fp_is_christophe( (int) $user_id ) ) { return $caps; }
    if ( in_array( $cap, [ 'edit_post', 'delete_post', 'read_post' ], true ) && ! empty( $args[0] ) ) {
        $post = get_post( (int) $args[0] );
        if ( $post && 'revision' === $post->post_type ) { $post = get_post( $post->post_parent ); }
        if ( ! $post ) { return [ 'do_not_allow' ]; }
        if ( 'attachment' === $post->post_type ) {
            return ( 'read_post' === $cap || (int) $post->post_author === (int) $user_id ) ? [ 'upload_files' ] : [ 'do_not_allow' ];
        }
        if ( ! isset( fp_schema()[$post->post_type] ) ) { return [ 'do_not_allow' ]; }
        if ( 'fp_information' === $post->post_type && $post->ID !== fp_information_id() ) { return [ 'do_not_allow' ]; }
        if ( 'delete_post' === $cap && 'fp_project' !== $post->post_type ) { return [ 'do_not_allow' ]; }
    }
    return $caps;
}, 30, 4 );

add_filter( 'wp_insert_post_data', static function ( $data, $postarr ) {
    if ( ! fp_is_christophe() || empty( $postarr['ID'] ) ) { return $data; }
    $original = get_post( (int) $postarr['ID'] );
    if ( $original && in_array( $original->post_type, [ 'fp_service', 'fp_information' ], true ) ) {
        $data['post_type'] = $original->post_type;
        $data['post_name'] = $original->post_name;
        $data['post_title'] = $original->post_title;
        $data['post_parent'] = 0;
        $data['menu_order'] = $original->menu_order;
        if ( 'fp_information' === $original->post_type ) { $data['post_status'] = $original->post_status; }
    }
    return $data;
}, 20, 2 );

function fp_information_panel_fields(): array {
    return [ 'phone_mobile', 'phone_landline', 'public_email', 'presentation', 'confirmed_area' ];
}

function fp_information_panel_url( array $args = [] ): string {
    return add_query_arg( $args, admin_url( 'admin.php?page=fp-site-information' ) );
}

function fp_register_information_panel(): void {
    add_menu_page(
        'Mes informations',
        'Mes informations',
        'edit_fp_informations',
        'fp-site-information',
        'fp_render_information_panel',
        'dashicons-id-alt',
        7
    );
}
add_action( 'admin_menu', 'fp_register_information_panel', 20 );

function fp_render_information_panel(): void {
    $id = fp_information_id();
    if ( ! $id || ! current_user_can( 'edit_post', $id ) ) {
        wp_die( 'Vous ne pouvez pas modifier ces informations.', 'Accès réservé', [ 'response' => 403 ] );
    }
    $schema = fp_schema()['fp_information']['fields'] ?? [];
    ?>
    <div class="wrap fp-information-panel">
        <h1>Mes informations</h1>
        <p>Modifiez ici les éléments récurrents du site. Les changements sont enregistrés dans une révision et se répercutent sur les pages concernées.</p>
        <?php if ( isset( $_GET['updated'] ) && '1' === $_GET['updated'] ) : ?>
            <div class="notice notice-success is-dismissible"><p>Vos informations sont enregistrées.</p></div>
        <?php endif; ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'fp_save_site_information', 'fp_information_nonce' ); ?>
            <input type="hidden" name="action" value="fp_save_site_information">
            <table class="form-table" role="presentation">
                <tbody>
                <?php foreach ( fp_information_panel_fields() as $key ) :
                    if ( empty( $schema[ $key ] ) ) { continue; }
                    $field = $schema[ $key ];
                    $value = get_post_meta( $id, $key, true );
                    ?>
                    <tr>
                        <th scope="row"><label for="fp-information-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
                        <td>
                            <?php if ( 'paragraph' === $field['type'] ) : ?>
                                <textarea class="large-text" rows="5" id="fp-information-<?php echo esc_attr( $key ); ?>" name="fp_information[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $value ); ?></textarea>
                            <?php else : ?>
                                <input class="regular-text" type="<?php echo 'public_email' === $key ? 'email' : 'text'; ?>" id="fp-information-<?php echo esc_attr( $key ); ?>" name="fp_information[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>">
                            <?php endif; ?>
                            <?php if ( ! empty( $field['description'] ) ) : ?><p class="description"><?php echo esc_html( $field['description'] ); ?></p><?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php submit_button( 'Enregistrer mes informations' ); ?>
        </form>
        <p><a href="<?php echo esc_url( home_url( '/entreprise/' ) ); ?>" target="_blank" rel="noopener">Voir la page Entreprise</a></p>
    </div>
    <?php
}

add_action( 'admin_post_fp_save_site_information', static function () {
    $id = fp_information_id();
    if ( ! $id || ! current_user_can( 'edit_post', $id ) ) {
        wp_die( 'Vous ne pouvez pas modifier ces informations.', 'Accès réservé', [ 'response' => 403 ] );
    }
    check_admin_referer( 'fp_save_site_information', 'fp_information_nonce' );
    if ( ! function_exists( 'pods' ) ) {
        wp_die( 'Les champs ne sont pas disponibles. Contactez Aliant avant de réessayer.', 'Enregistrement impossible', [ 'response' => 503 ] );
    }
    $submitted = isset( $_POST['fp_information'] ) && is_array( $_POST['fp_information'] ) ? wp_unslash( $_POST['fp_information'] ) : [];
    $values = [];
    foreach ( fp_information_panel_fields() as $key ) {
        $raw = isset( $submitted[ $key ] ) ? $submitted[ $key ] : '';
        $values[ $key ] = sanitize_meta( $key, is_scalar( $raw ) ? $raw : '', 'post' );
    }
    pods( 'fp_information', $id )->save( $values );
    wp_safe_redirect( fp_information_panel_url( [ 'updated' => '1' ] ) );
    exit;
} );

add_action( 'admin_menu', static function () {
    if ( ! fp_is_christophe() ) { return; }
    global $menu;
    $allowed = [ 'edit.php?post_type=fp_project', 'edit.php?post_type=fp_service', 'fp-site-information' ];
    foreach ( $menu as $item ) {
        if ( ! in_array( $item[2], $allowed, true ) ) { remove_menu_page( $item[2] ); }
    }
    // WP removes single-item submenus. For a role without core edit_posts, that
    // loses the CPT parent and incorrectly denies the native list screen. A
    // useful second link keeps its native parent/capability mapping intact.
    add_submenu_page( 'edit.php?post_type=fp_service', 'Voir les prestations', 'Voir sur le site', 'edit_fp_services', home_url( '/prestations/' ) );
}, 999 );

add_action( 'admin_init', static function () {
    if ( ! fp_is_christophe() || wp_doing_ajax() ) { return; }
    global $pagenow;
    if ( 'index.php' === $pagenow ) {
        wp_safe_redirect( admin_url( 'edit.php?post_type=fp_project' ) ); exit;
    }
    $allowed = [ 'admin.php', 'edit.php', 'post.php', 'post-new.php', 'profile.php', 'user-edit.php', 'upload.php', 'media-new.php', 'media.php', 'async-upload.php', 'admin-post.php', 'revision.php' ];
    if ( ! in_array( $pagenow, $allowed, true ) ) {
        wp_die( 'Cet écran est réservé à Aliant. Vous pouvez modifier vos chantiers, prestations et informations.', 'Accès réservé', [ 'response' => 403 ] );
    }
    if ( in_array( $pagenow, [ 'edit.php', 'post-new.php' ], true ) ) {
        $type = sanitize_key( $_GET['post_type'] ?? 'post' );
        if ( ! isset( fp_schema()[$type] ) || ( 'post-new.php' === $pagenow && 'fp_project' !== $type ) ) {
            wp_die( 'Cet écran est réservé à Aliant.', 'Accès réservé', [ 'response' => 403 ] );
        }
        if ( 'edit.php' === $pagenow && 'fp_information' === $type && fp_information_id() ) {
            wp_safe_redirect( admin_url( 'post.php?post=' . fp_information_id() . '&action=edit' ) ); exit;
        }
    }
    if ( 'admin.php' === $pagenow && 'fp-site-information' !== ( $_GET['page'] ?? '' ) ) {
        wp_die( 'Cet écran est réservé à Aliant.', 'Accès réservé', [ 'response' => 403 ] );
    }
} );

add_filter( 'login_redirect', static function ( $url, $requested, $user ) {
    return $user instanceof WP_User && in_array( 'fp_christophe', (array) $user->roles, true ) ? admin_url( 'edit.php?post_type=fp_project' ) : $url;
}, 10, 3 );
add_filter( 'show_admin_bar', static fn( $show ) => fp_is_christophe() ? false : $show );
add_filter( 'use_block_editor_for_post_type', static fn( $use, $type ) => isset( fp_schema()[$type] ) ? false : $use, 10, 2 );
add_filter( 'post_row_actions', static function ( $actions, $post ) {
    if ( isset( fp_schema()[$post->post_type] ) ) { unset( $actions['inline hide-if-no-js'] ); }
    return $actions;
}, 10, 2 );

add_action( 'add_meta_boxes', static function ( $type ) {
    if ( ! isset( fp_schema()[$type] ) ) { return; }
    foreach ( [ 'slugdiv', 'authordiv', 'postcustom', 'commentstatusdiv', 'commentsdiv', 'trackbacksdiv' ] as $box ) {
        remove_meta_box( $box, $type, 'normal' );
    }
    if ( 'fp_project' === $type ) { remove_meta_box( 'postexcerpt', $type, 'normal' ); }
} );

add_action( 'admin_notices', static function () {
    $screen = get_current_screen();
    if ( ! $screen || ! isset( fp_schema()[$screen->post_type] ) ) { return; }
    if ( ! function_exists( 'pods' ) || ! fp_schema() ) {
        echo '<div class="notice notice-error"><p>Les champs de cette fiche ne sont pas disponibles. Contactez Aliant avant de la modifier.</p></div>'; return;
    }
    if ( 'post' !== $screen->base ) { return; }
    $save_hint = 'Cliquez sur Mettre à jour pour enregistrer vos modifications.';
    if ( 'fp_information' !== $screen->post_type && ! in_array( get_post_status( get_the_ID() ), [ 'publish', 'private', 'future' ], true ) ) {
        $save_hint = 'Enregistrez le brouillon, puis vérifiez l’aperçu avant de publier.';
    }
    echo '<div class="notice notice-info"><p>' . esc_html( $save_hint ) . ' Les révisions permettent de revenir à une version enregistrée de la fiche. Elles ne récupèrent pas les photos supprimées de la médiathèque.</p></div>';
    if ( 'fp_project' === $screen->post_type ) {
        echo '<div class="notice notice-info"><p>Un chantier publié s’affiche si sa photo principale est ajoutée et sa publication autorisée. Renseignez la commune, sans nom ni adresse précise du client.</p></div>';
    }
} );

add_action( 'admin_head', static function () {
    $screen = get_current_screen();
    if ( ! $screen || ! isset( fp_schema()[$screen->post_type] ) ) { return; }
    echo '<style>.pods-form-ui-row{max-width:900px}.pods-form-ui-field-type-paragraph textarea{min-height:100px}#poststuff .inside .pods-form-ui-field{max-width:100%}';
    if ( fp_is_christophe() && in_array( $screen->post_type, [ 'fp_information', 'fp_service' ], true ) ) { echo '#titlediv,#edit-slug-box{display:none}'; }
    echo '@media(max-width:600px){.pods-form-ui-label,.pods-form-ui-field{float:none!important;width:100%!important}#poststuff{min-width:0}.pods-field{max-width:100%}}</style>';
} );

// No public app consumes REST. Native media and administration remain available
// to authenticated users; WordPress/Pods endpoint capability checks still apply.
add_filter( 'rest_authentication_errors', static function ( $result ) {
    if ( $result ) { return $result; }
    return is_user_logged_in() ? $result : new WP_Error( 'fp_authentication_required', 'Authentification requise.', [ 'status' => 401 ] );
}, 30 );
add_filter( 'rest_pre_dispatch', static function ( $result, $server, $request ) {
    // Pods exposes schema discovery to authenticated readers. Christophe Feret edits
    // content in native screens and never needs these configuration endpoints.
    if ( preg_match( '#^/pods(?:/|$)#', $request->get_route() ) && ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'fp_schema_access_denied', 'La configuration des contenus est réservée à Aliant.', [ 'status' => is_user_logged_in() ? 403 : 401 ] );
    }
    return $result;
}, 10, 3 );
add_filter( 'xmlrpc_enabled', '__return_false' );
