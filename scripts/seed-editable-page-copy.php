<?php
/** Seed locked text blocks on editable pages. Uses apply positional argument to write. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! function_exists( 'fp_page_copy_blocks' ) ) { throw new RuntimeException( 'Requires WP-CLI with the Feret plugin active.' ); }
$apply = in_array( 'apply', $args ?? [], true );
$changed = 0;
foreach ( fp_editable_page_definitions() as $slug => $definition ) {
    $page = get_page_by_path( $slug );
    if ( ! $page ) { WP_CLI::warning( "Missing page: {$slug}" ); continue; }
    if ( has_block( 'feret/site-copy', $page ) ) { WP_CLI::log( "Already prepared: {$slug}" ); continue; }
    if ( trim( $page->post_content ) !== '' ) { WP_CLI::warning( "{$slug}: existing content was left untouched." ); continue; }
    ++$changed;
    WP_CLI::log( ( $apply ? 'Preparing' : 'Would prepare' ) . ": {$slug}" );
    if ( $apply ) { wp_update_post( [ 'ID' => $page->ID, 'post_content' => fp_page_copy_blocks( $slug ) ] ); }
}
WP_CLI::success( ( $apply ? 'Prepared' : 'Dry run found' ) . " {$changed} editable page(s)." );
