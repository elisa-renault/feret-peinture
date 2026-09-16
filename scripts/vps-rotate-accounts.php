<?php
// Run only against the newly imported private VPS database.
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || wp_get_environment_type() !== 'staging' ) { exit( 1 ); }
foreach ( array( 'aliant' => 'FP_ADMIN_PASSWORD', 'christophe' => 'FP_EDITOR_PASSWORD' ) as $login => $variable ) {
    $user = get_user_by( 'login', $login );
    $password = getenv( $variable );
    if ( ! $user || ! $password || strlen( $password ) < 12 ) { WP_CLI::error( 'Missing account or secure password.' ); }
    wp_set_password( $password, $user->ID );
    WP_Session_Tokens::get_instance( $user->ID )->destroy_all();
}
WP_CLI::success( 'VPS account passwords rotated and previous sessions invalidated.' );
