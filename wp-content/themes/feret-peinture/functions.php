<?php
/** Presentation only. Business data and validation belong to Feret Peinture Core. */
if (!defined('ABSPATH')) { exit; }
require_once __DIR__ . '/inc/service-copy.php';
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'gallery', 'caption', 'style', 'script'));
    add_image_size('fp-project', 1000, 750, true);
    add_image_size('fp-project-wide', 1600, 1000, true);
    register_nav_menus(array('primary' => 'Navigation principale'));
});
add_action('wp_enqueue_scripts', function () {
    $version = wp_get_theme()->get('Version');
    $style_version = (string) filemtime(get_stylesheet_directory() . '/style.css');
    wp_enqueue_style('fp-theme', get_stylesheet_uri(), array(), $style_version);
    wp_enqueue_script('fp-theme', get_theme_file_uri('/assets/site.js'), is_page('contact') ? array('fp-altcha') : array(), (string) filemtime(get_theme_file_path('/assets/site.js')), true);
    wp_script_add_data('fp-theme', 'strategy', 'defer');
});
// Start both local fonts before the stylesheet is parsed. Match CSS URLs exactly.
add_action('wp_head', function () {
    foreach (array('newsreader.woff2', 'feret-sans.woff2') as $font) {
        echo '<link rel="preload" href="' . esc_url(get_stylesheet_directory_uri() . '/assets/fonts/' . $font) . '" as="font" type="font/woff2" crossorigin>' . "\n";
    }
}, 1);
function fp_theme_info($key, $default = '') { return function_exists('fp_info') ? fp_info($key, $default) : $default; }
function fp_theme_preview() { return function_exists('fp_is_preview') ? fp_is_preview() : wp_get_environment_type() !== 'production'; }
function fp_theme_services() { return function_exists('fp_services') ? fp_services() : array(); }
function fp_theme_projects($limit = 6) { return function_exists('fp_projects') ? fp_projects($limit) : array(); }
function fp_theme_has_projects() { return function_exists('fp_has_projects') && fp_has_projects(); }
function fp_theme_phone() { return function_exists('fp_phone_display') ? fp_phone_display() : ''; }
function fp_theme_phone_uri() { return function_exists('fp_phone_uri') ? fp_phone_uri() : ''; }
function fp_theme_arrow() { return '<svg viewBox="0 0 24 24" width="21" height="21" aria-hidden="true" focusable="false"><path d="M4 12h15M13 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>'; }
function fp_theme_quote_url($service = '') {
    $url = home_url('/contact/');
    if (!$service && is_singular('fp_service')) { $service = get_post_field('post_name', get_queried_object_id()); }
    if ($service) { $url = add_query_arg('prestation', $service, $url); }
    return $url;
}
function fp_theme_quote_button($label = 'Prendre rendez-vous', $class = 'button--primary', $service = '') {
    echo '<a class="button ' . esc_attr($class) . '" href="' . esc_url(fp_theme_quote_url($service)) . '" data-fp-event="click_quote">' . esc_html($label) . fp_theme_arrow() . '</a>';
}
function fp_theme_phone_link($label = '', $class = 'text-link') {
    if (!fp_theme_phone_uri()) { return; }
    echo '<a class="' . esc_attr($class) . '" href="' . esc_url(fp_theme_phone_uri()) . '" data-fp-event="click_phone">' . esc_html($label ?: fp_theme_phone()) . '</a>';
}
function fp_theme_email_link() {
    $email = fp_theme_info('public_email');
    if (is_email($email)) { echo '<a class="email-link" href="' . esc_url('mailto:' . $email) . '">' . esc_html($email) . '</a>'; }
}
function fp_theme_navigation() {
    $links = array('/prestations/' => 'Prestations');
    if (fp_theme_has_projects()) { $links['/realisations/'] = 'Réalisations'; }
    $links['/entreprise/'] = 'L’entreprise';
    $links['/zone-intervention/'] = 'Zone d’intervention';
    foreach ($links as $path => $label) {
        $current = (is_post_type_archive('fp_service') && $path === '/prestations/') || (is_post_type_archive('fp_project') && $path === '/realisations/') || (is_page(trim($path, '/')));
        $in_section = (is_singular('fp_service') && $path === '/prestations/') || (is_singular('fp_project') && $path === '/realisations/');
        echo '<a href="' . esc_url(home_url($path)) . '"' . ($current ? ' aria-current="page"' : ($in_section ? ' class="is-current-section"' : '')) . '>' . esc_html($label) . '</a>';
    }
}
function fp_theme_service_label($post) {
    return get_the_excerpt($post) ?: wp_trim_words(wp_strip_all_tags($post->post_content), 24, '…');
}
function fp_theme_service_rows($limit = 0) {
    $services = fp_theme_services();
    if ($limit) { $services = array_slice($services, 0, $limit); }
    foreach ($services as $index => $service) {
        echo '<a class="service-row" href="' . esc_url(get_permalink($service)) . '"><span class="service-number" aria-hidden="true">' . esc_html(sprintf('%02d', $index + 1)) . '</span><span class="service-row-title">' . esc_html(get_the_title($service)) . '</span><span class="service-row-description">' . esc_html(fp_theme_service_label($service)) . '</span><span class="service-arrow">' . fp_theme_arrow() . '</span></a>';
    }
}
function fp_theme_project_cards($limit = 3) {
    foreach (fp_theme_projects($limit) as $project) {
        $title = get_the_title($project);
        $label = get_post_meta($project->ID, 'town', true);
        $illustration_titles = array(
            'exemple-sol-aspect-bois' => 'Sol aspect bois',
            'exemple-sejour-lumineux' => 'Murs clairs et plafond blanc',
            'exemple-salon-escalier' => 'Salon et escalier en bois',
        );
        $illustration_key = get_post_meta($project->ID, '_fp_stock_demo_key', true);
        if (isset($illustration_titles[$illustration_key])) {
            $title = $illustration_titles[$illustration_key];
            $label = 'Photo d’illustration';
        }
        echo '<article class="project-card"><a href="' . esc_url(get_permalink($project)) . '">';
        if (has_post_thumbnail($project)) { echo get_the_post_thumbnail($project, 'fp-project', array('loading' => 'lazy')); }
        echo '<div class="project-caption"><div><p class="eyebrow">' . esc_html($label) . '</p><h3>' . esc_html($title) . '</h3></div>' . fp_theme_arrow() . '</div></a></article>';
    }
}
function fp_theme_breadcrumb($parent = '', $url = '') {
    echo '<nav class="breadcrumb" aria-label="Fil d’Ariane"><a href="' . esc_url(home_url('/')) . '">Accueil</a><span aria-hidden="true">/</span>';
    if ($parent) { echo '<a href="' . esc_url($url) . '">' . esc_html($parent) . '</a><span aria-hidden="true">/</span>'; }
    echo '<span aria-current="page">' . esc_html(is_post_type_archive('fp_service') ? 'Prestations' : (is_post_type_archive('fp_project') ? 'Réalisations' : (is_page('contact') ? 'Prendre rendez-vous' : get_the_title()))) . '</span></nav>';
}
function fp_theme_contact_band() {
    echo '<section class="contact-band"><div class="container contact-band-inner"><div><h2>Parlons de vos travaux.</h2><p>Indiquez la commune et les travaux envisagés.</p></div><div class="contact-band-actions">';
    fp_theme_quote_button(); fp_theme_phone_link('', 'phone-large');
    echo '</div></div></section>';
}
function fp_theme_has_seo_plugin() { return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION') || defined('SEOPRESS_VERSION'); }
function fp_theme_seo_data() {
    $data = array(
        'home' => array('Peintre en bâtiment à Écouen | Feret Peinture', 'Un projet de peinture à Écouen ? Présentez vos travaux et la commune du chantier à Christophe Feret pour préparer votre demande de rendez-vous.'),
        'prestations' => array('Prestations de peinture et revêtements | Feret Peinture', 'Peinture intérieure, extérieure, revêtements muraux et sols : consultez les prestations proposées par Christophe Feret.'),
        'realisations' => array('Réalisations de Christophe Feret | Feret Peinture', 'Découvrez les travaux présentés par Christophe Feret : nature du chantier, commune et photographies publiées avec autorisation.'),
        'entreprise' => array('Christophe Feret, peintre à Écouen | Feret Peinture', 'Christophe Feret est implanté à Écouen, dans le Val-d’Oise. Découvrez l’entreprise et contactez Christophe pour vos travaux.'),
        'zone-intervention' => array('Peintre dans le Val-d’Oise, en Île-de-France et dans l’Oise | Feret Peinture', 'Basé à Écouen, Christophe Feret étudie vos travaux de peinture et de revêtements dans le Val-d’Oise, en Île-de-France et dans l’Oise. Présentez votre chantier.'),
        'contact' => array('Prendre rendez-vous à Christophe Feret | Feret Peinture', 'Décrivez votre projet, indiquez la commune du chantier et laissez un moyen de contact pour convenir d’un rendez-vous à Christophe Feret.'),
        'mentions-legales' => array('Mentions légales | Feret Peinture', 'Informations sur l’éditeur et l’hébergement du site Feret Peinture.'),
        'confidentialite' => array('Confidentialité | Feret Peinture', 'Informations sur l’utilisation des données communiquées dans une demande de rendez-vous et l’exercice de vos droits.'),
        'merci' => array('Votre demande | Feret Peinture', 'Suivi de votre demande de rendez-vous à Christophe Feret.'),
    );
    $key = is_front_page() ? 'home' : (is_post_type_archive('fp_service') ? 'prestations' : (is_post_type_archive('fp_project') ? 'realisations' : get_post_field('post_name', get_queried_object_id())));
    if (isset($data[$key])) { return $data[$key]; }
    if (is_404()) { return array('Page introuvable | Feret Peinture', 'Retrouvez l’accueil, les prestations ou la demande de rendez-vous de Feret Peinture.'); }
    if (is_singular()) { return array(get_the_title() . ' | Feret Peinture', wp_trim_words(wp_strip_all_tags(get_the_excerpt() ?: get_post_field('post_content', get_queried_object_id())), 28, '…')); }
    return array('Feret Peinture | Christophe Feret à Écouen', 'Peintre en bâtiment implanté à Écouen. Présentez votre projet à Christophe Feret.');
}
add_filter('pre_get_document_title', function ($title) { return fp_theme_has_seo_plugin() ? $title : fp_theme_seo_data()[0]; });
add_action('wp_head', function () {
    if (fp_theme_has_seo_plugin()) { return; }
    $data = fp_theme_seo_data();
    echo '<meta name="description" content="' . esc_attr($data[1]) . '">' . "\n";
    // WordPress emits singular canonicals; archives and homepage are handled here.
    if (!is_singular() && !is_404() && !is_search()) {
        $url = is_front_page() ? home_url('/') : (is_post_type_archive() ? get_post_type_archive_link(get_query_var('post_type')) : '');
        if ($url) { echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n"; }
    }
    echo '<meta property="og:title" content="' . esc_attr($data[0]) . '"><meta property="og:description" content="' . esc_attr($data[1]) . '"><meta property="og:type" content="website"><meta property="og:locale" content="fr_FR">' . "\n";
    echo '<meta property="og:image" content="' . esc_url(get_theme_file_uri('/assets/partage.png')) . '"><meta property="og:image:width" content="1200"><meta property="og:image:height" content="630"><meta property="og:image:alt" content="Feret Peinture - Christophe Feret, peintre en bâtiment à Écouen">' . "\n";
}, 3);
add_action('wp_head', function () { echo '<link rel="icon" href="' . esc_url(get_theme_file_uri('/assets/favicon.svg')) . '" type="image/svg+xml">' . "\n"; }, 3);
add_filter('wp_robots', function ($robots) { if (is_page('merci') || is_404()) { $robots['noindex'] = true; unset($robots['index']); } return $robots; });
