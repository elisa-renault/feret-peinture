<?php
/** Self-hosted ALTCHA: local assets, expiring signatures and single-use proofs. */
defined( 'ABSPATH' ) || exit;
spl_autoload_register( function ( $class ) {
    $prefix = 'AltchaOrg\\Altcha\\';
    if ( str_starts_with( $class, $prefix ) ) {
        $file = __DIR__ . '/../vendor/altcha/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
        if ( is_file( $file ) ) { require_once $file; }
    }
} );
function fp_captcha_service() {
    return new \AltchaOrg\Altcha\Altcha( hash_hmac( 'sha256', 'fp-captcha', wp_salt( 'auth' ) ) );
}
function fp_captcha_challenge() {
    return fp_captcha_service()->createChallenge( new \AltchaOrg\Altcha\CreateChallengeOptions(
        algorithm: new \AltchaOrg\Altcha\Algorithm\Pbkdf2(), cost: 1000,
        expiresAt: time() + 20 * MINUTE_IN_SECONDS
    ) );
}
// Dedicated public challenge URL; keep the site's REST API private.
add_action( 'template_redirect', function () {
    if ( ! isset( $_GET['fp_captcha'] ) || '1' !== $_GET['fp_captcha'] ) { return; }
    nocache_headers();
    if ( 'GET' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) { wp_send_json_error( null, 405 ); }
    wp_send_json( fp_captcha_challenge()->toArray() );
}, -10 );
add_action( 'wp_enqueue_scripts', function () {
    if ( is_page( 'contact' ) ) {
        wp_enqueue_script( 'fp-altcha', plugins_url( '../assets/altcha-3.2.2.js', __FILE__ ), array(), '3.2.2', true );
    }
}, 5 );
/** Return the signed challenge ID, or false. Do not trust client-supplied verification states. */
function fp_captcha_verify( $raw ) {
    if ( ! is_string( $raw ) || strlen( $raw ) > 8000 || '' === $raw ) { return false; }
    try {
        $payload = \AltchaOrg\Altcha\Payload::fromBase64( $raw );
        $params = $payload->challenge->parameters;
        if ( 'PBKDF2/SHA-256' !== $params->algorithm || 1000 !== $params->cost || 32 !== $params->keyLength || '00' !== $params->keyPrefix || ! $params->expiresAt || $params->expiresAt <= time() || $params->expiresAt > time() + 20 * MINUTE_IN_SECONDS ) { return false; }
        $result = fp_captcha_service()->verifySolution( new \AltchaOrg\Altcha\VerifySolutionOptions( $payload, new \AltchaOrg\Altcha\Algorithm\Pbkdf2() ) );
        return $result->verified ? $payload->challenge->signature : false;
    } catch ( \Throwable $error ) { return false; }
}
function fp_captcha_consume( $signature ) {
    global $wpdb;
    $key = hash_hmac( 'sha256', 'captcha:' . $signature, wp_salt( 'nonce' ) );
    return 1 === $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->prefix}fp_quote_limits (counter_key, attempts, expires) VALUES (%s, 1, %d)", $key, time() + HOUR_IN_SECONDS ) );
}
