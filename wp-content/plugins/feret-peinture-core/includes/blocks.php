<?php
defined( 'ABSPATH' ) || exit;

function fp_service_block_names(): array {
    return [ 'feret/service-content', 'feret/service-note' ];
}

function fp_service_blocks_from_html( string $html ): string {
    $locked = [ 'lock' => [ 'move' => true, 'remove' => true ] ];
    return serialize_blocks( [
        [
            'blockName' => 'feret/service-content',
            'attrs' => $locked,
            'innerBlocks' => [],
            'innerHTML' => '<div class="wp-block-feret-service-content">' . $html . '</div>',
            'innerContent' => [ '<div class="wp-block-feret-service-content">' . $html . '</div>' ],
        ],
        [
            'blockName' => 'feret/service-note',
            'attrs' => $locked,
            'innerBlocks' => [],
            'innerHTML' => '',
            'innerContent' => [],
        ],
    ] );
}

add_action( 'init', static function () {
    foreach ( [ 'service-content', 'service-note' ] as $block ) {
        register_block_type_from_metadata( FP_CORE_PATH . '/blocks/' . $block );
    }
}, 15 );

add_filter( 'allowed_block_types_all', static function ( $allowed, $context ) {
    $post = $context->post ?? null;
    if ( $post instanceof WP_Post && 'fp_service' === $post->post_type ) {
        return fp_service_block_names();
    }
    return $allowed;
}, 20, 2 );
