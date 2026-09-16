<?php
/** Create the editor account without putting its password in command arguments. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || wp_get_environment_type() !== 'local' ) {
    exit( 1 );
}
if ( ! get_role( 'fp_christophe' ) ) {
    WP_CLI::error( 'Le rôle fp_christophe est absent. Exécuter le bootstrap avant de créer le compte.' );
}
if ( ! username_exists( 'christophe' ) ) {
    $password = getenv( 'CHRISTOPHE_PASSWORD' );
    if ( ! is_string( $password ) || strlen( $password ) < 16 ) {
        WP_CLI::error( 'Un mot de passe privé d’au moins 16 caractères est requis.' );
    }
    $result = wp_insert_user( array(
        'user_login'   => 'christophe',
        'user_pass'    => $password,
        'user_email'   => sanitize_email( getenv( 'CHRISTOPHE_EMAIL' ) ?: 'christophe@example.test' ),
        'display_name' => 'Christophe Feret',
        'role'         => 'fp_christophe',
    ) );
    if ( is_wp_error( $result ) ) {
        WP_CLI::error( 'Impossible de créer le compte Christophe. Vérifier les comptes existants.' );
    }
    WP_CLI::success( 'Compte Christophe créé sans notification email.' );
} else {
    $existing = get_user_by( 'login', 'christophe' );
    if ( ! $existing || array( 'fp_christophe' ) !== array_values( $existing->roles ) || user_can( $existing, 'manage_options' ) ) {
        WP_CLI::error( 'Le compte christophe existant possède un rôle inattendu. Aliant doit le vérifier avant de continuer.' );
    }
    WP_CLI::log( 'Compte Christophe existant conservé : rôle et mot de passe inchangés.' );
}
