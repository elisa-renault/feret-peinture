<?php
/** Positive production rendering contract, on a disposable local DB. No HTTP/email sent. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || '1' !== getenv( 'FP_RUN_DESTRUCTIVE_TESTS' ) || fp_is_preview() ) {
    throw new RuntimeException( 'Requires an explicitly disposable production-mode CLI test instance.' );
}
$checks = 0;
$assert = function ( $condition, $message ) use ( &$checks ) {
    if ( ! $condition ) { throw new RuntimeException( $message ); }
    ++$checks;
    WP_CLI::log( 'PASS: ' . $message );
};
$backup = array();
$information_id = fp_information_id();
$original_phone = get_post_meta( $information_id, 'phone_mobile', true );
$indexing = get_option( 'blog_public' );
$flag_names = array( 'FP_LAUNCH_APPROVED', 'FP_CONTACT_APPROVED', 'FP_SERVICES_APPROVED', 'FP_LEGAL_APPROVED', 'FP_PRIVACY_APPROVED', 'FP_MAIL_DELIVERY_VERIFIED', 'FP_PRIVACY_RETENTION', 'FP_BUSINESS_ADDRESS_JSON' );
$previous_env = array();
foreach ( $flag_names as $key ) { $previous_env[ $key ] = getenv( $key ); }
$test_domain = static fn() => 'https://feret-peinture.fr';
try {
    update_post_meta( $information_id, 'phone_mobile', '06 00 00 00 01' );
    foreach ( array_slice( $flag_names, 0, 6 ) as $flag ) { putenv( $flag . '=1' ); }
    putenv( 'FP_PRIVACY_RETENTION=Texte synthétique de recette, jamais publié.' );
    putenv( 'FP_BUSINESS_ADDRESS_JSON=' . wp_json_encode( array( 'streetAddress' => '10 avenue de Recette', 'postalCode' => '95440', 'addressLocality' => 'Écouen', 'addressCountry' => 'FR' ) ) );
    foreach ( array( 'mentions-legales', 'confidentialite' ) as $slug ) {
        $page = get_page_by_path( $slug );
        $backup[ $page->ID ] = array( 'content' => $page->post_content, 'status' => $page->post_status, 'approval' => get_post_meta( $page->ID, '_fp_legal_approved', true ) );
        wp_update_post( array( 'ID' => $page->ID, 'post_status' => 'publish', 'post_content' => '<p>RECETTE SYNTHÉTIQUE : document fictif temporaire. 10 avenue de Recette. ' . str_repeat( 'Texte synthétique uniquement pour valider le contrôle technique. ', 4 ) . '</p>[fp_contact_details]' ) );
        update_post_meta( $page->ID, '_fp_legal_approved', 1 );
    }
    update_option( 'blog_public', 1 );
    add_filter( 'pre_option_home', $test_domain );
    add_filter( 'pre_option_siteurl', $test_domain );
    $assert( ! fp_launch_errors(), 'All independent approvals and completed legal fixtures pass the launch gate' );
    $assert( apply_filters( 'wp_sitemaps_enabled', true ), 'Sitemap enabled only after approvals and public indexing' );
    $robots = apply_filters( 'wp_robots', array() );
    $assert( empty( $robots['noindex'] ), 'Approved public configuration has no forced noindex' );
    ob_start();
    do_action( 'wp_head' );
    $head = ob_get_clean();
    preg_match_all( '~<script type="application/ld\+json">(.*?)</script>~s', $head, $matches );
    $assert( 1 === count( $matches[1] ), 'Exactly one JSON-LD entity emitted' );
    $data = json_decode( $matches[1][0], true, 512, JSON_THROW_ON_ERROR );
    $assert( 'HousePainter' === $data['@type'] && 'https://schema.org' === $data['@context'], 'JSON-LD parses with the expected HousePainter vocabulary' );
    $assert( 'https://feret-peinture.fr/' === $data['url'] && ! str_contains( wp_json_encode( $data ), 'localhost' ), 'Structured URLs use the configured production origin' );
    $assert( str_replace( 'tel:', '', fp_phone_uri() ) === $data['telephone'], 'JSON-LD and visible telephone use the same data' );
    $assert( '10 avenue de Recette' === $data['address']['streetAddress'], 'Address is emitted only when also present in the approved legal text' );
    $assert( ! isset( $data['areaServed'] ), 'No unconfirmed geographic coverage emitted' );
    $assert( ! array_intersect( array( 'geo', 'aggregateRating', 'openingHours' ), array_keys( $data ) ), 'No invented coordinates, ratings or opening hours' );
    $assert( ! str_contains( do_shortcode( '[fp_contact_details]' ), 'example.test' ), 'Legal contact shortcode uses public editorial contact, never private mail transport' );
    $sitemap_types = apply_filters( 'wp_sitemaps_post_types', get_post_types( array( 'public' => true ), 'objects' ) );
    $assert( ! isset( $sitemap_types['post'] ) && ! isset( $sitemap_types['attachment'] ), 'No blog or media archives in the sitemap types' );
} finally {
    update_post_meta( $information_id, 'phone_mobile', $original_phone );
    remove_filter( 'pre_option_home', $test_domain );
    remove_filter( 'pre_option_siteurl', $test_domain );
    update_option( 'blog_public', $indexing );
    foreach ( $backup as $id => $original ) {
        wp_update_post( array( 'ID' => $id, 'post_status' => $original['status'], 'post_content' => $original['content'] ) );
        update_post_meta( $id, '_fp_legal_approved', $original['approval'] );
    }
    foreach ( $previous_env as $key => $value ) { false === $value ? putenv( $key ) : putenv( $key . '=' . $value ); }
}
WP_CLI::success( $checks . ' positive production assertions passed; original local data restored.' );
