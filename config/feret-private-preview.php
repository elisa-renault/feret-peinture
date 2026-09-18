<?php
/** Private VPS preview: only explicitly authorized form submissions may send mail. */
defined( 'ABSPATH' ) || exit;
add_filter( 'pre_wp_mail', static function ( $result ) {
    return function_exists( 'fp_quote_preview_delivery_allowed' ) && fp_quote_preview_delivery_allowed() ? $result : false;
} );
