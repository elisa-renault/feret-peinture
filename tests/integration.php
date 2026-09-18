<?php
/**
 * Integration checks against real WordPress and Pods. Run ONLY on a disposable DB:
 * docker compose run --rm -e FP_RUN_DESTRUCTIVE_TESTS=1 wpcli wp eval-file /project/tests/integration.php
 * Repeat with -e WP_ENVIRONMENT_TYPE=production on the disposable installation.
 * Uses synthetic .example.test accounts; sends no mail. HTTP tests are separate.
 */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || '1' !== getenv( 'FP_RUN_DESTRUCTIVE_TESTS' ) ) {
    throw new RuntimeException( 'Requires WP-CLI and explicit FP_RUN_DESTRUCTIVE_TESTS=1 on a disposable database.' );
}
if ( ! function_exists( 'fp_schema' ) || ! function_exists( 'pods' ) ) {
    WP_CLI::error( 'Activate Pods and Feret Peinture core first.' );
}

// WP-CLI evaluates this file inside a method scope; share counters explicitly.
global $fpqa_failures, $fpqa_checks;
$fpqa_failures = [];
$fpqa_checks = 0;
function fpqa_check( bool $condition, string $description ): void {
    global $fpqa_failures, $fpqa_checks;
    ++$fpqa_checks;
    WP_CLI::log( ( $condition ? 'PASS ' : 'FAIL ' ) . $description );
    if ( ! $condition ) { $fpqa_failures[] = $description; }
}
function fpqa_ids( array $posts ): array { return array_map( static fn( $post ) => (int) $post->ID, $posts ); }
function fpqa_rest( string $method, string $route, array $params = [] ): WP_REST_Response {
    // Pods enables its administrator bypass for CLI commands. Remove that CLI-only
    // shortcut when exercising permission callbacks as an ordinary HTTP editor.
    remove_filter( 'pods_is_admin', '__return_true' );
    $request = new WP_REST_Request( $method, $route );
    $request->set_body_params( $params );
    return rest_do_request( $request );
}

$posts_to_delete = [];
$attachments_to_delete = [];
$users_to_delete = [];
$original_user = get_current_user_id();
$original_information = [];
$restore_posts = [];
$information_id = fp_information_id();
$original_information_revisions = $information_id ? array_keys( wp_get_post_revisions( $information_id ) ) : [];
$admin_id = (int) ( get_users( [ 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ] )[0] ?? 0 );
fpqa_check( $admin_id > 0, 'Administrator account exists in the disposable installation' );
fpqa_check( $information_id > 0, 'Bootstrap created the information singleton' );
if ( ! $admin_id || ! $information_id ) { WP_CLI::error( 'Run the bootstrap before this suite.' ); }

try {
    wp_set_current_user( $admin_id );
    fp_install_roles();
    foreach ( fp_schema() as $type => $definition ) {
        $pod = pods( $type );
        fpqa_check( $pod->is_valid(), "Pods type {$type} is registered and usable" );
        $actual = $pod->fields();
        foreach ( $definition['fields'] as $name => $field ) {
            fpqa_check( isset( $actual[$name] ) && $actual[$name]['type'] === $field['type'], "Pods field {$type}.{$name} has its expected type" );
        }
        fpqa_check( ! get_post_type_object( $type )->show_in_rest, "{$type} has no public native REST collection" );
    }
    $login = 'fpqa_' . strtolower( wp_generate_password( 8, false, false ) );
    $editor = wp_insert_user( [ 'user_login' => $login, 'user_pass' => wp_generate_password( 48, true, true ), 'user_email' => $login . '@example.test', 'role' => 'fp_christophe' ] );
    if ( is_wp_error( $editor ) ) { throw new RuntimeException( 'Cannot create the synthetic editor account.' ); }
    $editor = (int) $editor;
    $users_to_delete[] = $editor;
    $service_ids = get_posts( [ 'post_type' => 'fp_service', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids', 'suppress_filters' => true ] );
    fpqa_check( count( $service_ids ) === 4, 'Bootstrap creates exactly four service families' );
    $service_id = (int) ( $service_ids[0] ?? 0 );
    $service2_id = (int) ( $service_ids[1] ?? 0 );
    $service_original = get_post( $service_id, ARRAY_A );
    $restore_posts[$service_id] = [ 'post' => $service_original, 'meta' => [ 'visible' => get_post_meta( $service_id, 'visible', true ) ], 'revisions' => array_keys( wp_get_post_revisions( $service_id ) ) ];
    $page = wp_insert_post( [ 'post_title' => 'FP QA private page', 'post_status' => 'draft', 'post_type' => 'page' ] );
    $ordinary = wp_insert_post( [ 'post_title' => 'FP QA private post', 'post_status' => 'draft', 'post_type' => 'post' ] );
    $extra_information = wp_insert_post( [ 'post_title' => 'FP QA forbidden second singleton', 'post_status' => 'draft', 'post_type' => 'fp_information' ] );
    array_push( $posts_to_delete, $page, $ordinary, $extra_information );

    require_once ABSPATH . 'wp-admin/includes/image.php';
    // Programmatically generated solid swatches, never portfolio photographs.
    if ( ! function_exists( 'imagecreatetruecolor' ) ) { throw new RuntimeException( 'GD is required for real image-processing tests.' ); }
    $swatch = imagecreatetruecolor( 24, 24 );
    imagefill( $swatch, 0, 0, imagecolorallocate( $swatch, 35, 66, 62 ) );
    ob_start(); imagepng( $swatch ); $pixel = ob_get_clean(); imagedestroy( $swatch );
    for ( $i = 1; $i <= 3; ++$i ) {
        $upload = wp_upload_bits( 'fpqa-pixel-' . $i . '.png', null, $pixel );
        if ( $upload['error'] ) { throw new RuntimeException( 'Cannot write image fixture.' ); }
        $attachment = wp_insert_attachment( [ 'post_title' => 'QA image ' . $i, 'post_mime_type' => 'image/png', 'post_status' => 'inherit', 'post_author' => $editor ], $upload['file'] );
        wp_update_attachment_metadata( $attachment, wp_generate_attachment_metadata( $attachment, $upload['file'] ) );
        $attachments_to_delete[] = (int) $attachment;
    }
    [ $image1, $image2, $image3 ] = $attachments_to_delete;

    foreach ( [ 'large' => [ 3200, 1600 ], 'orientation' => [ 90, 60 ] ] as $fixture => $dimensions ) {
        $canvas = imagecreatetruecolor( $dimensions[0], $dimensions[1] );
        imagefill( $canvas, 0, 0, imagecolorallocate( $canvas, 159, 63, 46 ) );
        ob_start(); imagejpeg( $canvas, null, 92 ); $jpeg = ob_get_clean(); imagedestroy( $canvas );
        if ( 'orientation' === $fixture ) {
            // Minimal valid EXIF/TIFF IFD with Orientation=6 (rotate 90° clockwise).
            $exif = "Exif\0\0II" . pack( 'vVv', 42, 8, 1 ) . pack( 'vvVV', 0x0112, 3, 1, 6 ) . pack( 'V', 0 );
            $jpeg = substr( $jpeg, 0, 2 ) . "\xFF\xE1" . pack( 'n', strlen( $exif ) + 2 ) . $exif . substr( $jpeg, 2 );
        }
        $upload = wp_upload_bits( 'fpqa-' . $fixture . '.jpg', null, $jpeg );
        if ( $upload['error'] ) { throw new RuntimeException( 'Cannot write JPEG fixture.' ); }
        $attachment = wp_insert_attachment( [ 'post_title' => 'QA ' . $fixture, 'post_mime_type' => 'image/jpeg', 'post_status' => 'inherit', 'post_author' => $editor ], $upload['file'] );
        $attachments_to_delete[] = (int) $attachment;
        $metadata = wp_generate_attachment_metadata( $attachment, $upload['file'] );
        wp_update_attachment_metadata( $attachment, $metadata );
        if ( 'large' === $fixture ) {
            fpqa_check( 2200 === (int) ( $metadata['width'] ?? 0 ) && 1100 === (int) ( $metadata['height'] ?? 0 ), 'Real JPEG upload is reduced from 3200×1600 to 2200×1100' );
            fpqa_check( ! empty( $metadata['sizes']['medium'] ) && ! empty( $metadata['sizes']['thumbnail'] ), 'WordPress generates responsive image derivatives' );
        } else {
            fpqa_check( 60 === (int) ( $metadata['width'] ?? 0 ) && 90 === (int) ( $metadata['height'] ?? 0 ), 'Real EXIF orientation 6 JPEG is rotated on import (90×60 → 60×90)' );
        }
    }

    wp_set_current_user( $editor );
    foreach ( [ 'manage_options', 'activate_plugins', 'install_plugins', 'update_plugins', 'edit_plugins', 'edit_theme_options', 'switch_themes', 'list_users', 'create_users', 'edit_users', 'promote_users', 'delete_users', 'edit_posts', 'publish_posts', 'delete_posts', 'edit_pages', 'publish_pages', 'delete_pages', 'pods', 'pods_admin', 'unfiltered_html', 'unfiltered_upload', 'create_fp_services', 'create_fp_informations', 'delete_fp_services', 'delete_fp_informations' ] as $cap ) {
        fpqa_check( ! current_user_can( $cap ), 'Christophe Feret cannot ' . $cap );
    }
    foreach ( [ 'read', 'upload_files', 'create_fp_projects', 'publish_fp_projects' ] as $cap ) {
        fpqa_check( current_user_can( $cap ), 'Christophe Feret can ' . $cap );
    }
    fpqa_check( current_user_can( 'edit_post', $information_id ), 'Christophe Feret can edit the actual information singleton' );
    fpqa_check( ! current_user_can( 'edit_post', $extra_information ), 'Christophe Feret cannot edit a second information record by ID' );
    fpqa_check( ! current_user_can( 'delete_post', $information_id ), 'Christophe Feret cannot delete the information singleton' );
    fpqa_check( current_user_can( 'edit_post', $service_id ), 'Christophe Feret can edit a seeded service' );
    fpqa_check( ! current_user_can( 'delete_post', $service_id ), 'Christophe Feret cannot delete a seeded service by ID' );
    wp_update_post( [ 'ID' => $service_id, 'post_title' => 'Forbidden renamed family', 'post_name' => 'forbidden-family-slug', 'post_content' => 'QA prestation modifiée par Christophe Feret.' ] );
    fpqa_check( $service_original['post_title'] === get_the_title( $service_id ) && $service_original['post_name'] === get_post_field( 'post_name', $service_id ), 'Service family title and slug resist forged structural changes' );
    fpqa_check( 'QA prestation modifiée par Christophe Feret.' === get_post_field( 'post_content', $service_id ), 'Christophe Feret actually changes service editorial text' );
    pods( 'fp_service', $service_id )->save( [ 'visible' => 0 ] );
    fpqa_check( ! in_array( $service_id, fpqa_ids( fp_services() ), true ), 'A service hidden by Christophe Feret disappears from listings' );
    pods( 'fp_service', $service_id )->save( [ 'visible' => 1 ] );
    fpqa_check( ! current_user_can( 'edit_post', $page ) && ! current_user_can( 'edit_post', $ordinary ), 'Core page/post ID edit attempts are denied' );
    fpqa_check( ! current_user_can( 'edit_user', $admin_id ), 'Christophe Feret cannot edit the administrator by ID' );
    fpqa_check( ! current_user_can( 'delete_user', $admin_id ), 'Christophe Feret cannot delete the administrator by ID' );
    foreach ( [ 'GET' => '/wp/v2/settings', 'POST' => '/wp/v2/pages' ] as $method => $route ) {
        $response = fpqa_rest( $method, $route, [ 'title' => 'Denied QA request', 'status' => 'publish' ] );
        fpqa_check( 403 === $response->get_status(), "Authenticated REST {$method} {$route} denied with 403" );
    }
    $response = fpqa_rest( 'POST', '/wp/v2/posts', [ 'title' => 'Denied QA request', 'status' => 'publish' ] );
    fpqa_check( 403 === $response->get_status(), 'Authenticated REST cannot create a core WordPress post' );
    $response = fpqa_rest( 'GET', '/pods/v1/pods' );
    fpqa_check( 403 === $response->get_status(), 'Authenticated REST cannot inspect or manage the Pods schema (403)' );
    $response = fpqa_rest( 'GET', '/wp/v2/fp_information' );
    fpqa_check( 404 === $response->get_status(), 'Information CPT has no REST endpoint' );

    $project = wp_insert_post( [ 'post_type' => 'fp_project', 'post_title' => 'FP QA revision A', 'post_content' => 'Description réelle de test A.', 'post_status' => 'draft', 'post_author' => $editor ], true );
    if ( is_wp_error( $project ) ) { throw new RuntimeException( 'Cannot insert test project.' ); }
    $project = (int) $project;
    $posts_to_delete[] = $project;
    fpqa_check( current_user_can( 'edit_post', $project ), 'Christophe Feret can edit his own draft project' );
    wp_update_post( [ 'ID' => $project, 'post_status' => 'publish' ] );
    fpqa_check( 'publish' === get_post_status( $project ) && current_user_can( 'edit_post', $project ), 'Project publishes and remains editable' );
    set_post_thumbnail( $project, $image1 );
    $save_a = pods( 'fp_project', $project )->save( [ 'town' => 'QA Écouen A', 'service' => $service_id, 'short_description' => 'Texte A', 'gallery' => [ $image1, $image2 ], 'before_photo' => $image1, 'after_photo' => $image2, 'featured' => 1, 'publication_authorized' => 1 ] );
    fpqa_check( ! is_wp_error( $save_a ) && $save_a, 'Actual Pods save accepts the project fields' );
    fpqa_check( [ $image1, $image2 ] === fp_gallery( $project ), 'Pods stores the first ordered gallery' );
    $revision_a = 0;
    foreach ( wp_get_post_revisions( $project ) as $revision ) {
        $snapshot = get_post_meta( $revision->ID, '_fp_revision_fields', true );
        if ( 'QA Écouen A' === ( $snapshot['fields']['town'] ?? '' ) ) { $revision_a = (int) $revision->ID; break; }
    }
    fpqa_check( $revision_a > 0, 'A native WordPress revision contains the saved Pods snapshot' );
    wp_update_post( [ 'ID' => $project, 'post_title' => 'FP QA revision B', 'post_content' => 'Description réelle de test B.' ] );
    set_post_thumbnail( $project, $image3 );
    pods( 'fp_project', $project )->save( [ 'town' => 'QA Écouen B', 'service' => $service2_id, 'short_description' => 'Texte B', 'gallery' => [ $image3, $image1 ], 'before_photo' => $image3, 'after_photo' => '', 'featured' => 0, 'publication_authorized' => 0 ] );
    fpqa_check( [ $image3, $image1 ] === fp_gallery( $project ), 'A second real Pods save changes image selection and order' );
    fpqa_check( current_user_can( 'edit_post', $revision_a ), 'Christophe Feret has native revision restoration permission' );
    $restored = $revision_a ? wp_restore_post_revision( $revision_a ) : false;
    fpqa_check( $project === $restored, 'Native wp_restore_post_revision successfully restores the project' );
    fpqa_check( 'FP QA revision A' === get_the_title( $project ) && 'Description réelle de test A.' === get_post_field( 'post_content', $project ), 'Revision restores title and main content' );
    fpqa_check( 'QA Écouen A' === get_post_meta( $project, 'town', true ) && 'Texte A' === get_post_meta( $project, 'short_description', true ), 'Revision restores scalar Pods fields' );
    fpqa_check( [ $image1, $image2 ] === fp_gallery( $project ), 'Revision restores actual gallery IDs and their order' );
    fpqa_check( $image1 === fp_image_id( $project, 'before_photo' ) && $image2 === fp_image_id( $project, 'after_photo' ), 'Revision restores both before/after relationships' );
    fpqa_check( $service_id === (int) get_post_meta( $project, 'service', true ), 'Revision restores the actual saved service relationship' );
    fpqa_check( $image1 === (int) get_post_thumbnail_id( $project ), 'Revision restores the featured image selection' );
    fpqa_check( '1' === (string) get_post_meta( $project, 'publication_authorized', true ) && '1' === (string) get_post_meta( $project, 'featured', true ), 'Revision restores publication authorization and featured flag' );
    fpqa_check( current_user_can( 'delete_post', $project ), 'Christophe Feret may trash his project' );
    wp_trash_post( $project );
    fpqa_check( 'trash' === get_post_status( $project ) && current_user_can( 'delete_post', $project ), 'Trashed project retains recovery permission' );
    wp_untrash_post( $project );
    fpqa_check( 'trash' !== get_post_status( $project ), 'Native untrash restores the project' );
    fpqa_check( [ $image1, $image2 ] === fp_gallery( $project ), 'Trash and recovery preserve Pods gallery relationships' );

    foreach ( array_keys( fp_schema()['fp_information']['fields'] ) as $key ) { $original_information[$key] = get_post_meta( $information_id, $key, true ); }
    $information_title = get_the_title( $information_id );
    $information_slug = get_post_field( 'post_name', $information_id );
    pods( 'fp_information', $information_id )->save( [ 'phone_mobile' => '06 00 00 00 01', 'public_email' => 'qa@example.test', 'presentation' => 'Présentation QA <script>script</script>', 'confirmed_area' => 'Zone QA non écrasée' ] );
    wp_update_post( [ 'ID' => $information_id, 'post_title' => 'Attempted structural change', 'post_name' => 'attempted-structure' ] );
    fpqa_check( $information_title === get_the_title( $information_id ) && $information_slug === get_post_field( 'post_name', $information_id ), 'Forged information title and slug changes are ignored server-side' );
    fpqa_check( false === strpos( get_post_meta( $information_id, 'presentation', true ), '<script>' ), 'Information field sanitation removes script markup on save' );
    if ( fp_is_preview() || fp_approved( 'FP_CONTACT_APPROVED' ) ) {
        fpqa_check( '06 00 00 00 01' === fp_phone_display() && 'tel:+33600000001' === fp_phone_uri(), 'Approved mobile is displayed with a callable international URI' );
        fpqa_check( 'qa@example.test' === fp_info( 'public_email' ), 'Edited public email is returned by the shared content helper' );
    } else {
        fpqa_check( '' === fp_phone_display() && '' === fp_info( 'public_email' ), 'Unapproved contact details are hidden in production' );
    }

    wp_set_current_user( $admin_id );
    wp_update_post( [ 'ID' => $project, 'post_status' => 'publish' ] );
    $demo = wp_insert_post( [ 'post_title' => 'QA demonstration', 'post_type' => 'fp_project', 'post_status' => 'publish', 'meta_input' => [ '_fp_demo' => 1, 'publication_authorized' => 1 ] ] );
    $unauthorized = wp_insert_post( [ 'post_title' => 'QA unauthorized', 'post_type' => 'fp_project', 'post_status' => 'publish', 'meta_input' => [ 'publication_authorized' => 0 ] ] );
    $empty = wp_insert_post( [ 'post_title' => 'QA empty gallery', 'post_type' => 'fp_project', 'post_status' => 'publish', 'meta_input' => [ 'publication_authorized' => 1 ] ] );
    array_push( $posts_to_delete, $demo, $unauthorized, $empty );
    set_post_thumbnail( $demo, $image1 );
    set_post_thumbnail( $unauthorized, $image1 );
    wp_set_current_user( 0 );
    $visible = fpqa_ids( fp_projects( 100 ) );
    fpqa_check( in_array( $project, $visible, true ), 'Approved real project with an image is publicly listed' );
    fpqa_check( ! in_array( $unauthorized, $visible, true ), 'Unauthorized project is excluded from public listings' );
    fpqa_check( ! in_array( $empty, $visible, true ), 'A project without an actual image is excluded from public listings' );
    fpqa_check( ! fp_public_content_allowed( get_post( $unauthorized ) ) && ! fp_public_content_allowed( get_post( $empty ) ), 'Direct project rendering rejects missing authorization and missing image' );
    if ( ! fp_is_preview() ) {
        fpqa_check( ! in_array( $demo, $visible, true ), 'Production excludes explicitly flagged demonstration projects' );
        fpqa_check( ! fp_public_content_allowed( get_post( $demo ) ), 'Production also denies direct rendering of demo projects' );
        if ( ! fp_approved( 'FP_SERVICES_APPROVED' ) ) { fpqa_check( [] === fp_services(), 'Production hides unapproved service families' ); }
    }
    $sitemap_args = apply_filters( 'wp_sitemaps_posts_query_args', [ 'post_type' => 'fp_project', 'post_status' => 'publish', 'posts_per_page' => 100 ], 'fp_project' );
    $sitemap_ids = fpqa_ids( ( new WP_Query( $sitemap_args ) )->posts );
    fpqa_check( ! in_array( $empty, $sitemap_ids, true ) && ! in_array( $demo, $sitemap_ids, true ) && ! in_array( $unauthorized, $sitemap_ids, true ), 'Sitemap project query excludes empty, demo and unauthorized records' );
    $anonymous_auth = apply_filters( 'rest_authentication_errors', null );
    fpqa_check( is_wp_error( $anonymous_auth ) && 401 === $anonymous_auth->get_error_data()['status'], 'Anonymous REST authentication is denied with 401' );
    fpqa_check( [] === fp_gallery( $project, 'private_config' ), 'Gallery accessor rejects arbitrary metadata keys' );

    // Validate actual legal-page launch gates, including revocation after approval.
    wp_set_current_user( $admin_id );
    $legal_error = static function ( string $slug ): bool {
        return (bool) array_filter( fp_launch_errors(), static fn( $error ) => str_contains( $error, 'Page ' . $slug . ' :' ) );
    };
    foreach ( [ 'mentions-legales' ] as $slug ) {
        $legal = get_page_by_path( $slug );
        if ( ! $legal ) { fpqa_check( false, "Legal page {$slug} exists" ); continue; }
        $restore_posts[$legal->ID] = [ 'post' => get_post( $legal->ID, ARRAY_A ), 'meta' => [ '_fp_legal_approved' => get_post_meta( $legal->ID, '_fp_legal_approved', true ) ], 'revisions' => array_keys( wp_get_post_revisions( $legal->ID ) ) ];
        $legal_body = str_repeat( 'Texte synthétique réservé à cette vérification locale, jamais à publier. ', 4 );
        wp_update_post( [ 'ID' => $legal->ID, 'post_status' => 'publish', 'post_content' => $legal_body ] );
        delete_post_meta( $legal->ID, '_fp_legal_approved' );
        fpqa_check( $legal_error( $slug ), "{$slug}: long published page alone does not authorize launch" );
        update_post_meta( $legal->ID, '_fp_legal_approved', 1 );
        fpqa_check( ! $legal_error( $slug ), "{$slug}: complete published page with explicit approval passes its page gate" );
        wp_update_post( [ 'ID' => $legal->ID, 'post_status' => 'draft' ] );
        fpqa_check( $legal_error( $slug ), "{$slug}: returning approved page to draft blocks launch" );
        wp_update_post( [ 'ID' => $legal->ID, 'post_status' => 'publish', 'post_content' => 'Court.' ] );
        fpqa_check( $legal_error( $slug ), "{$slug}: incomplete approved page blocks launch" );
    }
    if ( ! fp_approved( 'FP_LAUNCH_APPROVED' ) ) {
        fpqa_check( (bool) array_filter( fp_launch_errors(), static fn( $error ) => str_starts_with( $error, 'FP_LAUNCH_APPROVED' ) ), 'Editorial/legal changes cannot bypass missing private launch approval' );
        fpqa_check( false === apply_filters( 'wp_sitemaps_enabled', true ), 'Sitemap stays disabled before explicit launch approval' );
    }

    // Repeat the installer against deliberately edited editorial data.
    wp_set_current_user( $admin_id );
    $bootstrap = dirname( __DIR__ ) . '/scripts/bootstrap.php';
    if ( is_file( $bootstrap ) ) {
        $before_counts = [];
        foreach ( [ 'page', 'fp_service', 'fp_information' ] as $type ) { $before_counts[$type] = (array) wp_count_posts( $type ); }
        require $bootstrap;
        $after_counts = [];
        foreach ( [ 'page', 'fp_service', 'fp_information' ] as $type ) { $after_counts[$type] = (array) wp_count_posts( $type ); }
        fpqa_check( $before_counts === $after_counts, 'Second bootstrap creates no duplicate pages, services or information' );
        fpqa_check( 'Zone QA non écrasée' === get_post_meta( $information_id, 'confirmed_area', true ), 'Second bootstrap preserves Christophe Feret’s modified field content' );
        fpqa_check( 'QA prestation modifiée par Christophe Feret.' === get_post_field( 'post_content', $service_id ), 'Second bootstrap preserves edited service prose' );
        fpqa_check( [ $image1, $image2 ] === fp_gallery( $project ), 'Second bootstrap preserves existing project galleries' );
    } else { fpqa_check( false, 'Bootstrap is available for its actual idempotence check' ); }
} catch ( Throwable $error ) {
    $fpqa_failures[] = get_class( $error ) . ': ' . $error->getMessage();
    WP_CLI::warning( end( $fpqa_failures ) );
} finally {
    wp_set_current_user( $admin_id );
    if ( $original_information ) { pods( 'fp_information', $information_id )->save( $original_information ); }
    foreach ( $restore_posts as $id => $state ) {
        wp_update_post( wp_slash( $state['post'] ) );
        foreach ( $state['meta'] as $key => $value ) {
            if ( '' === $value ) { delete_post_meta( $id, $key ); } else { update_post_meta( $id, $key, $value ); }
        }
        foreach ( wp_get_post_revisions( $id ) as $revision ) {
            if ( ! in_array( $revision->ID, $state['revisions'], true ) ) { wp_delete_post_revision( $revision->ID ); }
        }
    }
    foreach ( wp_get_post_revisions( $information_id ) as $revision ) {
        if ( ! in_array( $revision->ID, $original_information_revisions, true ) ) { wp_delete_post_revision( $revision->ID ); }
    }
    foreach ( $posts_to_delete as $id ) { if ( $id ) { wp_delete_post( $id, true ); } }
    foreach ( $attachments_to_delete as $id ) { wp_delete_attachment( $id, true ); }
    require_once ABSPATH . 'wp-admin/includes/user.php';
    foreach ( $users_to_delete as $id ) { wp_delete_user( $id ); }
    wp_set_current_user( $original_user );
}
if ( $fpqa_failures ) { WP_CLI::error( count( $fpqa_failures ) . ' failures / ' . $fpqa_checks . ' checks: ' . implode( '; ', $fpqa_failures ) ); }
WP_CLI::success( $fpqa_checks . ' integration checks passed (' . wp_get_environment_type() . '). Synthetic fixtures removed.' );
