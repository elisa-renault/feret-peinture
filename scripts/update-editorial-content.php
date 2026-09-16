<?php
/** Explicit, repeatable editorial update. Run with wp eval-file; backs up changed fields in an option. */
if (!defined('WP_CLI') || !WP_CLI) { exit(1); }
$expected = [
    'peinture-interieure' => ['b226f762973a9f6ea77a427e5f68a51baae60e0cea472d4df108e5e49d3e7454', '03a3ae08ed796a739894c856a63b35d5a72476d4f965d3ff6e1332acec24542b'],
    'peinture-exterieure' => ['304b964c99e954f5880ffebd793ff8d52c193ac2134549a8a81f016bb921a7cd', '66ff855d4550294ec5f24e9b130bab5c35f6f216432151399af11d0b2f185aaa'],
    'revetements-muraux' => ['90eebd4f3402a6eb5cf95e64da55b2f669dacb4bfe7b862fbcca4fc31a5126a4', '38a99a8768741a943625ca4153c32be3d60b2191bc3331f99dc1e6fc903c22be'],
    'revetements-sols' => ['e8a0b30cd9d0eae5a14214ef00c63fc273b96cfc59fc821c232c737bd9c0948a', '0d98f00f1732a1bf74ccc334a7cac2e589929729e7667836997e639517a6f887'],
];
$information = get_page_by_path('mes-informations', OBJECT, 'fp_information');
$old_presentation = 'Christophe Feret est implanté à Écouen, dans le Val-d’Oise. L’entreprise exerce une activité de travaux de peinture.';
$new_presentation = 'Christophe Feret est peintre en bâtiment à Écouen, dans le Val-d’Oise.';
$update_presentation = $information && get_post_meta($information->ID, 'presentation', true) === $old_presentation;
$changes = [];
foreach (fp_service_seed() as $slug => $copy) {
    $post = get_page_by_path($slug, OBJECT, 'fp_service');
    if (!$post) { WP_CLI::error('Prestation absente : ' . $slug); }
    if ($post->post_content === $copy['content'] && $post->post_excerpt === $copy['excerpt']) { continue; }
    if (hash('sha256', $post->post_content) !== $expected[$slug][0] || hash('sha256', $post->post_excerpt) !== $expected[$slug][1]) {
        WP_CLI::error('Texte personnalisé à préserver : ' . $slug . '. Aucun changement appliqué.');
    }
    $changes[$post->ID] = ['old' => ['post_content' => $post->post_content, 'post_excerpt' => $post->post_excerpt], 'new' => $copy];
}
if ($changes && !get_option('fp_editorial_content_backup_20260916')) {
    if (!add_option('fp_editorial_content_backup_20260916', $changes, '', false)) { WP_CLI::error('Sauvegarde impossible.'); }
}
foreach ($changes as $id => $change) {
    wp_save_post_revision($id);
    $result = wp_update_post(wp_slash(['ID' => $id, 'post_content' => $change['new']['content'], 'post_excerpt' => $change['new']['excerpt']]), true);
    if (is_wp_error($result)) { WP_CLI::error($result->get_error_message()); }
}
if ($update_presentation) {
    if (!get_option('fp_editorial_presentation_backup_20260916') && !add_option('fp_editorial_presentation_backup_20260916', ['ID' => $information->ID, 'presentation' => $old_presentation], '', false)) {
        WP_CLI::error('Sauvegarde de la présentation impossible.');
    }
    update_post_meta($information->ID, 'presentation', $new_presentation);
}
WP_CLI::log($update_presentation ? 'Présentation simplifiée.' : 'Présentation conservée.');
WP_CLI::success(count($changes) . ' prestations mises à jour. Coordonnées et réglages de publication conservés.');
