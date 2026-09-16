<?php
/** Run only against a disposable local WordPress with Mailpit: wp eval-file tests/form-integration.php. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! fp_is_preview() || ! str_ends_with( fp_mail_setting( 'FP_QUOTE_TO' ), '.test' ) ) {
    throw new RuntimeException( 'Local environment and .test recipient required.' );
}
$checks = 0;
$assert = function ( $condition, $description ) use ( &$checks ) {
    if ( ! $condition ) { throw new RuntimeException( 'FAIL: ' . $description ); }
    ++$checks;
    WP_CLI::log( 'PASS: ' . $description );
};
$token = function () {
    $body = ( time() - 3 ) . '.' . wp_generate_password( 24, false, false );
    return $body . '.' . hash_hmac( 'sha256', $body, wp_salt( 'nonce' ) );
};
$base = array( 'name' => 'Recette locale', 'town' => 'Écouen', 'type' => 'a-preciser', 'description' => 'Message fictif de recette : peinture du séjour.', 'phone' => '', 'email' => 'recette@example.test', 'period' => '', 'website' => '', 'fp_nonce' => wp_create_nonce( 'fp_quote' ), 'fp_token' => $token() );
$assert( fp_quote_ready(), 'Mailpit configured and quote form enabled locally' );
$assert( empty( fp_quote_validate( $base )['errors'] ), 'Email-only request valid' );
$phone_only = array_merge( $base, array( 'email' => '', 'phone' => '01 23 45 67 89' ) );
$assert( empty( fp_quote_validate( $phone_only )['errors'] ), 'Phone-only request valid' );
$assert( isset( fp_quote_validate( array_merge( $base, array( 'email' => '' ) ) )['errors']['phone'] ), 'No contact method rejected' );
$GLOBALS['fp_quote_result'] = fp_quote_validate( array_merge( $base, array( 'email' => '' ) ) );
ob_start();
fp_render_quote_form();
$missing_contact_html = ob_get_clean();
unset( $GLOBALS['fp_quote_result'] );
$assert( str_contains( $missing_contact_html, 'href="#quote-contact"' ), 'Missing contact error links to the contact group' );
$assert( 2 === substr_count( $missing_contact_html, 'aria-invalid="true" aria-describedby="contact-hint error-contact"' ), 'Both contact fields reference the shared error and instructions' );
$assert( str_contains( $missing_contact_html, 'value="Recette locale"' ), 'Rendered contact error keeps the entered name' );
$assert( str_contains( $missing_contact_html, 'role="status" aria-live="polite"' ), 'Submission has a live status region' );
$assert( isset( fp_quote_validate( array_merge( $base, array( 'email' => 'invalid' ) ) )['errors']['email'] ), 'Malformed email rejected' );
$assert( isset( fp_quote_validate( array_merge( $base, array( 'email' => "x@example.test\r\nBcc: third@example.test" ) ) )['errors']['email'] ), 'Header injection rejected' );
$assert( isset( fp_quote_validate( array_merge( $base, array( 'phone' => 'bonjour 0123456789' ) ) )['errors']['phone'] ), 'Malformed optional phone rejected even with valid email' );
$assert( isset( fp_quote_validate( array_merge( $base, array( 'type' => 'electricite' ) ) )['errors']['type'] ), 'Unknown service rejected' );
$assert( isset( fp_quote_validate( array_merge( $base, array( 'description' => str_repeat( 'é', 4001 ) ) ) )['errors']['description'] ), 'Description limit enforced in characters' );
$assert( fp_quote_token_valid( $base['fp_token'] ), 'Signed aged token accepted' );
$assert( ! fp_quote_token_valid( fp_quote_token() ), 'Instant submission rejected' );
$assert( ! fp_quote_token_valid( $base['fp_token'] . 'x' ), 'Forged token rejected' );
$salt = wp_generate_password( 10, false );
$result = fp_quote_submit( array_merge( $base, array( 'website' => 'spam' ) ), 'spam-' . $salt );
$assert( ! $result['success'] && isset( $result['errors']['form'] ), 'Honeypot rejected without fake success' );
$result = fp_quote_submit( array_merge( $base, array( 'fp_nonce' => 'incorrect' ) ), 'nonce-' . $salt );
$assert( ! $result['success'], 'Invalid WordPress nonce rejected' );
for ( $i = 0; $i < 5; $i++ ) { $assert( fp_quote_rate_allowed( 'rate-' . $salt ), 'Rate limit allows attempt ' . ( $i + 1 ) ); }
$assert( ! fp_quote_rate_allowed( 'rate-' . $salt ), 'Rate limit rejects sixth attempt' );
$result = fp_quote_submit( $base, 'valid-' . $salt );
if ( ! $result['success'] ) { WP_CLI::log( 'Quote failure category: ' . implode( ', ', array_keys( $result['errors'] ) ) ); }
$assert( $result['success'], 'Real wp_mail SMTP delivery accepted by Mailpit' );
$assert( ! fp_quote_submit( $base, 'repeat-' . $salt )['success'], 'Same signed request cannot send twice' );
$old_port = getenv( 'FP_SMTP_PORT' );
putenv( 'FP_SMTP_PORT=1' );
$result = fp_quote_submit( array_merge( $base, array( 'fp_token' => $token() ) ), 'failure-' . $salt );
$assert( ! $result['success'] && isset( $result['errors']['form'] ), 'Actual SMTP connection failure has no success' );
$assert( $result['values']['description'] === $base['description'], 'SMTP failure retains supplied text in response state' );
false === $old_port ? putenv( 'FP_SMTP_PORT' ) : putenv( 'FP_SMTP_PORT=' . $old_port );
$old_host = getenv( 'FP_SMTP_HOST' );
putenv( 'FP_SMTP_HOST' );
$assert( ! fp_quote_ready(), 'Missing SMTP disables quote form' );
$assert( ! fp_quote_submit( $base, 'missing-' . $salt )['success'], 'Missing SMTP never produces a success' );
false === $old_host ? putenv( 'FP_SMTP_HOST' ) : putenv( 'FP_SMTP_HOST=' . $old_host );
global $wpdb;
$rows = $wpdb->get_results( "SELECT counter_key, attempts, expires FROM {$wpdb->prefix}fp_quote_limits", ARRAY_A );
$assert( ! str_contains( wp_json_encode( $rows ), 'recette@example.test' ), 'Rate database contains no submitted email or message' );
WP_CLI::success( $checks . ' quote integration assertions passed.' );
