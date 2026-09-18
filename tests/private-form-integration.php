<?php
/** Read-only staging gate checks; no network request or email is sent. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || 'staging' !== wp_get_environment_type() || ! str_contains( home_url(), 'localhost' ) ) {
    throw new RuntimeException( 'Requires local WordPress with staging environment override.' );
}
require '/project/config/feret-private-preview.php';
$settings = array(
    'FP_PREVIEW_FORM_ENABLED' => '1', 'FP_CONTACT_APPROVED' => '1',
    'FP_MAIL_DELIVERY_VERIFIED' => '1', 'FP_PRIVACY_RETENTION' => 'Test retention',
    'FP_QUOTE_TO' => 'quote@example.test', 'FP_MAIL_FROM' => 'site@example.test',
    'FP_SMTP_HOST' => 'smtp.example.test', 'FP_SMTP_SECURE' => 'ssl',
);
$previous = array();
$count = 0;
$check = static function ( $condition, $message ) use ( &$count ) {
    if ( ! $condition ) { throw new RuntimeException( $message ); }
    ++$count;
    WP_CLI::log( 'PASS: ' . $message );
};
try {
    foreach ( $settings as $key => $value ) { $previous[$key] = getenv( $key ); putenv( $key . '=' . $value ); }
    $check( fp_quote_ready(), 'Explicitly enabled private form is ready without public launch approval' );
    $check( false === apply_filters( 'pre_wp_mail', null, array() ), 'Unrelated staging email stays blocked' );
    $GLOBALS['fp_quote_mail_active'] = true;
    $check( null === apply_filters( 'pre_wp_mail', null, array() ), 'Authorized form mail passes both preview guards' );
    foreach ( array( 'FP_PREVIEW_FORM_ENABLED', 'FP_CONTACT_APPROVED', 'FP_MAIL_DELIVERY_VERIFIED', 'FP_PRIVACY_RETENTION' ) as $key ) {
        putenv( $key . '=' );
        $check( ! fp_quote_ready() && false === apply_filters( 'pre_wp_mail', null, array() ), $key . ' missing closes the form and email gate' );
        putenv( $key . '=' . $settings[$key] );
    }
    putenv( 'FP_SMTP_SECURE=' );
    $check( ! fp_quote_ready(), 'Unencrypted real staging transport is rejected' );
    putenv( 'FP_SMTP_SECURE=ssl' );
    $GLOBALS['fp_quote_mail_active'] = false;
    $check( false === apply_filters( 'pre_wp_mail', null, array() ), 'Leaving form scope blocks unrelated mail again' );
    ob_start(); fp_render_quote_form(); $html = ob_get_clean();
    $check( str_contains( $html, 'data-quote-form' ) && ! str_contains( $html, 'Aucun message ne sera transmis' ), 'Active staging form does not falsely claim test-only delivery' );
} finally {
    unset( $GLOBALS['fp_quote_mail_active'] );
    foreach ( $previous as $key => $value ) { false === $value ? putenv( $key ) : putenv( $key . '=' . $value ); }
}
WP_CLI::success( $count . ' private form assertions passed without sending email.' );
