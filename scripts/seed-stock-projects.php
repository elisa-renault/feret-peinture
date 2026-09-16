<?php
/** Explicit preview-only fixtures: wp eval-file /project/scripts/seed-stock-projects.php */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
if ( ! function_exists( 'fp_is_preview' ) || ! fp_is_preview() || ! function_exists( 'pods' ) ) {
    WP_CLI::error( 'Import réservé à la prévisualisation avec Pods actif.' );
}
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$photos = [
    7027840 => [ 'Séjour vide aux murs clairs et au sol en bois', 'Curtis Adams', 'https://www.pexels.com/photo/an-empty-living-room-7027840/' ],
    8288962 => [ 'Salon lumineux ouvert sur un escalier en bois', 'Allyson SALNESS', 'https://www.pexels.com/photo/a-modern-living-room-with-wooden-floor-and-stairs-8288962/' ],
    7027720 => [ 'Salon aménagé avec un sol aspect bois', 'Curtis Adams', 'https://www.pexels.com/photo/living-room-with-wooden-floor-7027720/' ],
];
$attachments = [];
foreach ( $photos as $key => $photo ) {
    $existing = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_fp_stock_source', 'meta_value' => $photo[2], 'numberposts' => 1 ] );
    if ( $existing ) { $attachments[$key] = $existing[0]->ID; continue; }
    $url = 'https://images.pexels.com/photos/' . $key . '/pexels-photo-' . $key . '.jpeg?auto=compress&cs=tinysrgb&w=1600';
    $id = media_sideload_image( $url, 0, $photo[0], 'id' );
    if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
    update_post_meta( $id, '_fp_stock_source', $photo[2] );
    update_post_meta( $id, '_fp_demo', 1 );
    update_post_meta( $id, '_wp_attachment_image_alt', $photo[0] . ' : photo d’illustration.' );
    wp_update_post( [ 'ID' => $id, 'post_excerpt' => 'Photo d’illustration : ' . $photo[1] . ' / Pexels. Ce visuel ne représente pas un chantier de Christophe Feret.', 'post_content' => 'Source : ' . $photo[2] . "\nLicence : https://www.pexels.com/license/" ] );
    $attachments[$key] = $id;
}

$projects = [
    'exemple-sejour-lumineux' => [
        'Exemple : un séjour lumineux', 'peinture-interieure', 7027840, 7027720,
        'Des murs clairs et un plafond blanc pour une pièce de vie lumineuse.',
        '<h2>Le projet imaginé</h2><p>Rafraîchir une pièce de vie en conservant une palette douce, facile à associer au mobilier et au sol existant.</p><h2>Les travaux envisagés</h2><p>Protection des sols, préparation des murs, rebouchage des petits défauts et ponçage. Une sous-couche adaptée précède la peinture des murs et du plafond.</p><h2>Les finitions</h2><p>Un blanc chaud sur les murs et une finition mate au plafond composent l’ambiance souhaitée. Les teintes et les produits seraient définis après examen des supports.</p>',
    ],
    'exemple-salon-escalier' => [
        'Exemple : un salon ouvert sur l’escalier', 'peinture-interieure', 8288962, 7027840,
        'Une continuité de tons clairs entre le salon, les boiseries et la montée d’escalier.',
        '<h2>Le projet imaginé</h2><p>Harmoniser les murs du salon et de la circulation pour relier visuellement les espaces et mettre en valeur les éléments en bois.</p><h2>La préparation</h2><p>Les marches, les garde-corps et le mobilier sont protégés. Les surfaces à peindre sont nettoyées, les défauts repris et les zones réparées poncées.</p><h2>Le rendu recherché</h2><p>Des murs aux tons neutres, des raccords soignés et un plafond clair. Les accès en hauteur et les boiseries à reprendre seraient précisés au devis.</p>',
    ],
    'exemple-sol-aspect-bois' => [
        'Exemple : un sol aspect bois', 'revetements-sols', 7027720, 8288962,
        'Un revêtement chaleureux pour donner de l’unité à une pièce de vie.',
        '<h2>Le projet imaginé</h2><p>Remplacer le revêtement d’une pièce de vie par un sol aspect bois, choisi pour son accord avec les murs et le mobilier.</p><h2>La préparation du support</h2><p>La dépose éventuelle, la planéité du sol et le besoin d’une sous-couche seraient évalués avant la pose. Le choix entre stratifié et PVC dépendrait du support et de l’usage.</p><h2>Les finitions prévues</h2><p>Plinthes, seuils et raccords au droit des portes complètent la pose. Les photos présentent une ambiance de référence et ne permettent pas d’identifier le matériau posé.</p>',
    ],
];
foreach ( $projects as $slug => $project ) {
    $existing = get_posts( [ 'post_type' => 'fp_project', 'post_status' => array_keys( get_post_stati() ), 'meta_key' => '_fp_stock_demo_key', 'meta_value' => $slug, 'numberposts' => 1 ] );
    if ( $existing ) { WP_CLI::log( 'Conservé : ' . get_permalink( $existing[0] ) ); continue; }
    $service = get_page_by_path( $project[1], OBJECT, 'fp_service' );
    if ( ! $service ) { WP_CLI::error( 'Prestation introuvable : ' . $project[1] ); }
    $notice = '<p><strong>Exemple fictif pour visualiser le site.</strong> Les photographies de stock illustrent des ambiances et des lieux différents. Elles ne représentent pas des travaux réalisés par Christophe Feret.</p>';
    $credits = '<h2>Photographies d’illustration</h2><p>';
    foreach ( [ $project[2], $project[3] ] as $key ) {
        $credits .= '<a href="' . esc_url( $photos[$key][2] ) . '">' . esc_html( $photos[$key][1] ) . ' / Pexels</a>. ';
    }
    $id = wp_insert_post( wp_slash( [ 'post_type' => 'fp_project', 'post_status' => 'draft', 'post_name' => $slug, 'post_title' => $project[0], 'post_content' => $notice . $project[5] . $credits . '</p>', 'comment_status' => 'closed', 'ping_status' => 'closed', 'meta_input' => [ '_fp_demo' => 1, '_fp_stock_demo_key' => $slug ] ] ), true );
    if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
    set_post_thumbnail( $id, $attachments[$project[2]] );
    // Existing preview visibility gate. _fp_demo prevents production visibility.
    pods( 'fp_project', $id )->save( [ 'town' => 'Lieu fictif', 'service' => $service->ID, 'short_description' => 'Illustration : ' . $project[4], 'gallery' => [ $attachments[$project[3]] ], 'featured' => 0, 'publication_authorized' => 1 ] );
    $result = wp_update_post( [ 'ID' => $id, 'post_status' => 'publish' ], true );
    if ( is_wp_error( $result ) ) { WP_CLI::error( $result->get_error_message() ); }
    if ( ! fp_public_content_allowed( get_post( $id ) ) || ! fp_gallery( $id ) ) { WP_CLI::error( 'Vérifiez la fiche ' . $id ); }
    WP_CLI::success( get_permalink( $id ) );
}
