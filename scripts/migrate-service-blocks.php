<?php
/** Convert legacy service HTML to the locked Feret blocks. Requires the apply argument to write. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! function_exists( 'fp_service_blocks_from_html' ) ) {
    throw new RuntimeException( 'Requires WP-CLI with Feret Peinture - métier active.' );
}
$apply = in_array( 'apply', $args ?? [], true );
$changed = 0;
$skipped = 0;
foreach ( get_posts( [ 'post_type' => 'fp_service', 'post_status' => 'any', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC', 'suppress_filters' => true ] ) as $service ) {
    if ( has_block( 'feret/service-content', $service ) ) { ++$skipped; continue; }
    if ( has_blocks( $service->post_content ) ) {
        WP_CLI::warning( "{$service->post_title}: contains unrecognised blocks and was left unchanged." );
        ++$skipped;
        continue;
    }
    ++$changed;
    WP_CLI::log( ( $apply ? 'Converting' : 'Would convert' ) . ": {$service->post_title}" );
    if ( $apply ) {
        wp_update_post( [ 'ID' => $service->ID, 'post_content' => fp_service_blocks_from_html( $service->post_content ) ] );
    }
}
WP_CLI::success( ( $apply ? 'Converted' : 'Dry run found' ) . " {$changed} service(s); skipped {$skipped}." );
