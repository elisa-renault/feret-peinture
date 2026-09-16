<?php
defined( 'ABSPATH' ) || exit;

/**
 * WordPress revisions restore our complete, versioned editorial snapshot.
 * Pods' native revision support excludes relationships/file fields (Pods 3.3.9.2,
 * src/Pods/WP/Revisions.php). Restoring through Pods also rebuilds relation order.
 * No media binary is copied: restore media files from backups if deleted.
 */
add_filter( 'wp_post_revision_meta_keys', static function ( $keys, $type ) {
    if ( isset( fp_schema()[$type] ) ) { $keys[] = '_fp_revision_fields'; }
    return array_values( array_unique( $keys ) );
}, 20, 2 );

function fp_capture_revision_fields( $pieces, $is_new, $id ) {
    if ( ! empty( $GLOBALS['fp_restoring_revision'] ) || wp_is_post_revision( $id ) ) { return $pieces; }
    $type = get_post_type( $id );
    $schema = fp_schema()[$type]['fields'] ?? [];
    if ( ! $schema ) { return $pieces; }
    $fields = [];
    foreach ( $schema as $key => $field ) {
        if ( 'file' === $field['type'] ) {
            $ids = fp_gallery( (int) $id, $key );
            $fields[$key] = 'single' === $field['file_format_type'] ? ( $ids[0] ?? '' ) : $ids;
        } elseif ( 'pick' === $field['type'] ) {
            // This single post-type relationship is persisted as its ID. Pods'
            // traversal `service.ID` returns false for code-extended types in
            // 3.3.9.2, so capture the canonical meta instead of that traversal.
            $value = get_post_meta( $id, $key, true );
            $fields[$key] = is_numeric( $value ) && get_post_type( (int) $value ) === $field['pick_val'] ? (int) $value : '';
        } else {
            $fields[$key] = get_post_meta( $id, $key, true );
        }
    }
    update_post_meta( $id, '_fp_revision_fields', [ 'schema' => 1, 'fields' => $fields, 'thumbnail' => (int) get_post_thumbnail_id( $id ) ] );
    wp_save_post_revision( $id );
    return $pieces;
}
add_filter( 'pods_api_post_save_pod_item', 'fp_capture_revision_fields', 100, 3 );

add_action( 'wp_restore_post_revision', static function ( $post_id, $revision_id ) {
    $snapshot = get_post_meta( $revision_id, '_fp_revision_fields', true );
    if ( ! is_array( $snapshot ) || empty( $snapshot['fields'] ) || ! isset( fp_schema()[get_post_type( $post_id )] ) ) { return; }
    $GLOBALS['fp_restoring_revision'] = true;
    try {
        pods( get_post_type( $post_id ), $post_id )->save( $snapshot['fields'] );
        if ( ! empty( $snapshot['thumbnail'] ) ) {
            set_post_thumbnail( $post_id, (int) $snapshot['thumbnail'] );
        } else {
            delete_post_thumbnail( $post_id );
        }
    } finally {
        $GLOBALS['fp_restoring_revision'] = false;
    }
}, 20, 2 );
