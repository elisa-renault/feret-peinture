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
            'short_description' => [ 'label' => 'Description courte', 'type' => 'paragraph', 'description' => 'Décrivez les travaux réellement réalisés, sans nom de client.' ],
            'gallery' => [ 'label' => 'Photos du chantier', 'type' => 'file', 'file_format_type' => 'multi', 'file_type' => 'images', 'file_uploader' => 'attachment', 'file_limit' => 12, 'description' => 'Ajoutez jusqu’à 12 photos autorisées. Faites-les glisser pour choisir leur ordre.' ],
            'before_photo' => [ 'label' => 'Photo avant (facultative)', 'type' => 'file', 'file_format_type' => 'single', 'file_type' => 'images', 'file_uploader' => 'attachment' ],
            'after_photo' => [ 'label' => 'Photo après (facultative)', 'type' => 'file', 'file_format_type' => 'single', 'file_type' => 'images', 'file_uploader' => 'attachment', 'description' => 'Le bloc avant/après s’affiche uniquement si les deux photos sont renseignées.' ],
            'featured' => [ 'label' => 'Mettre en avant', 'type' => 'boolean', 'default' => 0 ],
            'publication_authorized' => [ 'label' => 'Photos réelles et publication autorisée', 'type' => 'boolean', 'default' => 0, 'description' => 'Cochez uniquement après accord de publication. Sans cet accord, le chantier reste masqué du site public.' ],
        ],
    ],
    'fp_service' => [
        'label' => 'Mes prestations',
        'fields' => [
            'visible' => [ 'label' => 'Afficher cette prestation', 'type' => 'boolean', 'default' => 1, 'description' => 'Décochez pour la masquer. Les familles sont configurées par Aliant.' ],
        ],
    ],
    'fp_information' => [
        'label' => 'Mes informations',
        'fields' => [
            'phone_mobile' => [ 'label' => 'Téléphone principal', 'type' => 'text', 'description' => 'Numéro affiché dans les boutons Appeler, après validation de lancement par Aliant.' ],
            'phone_landline' => [ 'label' => 'Téléphone fixe (facultatif)', 'type' => 'text' ],
            'public_email' => [ 'label' => 'Email public validé (facultatif)', 'type' => 'email', 'description' => 'Uniquement une adresse existante que vous souhaitez publier. Le destinataire privé des devis est géré par Aliant.' ],
            'presentation' => [ 'label' => 'Présentation de l’entreprise', 'type' => 'paragraph', 'description' => 'Des informations concrètes sur votre activité, sans promesse non vérifiée.' ],
            'confirmed_area' => [ 'label' => 'Communes d’intervention confirmées', 'type' => 'paragraph', 'description' => 'Une commune par ligne. Laissez vide tant que la zone n’est pas confirmée.' ],
            'temporary_message' => [ 'label' => 'Message temporaire (facultatif)', 'type' => 'paragraph', 'description' => 'Un message court affiché sur le site. Pensez à le supprimer quand il n’est plus d’actualité.' ],
        ],
    ],
];
