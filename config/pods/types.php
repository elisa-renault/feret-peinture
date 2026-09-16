<?php
/**
 * Versioned, code-registered Pods schema. Definitions extend native WordPress
 * post types registered by the métier plugin. No schema is editable by Christophe.
 * Source: https://docs.pods.io/code/registering-configurations/
 */
defined( 'ABSPATH' ) || exit;

return [
    'fp_project' => [
        'label' => 'Mes chantiers',
        'fields' => [
            'town' => [ 'label' => 'Commune du chantier', 'type' => 'text', 'required' => 1, 'description' => 'La commune uniquement, jamais l’adresse du client.' ],
            'service' => [ 'label' => 'Prestation', 'type' => 'pick', 'pick_object' => 'post_type', 'pick_val' => 'fp_service', 'pick_format_type' => 'single', 'pick_format_single' => 'dropdown' ],
            'short_description' => [ 'label' => 'Description courte', 'type' => 'paragraph', 'description' => 'Résumez les travaux réalisés. Ce texte apparaît en haut de la fiche chantier.' ],
            'gallery' => [ 'label' => 'Photos du chantier', 'type' => 'file', 'file_format_type' => 'multi', 'file_type' => 'images', 'file_uploader' => 'attachment', 'file_limit' => 12, 'description' => 'Ajoutez jusqu’à 12 photos autorisées. Faites-les glisser pour choisir leur ordre.' ],
            'before_photo' => [ 'label' => 'Photo avant (facultative)', 'type' => 'file', 'file_format_type' => 'single', 'file_type' => 'images', 'file_uploader' => 'attachment' ],
            'after_photo' => [ 'label' => 'Photo après (facultative)', 'type' => 'file', 'file_format_type' => 'single', 'file_type' => 'images', 'file_uploader' => 'attachment', 'description' => 'Ajoutez les deux photos pour afficher l’avant/après.' ],
            'featured' => [ 'label' => 'Mettre en avant', 'type' => 'boolean', 'default' => 0, 'description' => 'Place ce chantier avant les autres dans les listes. L’accueil affiche les trois premiers.' ],
            'publication_authorized' => [ 'label' => 'Photos réelles et publication autorisée', 'type' => 'boolean', 'default' => 0, 'description' => 'Cochez si les photos montrent ce chantier et si vous avez l’autorisation de les publier. Sinon, la fiche reste masquée.' ],
        ],
    ],
    'fp_service' => [
        'label' => 'Mes prestations',
        'fields' => [
            'visible' => [ 'label' => 'Afficher cette prestation', 'type' => 'boolean', 'default' => 1, 'description' => 'Décochez pour masquer la prestation sur le site sans effacer son contenu.' ],
        ],
    ],
    'fp_information' => [
        'label' => 'Mes informations',
        'fields' => [
            'phone_mobile' => [ 'label' => 'Téléphone mobile public', 'type' => 'text', 'description' => 'Affiché sur le site pour prendre rendez-vous avec Christophe avant l’établissement du devis.' ],
            'phone_landline' => [ 'label' => 'Téléphone fixe (facultatif)', 'type' => 'text', 'description' => 'Conservé dans l’administration uniquement. Ce numéro n’est pas affiché sur le site.' ],
            'public_email' => [ 'label' => 'Email affiché sur le site (facultatif)', 'type' => 'email', 'description' => 'Indiquez une adresse que vous consultez. Pour changer l’adresse qui reçoit les demandes de devis, contactez Aliant.' ],
            'presentation' => [ 'label' => 'Présentation de l’entreprise', 'type' => 'paragraph', 'description' => 'Présentez brièvement votre activité. Ce texte apparaît sur l’accueil et la page de l’entreprise.' ],
            'confirmed_area' => [ 'label' => 'Zone d’intervention', 'type' => 'paragraph', 'description' => 'Décrivez le secteur ou la limite de trajet depuis Écouen.' ],
            'temporary_message' => [ 'label' => 'Message temporaire (facultatif)', 'type' => 'paragraph', 'description' => 'Affiché en haut des pages. Effacez-le lorsqu’il n’est plus d’actualité.' ],
        ],
    ],
];
