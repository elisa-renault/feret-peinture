<?php
defined( 'ABSPATH' ) || exit;

function fp_service_block_names(): array {
    return [ 'feret/service-content', 'feret/service-note' ];
}

function fp_editable_page_definitions(): array {
    return [
        'accueil' => [
            [ 'hero_label', 'Accueil - surtitre', 'Christophe Feret · Val-d’Oise' ], [ 'hero_title', 'Accueil - titre principal', 'Peintre en bâtiment à Écouen.' ],
            [ 'hero_lead', 'Accueil - introduction', 'Peinture intérieure et extérieure, revêtements de sols et murs.' ], [ 'hero_description', 'Accueil - présentation', 'Rafraîchir une pièce, rénover une façade ou changer un sol : Christophe Feret prépare les supports et réalise les finitions adaptées à votre projet.' ],
            [ 'hero_visit', 'Accueil - rendez-vous', 'Une visite sur place pour évaluer les travaux avant le devis.' ], [ 'services_title', 'Accueil - titre prestations', 'Quels travaux envisagez-vous ?' ],
            [ 'company_label', 'Accueil - surtitre entreprise', 'Votre interlocuteur' ], [ 'company_title', 'Accueil - titre entreprise', 'Christophe Feret' ],
            [ 'process_title', 'Accueil - titre étapes', 'Un rendez-vous avant le devis' ], [ 'step_one_title', 'Accueil - étape 1 titre', 'Prenez contact' ], [ 'step_one_text', 'Accueil - étape 1 texte', 'Appelez Christophe Feret ou laissez vos coordonnées pour convenir d’un rendez-vous.' ],
            [ 'step_two_title', 'Accueil - étape 2 titre', 'Une visite sur place' ], [ 'step_two_text', 'Accueil - étape 2 texte', 'Christophe Feret se déplace pour examiner les supports et évaluer lui-même les travaux.' ],
            [ 'step_three_title', 'Accueil - étape 3 titre', 'Un devis adapté au chantier' ], [ 'step_three_text', 'Accueil - étape 3 texte', 'Le devis est établi après cette visite, à partir des travaux convenus avec vous.' ],
            [ 'area_title', 'Accueil - titre zone', 'Votre peintre dans le Val-d’Oise et les environs.' ], [ 'faq_title', 'Accueil - titre questions', 'Avant votre demande de rendez-vous.' ],
            [ 'projects_title', 'Accueil - titre réalisations', 'Quelques exemples de travaux' ],
            [ 'faq_one_question', 'Accueil - question 1', 'Peut-on obtenir un devis en ligne ?' ], [ 'faq_one_answer', 'Accueil - réponse 1', 'Non. Un rendez-vous sur place est nécessaire pour que Christophe Feret évalue lui-même les travaux avant d’établir le devis.' ],
            [ 'faq_two_question', 'Accueil - question 2', 'Faut-il connaître la surface exacte ?' ], [ 'faq_two_answer', 'Accueil - réponse 2', 'Non. Une description des pièces ou des surfaces concernées suffit pour le premier contact.' ],
            [ 'faq_three_question', 'Accueil - question 3', 'Que détaille le devis ?' ], [ 'faq_three_answer', 'Accueil - réponse 3', 'Le devis décrit les travaux par pièce ou par surface : préparation des supports, revêtements et finitions, quantités et prix. Il précise aussi les travaux complémentaires et les conditions de règlement.' ],
            [ 'faq_four_question', 'Accueil - question 4', 'Puis-je envoyer des photos ?' ], [ 'faq_four_answer', 'Accueil - réponse 4', 'Le formulaire n’accepte pas de pièces jointes. Vous pourrez convenir avec Christophe Feret d’un moyen de lui envoyer vos photos.' ],
        ],
        'entreprise' => [
            [ 'title', 'Entreprise - titre', 'Christophe Feret, peintre à Écouen.' ], [ 'intro', 'Entreprise - introduction', 'Peinture, décoration, revêtements de sols et murs : une entreprise locale créée en 2008.' ],
            [ 'person_text', 'Entreprise - présentation', 'Un interlocuteur pour vos travaux, de la préparation aux finitions.' ], [ 'preparation_title', 'Entreprise - titre préparation', 'Préparer les surfaces avant de peindre' ],
            [ 'preparation_text', 'Entreprise - texte préparation', 'Avant la peinture, il y a le travail du support : lessiver, reboucher les fissures, enduire et poncer. Christophe Feret adapte cette préparation à l’état des murs, des plafonds ou des boiseries à rénover.' ],
            [ 'quote_title', 'Entreprise - titre devis', 'Un devis détaillé, pièce par pièce' ], [ 'quote_text', 'Entreprise - texte devis', 'La préparation, les finitions, les quantités et les prix sont décrits dans le devis. Vous savez ce qui est prévu pour chaque pièce ou chaque élément du chantier.' ],
            [ 'contact_title', 'Entreprise - titre contact', 'Parlons de vos travaux' ], [ 'contact_text', 'Entreprise - texte contact', 'Quelques mots sur votre projet et la commune du chantier suffisent pour un premier échange avec Christophe Feret.' ],
        ],
        'zone-intervention' => [
            [ 'title', 'Zone - titre', 'Vos travaux dans le Val-d’Oise et les environs.' ], [ 'intro', 'Zone - introduction', 'Depuis Écouen, Christophe Feret intervient dans le Val-d’Oise, en Île-de-France et dans l’Oise, selon la commune de votre chantier.' ],
            [ 'near_title', 'Zone - titre proximité', 'Un peintre près de chez vous' ], [ 'near_text', 'Zone - texte proximité', 'Écouen, Ézanville, Domont, Montmorency, Sarcelles ou L’Isle-Adam : Christophe Feret accompagne vos projets de peinture et de revêtements dans le Val-d’Oise. Dans l’Oise, les secteurs de Chantilly, Gouvieux, Senlis et Compiègne sont également accessibles.' ],
            [ 'far_title', 'Zone - titre distance', 'Votre chantier est plus loin ?' ], [ 'far_text', 'Zone - texte distance', 'Paris et les autres départements franciliens, mais aussi les secteurs d’Amiens, de Rouen ou de Reims peuvent être envisagés.' ],
            [ 'question_text', 'Zone - texte demande', 'Votre commune n’est pas citée ? Indiquez-la dans votre demande : Christophe Feret vous confirmera la possibilité d’intervention.' ], [ 'card_caption', 'Zone - carte localisation', 'Le point de départ de vos projets' ],
        ],
        'contact' => [
            [ 'title', 'Contact - titre', 'Demander un rendez-vous' ], [ 'intro', 'Contact - introduction', 'Appelez Christophe Feret ou indiquez la commune du chantier, les travaux envisagés et un moyen de vous joindre. Il vous recontactera pour convenir d’une visite sur place, avant d’établir le devis.' ],
            [ 'aside_label', 'Contact - surtitre coordonnées', 'Coordonnées' ], [ 'aside_text', 'Contact - texte coordonnées', 'Peintre à Écouen, Val-d’Oise' ],
        ],
    ];
}

function fp_editable_page_slugs(): array { return array_keys( fp_editable_page_definitions() ); }

function fp_editable_page_extra_blocks(): array {
    return [ 'core/heading', 'core/paragraph', 'core/list', 'core/quote', 'core/image', 'core/buttons', 'core/button', 'core/separator' ];
}

function fp_is_editable_page( $post ): bool {
    $post = get_post( $post );
    return $post instanceof WP_Post && 'page' === $post->post_type && in_array( $post->post_name, fp_editable_page_slugs(), true );
}

function fp_page_copy_blocks( string $slug ): string {
    $blocks = [];
    foreach ( fp_editable_page_definitions()[ $slug ] ?? [] as [ $key, $label, $content ] ) {
        $blocks[] = [ 'blockName' => 'feret/site-copy', 'attrs' => [ 'key' => $key, 'label' => $label, 'content' => $content, 'lock' => [ 'move' => true, 'remove' => true ] ], 'innerBlocks' => [], 'innerHTML' => '', 'innerContent' => [] ];
    }
    return serialize_blocks( $blocks );
}

function fp_page_copy_block_template( string $slug ): array {
    $template = [];
    foreach ( fp_editable_page_definitions()[ $slug ] ?? [] as [ $key, $label, $content ] ) {
        $template[] = [ 'feret/site-copy', [ 'key' => $key, 'label' => $label, 'content' => $content, 'lock' => [ 'move' => true, 'remove' => true ] ] ];
    }
    return $template;
}

function fp_site_copy( string $slug, string $key, string $default = '' ): string {
    $copy = [];
    $page = get_page_by_path( $slug );
    foreach ( $page ? parse_blocks( $page->post_content ) : [] as $block ) {
        if ( 'feret/site-copy' === $block['blockName'] && ! empty( $block['attrs']['key'] ) ) { $copy[ $block['attrs']['key'] ] = (string) ( $block['attrs']['content'] ?? '' ); }
    }
    $value = trim( (string) ( $copy[ $key ] ?? '' ) );
    return '' === $value ? $default : wp_strip_all_tags( $value );
}

function fp_editable_page_extra_content( string $slug ): string {
    $page = get_page_by_path( $slug );
    if ( ! $page ) { return ''; }
    $blocks = array_filter( parse_blocks( $page->post_content ), static fn( $block ) => in_array( $block['blockName'], fp_editable_page_extra_blocks(), true ) );
    return $blocks ? do_blocks( serialize_blocks( $blocks ) ) : '';
}

function fp_sanitize_editable_page_content( string $content, WP_Post $original, ?string $raw_copy_content = null ): string {
    $slug = $original->post_name;
    $incoming = parse_blocks( $content );
    $incoming_copy = parse_blocks( null === $raw_copy_content ? $content : $raw_copy_content );
    $original_copy = [];
    foreach ( parse_blocks( $original->post_content ) as $block ) {
        if ( 'feret/site-copy' === $block['blockName'] && ! empty( $block['attrs']['key'] ) ) { $original_copy[ $block['attrs']['key'] ] = $block; }
    }
    $submitted_copy = [];
    $extra = [];
    foreach ( $incoming_copy as $block ) {
        if ( 'feret/site-copy' === $block['blockName'] && ! empty( $block['attrs']['key'] ) ) { $submitted_copy[ $block['attrs']['key'] ] = $block; }
    }
    foreach ( $incoming as $block ) {
        if ( in_array( $block['blockName'], fp_editable_page_extra_blocks(), true ) ) { $extra[] = $block; }
    }
    $locked = [];
    foreach ( fp_editable_page_definitions()[ $slug ] ?? [] as [ $key, $label, $default ] ) {
        $block = $original_copy[ $key ] ?? [ 'blockName' => 'feret/site-copy', 'attrs' => [ 'key' => $key, 'label' => $label, 'content' => $default ], 'innerBlocks' => [], 'innerHTML' => '', 'innerContent' => [] ];
        if ( isset( $submitted_copy[ $key ]['attrs']['content'] ) ) { $block['attrs']['content'] = wp_kses_post( (string) $submitted_copy[ $key ]['attrs']['content'] ); }
        $block['attrs']['key'] = $key;
        $block['attrs']['label'] = $label;
        $block['attrs']['lock'] = [ 'move' => true, 'remove' => true ];
        $locked[] = $block;
    }
    return serialize_blocks( array_merge( $locked, $extra ) );
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
    foreach ( [ 'service-content', 'service-note', 'site-copy' ] as $block ) {
        register_block_type_from_metadata( FP_CORE_PATH . '/blocks/' . $block );
    }
}, 15 );

add_filter( 'allowed_block_types_all', static function ( $allowed, $context ) {
    $post = $context->post ?? null;
    if ( $post instanceof WP_Post && 'fp_service' === $post->post_type ) {
        return fp_service_block_names();
    }
    if ( fp_is_editable_page( $post ) ) { return array_merge( [ 'feret/site-copy' ], fp_editable_page_extra_blocks() ); }
    return $allowed;
}, 20, 2 );

add_filter( 'block_editor_settings_all', static function ( $settings, $context ) {
    $post = $context->post ?? null;
    if ( fp_is_editable_page( $post ) ) {
        $settings['template'] = fp_page_copy_block_template( $post->post_name );
        $settings['templateLock'] = false;
    }
    return $settings;
}, 20, 2 );
