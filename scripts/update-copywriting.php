<?php
/** Apply approved copywriting edits; preserve customized content and save prior values. */
if (!defined('WP_CLI') || !WP_CLI) { exit(1); }
$expected = [
    'peinture-interieure' => ['b226f762973a9f6ea77a427e5f68a51baae60e0cea472d4df108e5e49d3e7454', '03a3ae08ed796a739894c856a63b35d5a72476d4f965d3ff6e1332acec24542b'],
    'peinture-exterieure' => ['b6523ec45b86abfba26b9970fa8c318babbff6ce0bcc13ec7de2d3a6360078de', '66ff855d4550294ec5f24e9b130bab5c35f6f216432151399af11d0b2f185aaa'],
    'revetements-muraux' => ['40ce0323134a523b46b5a77c77df2f51bb4ac4291a8a3152f94fee0f3eec26d4', '38a99a8768741a943625ca4153c32be3d60b2191bc3331f99dc1e6fc903c22be'],
    'revetements-sols' => ['00106ac882c97c06354c40c425f3844a72e55650fc5e6a6818b6a8c5e0426e16', 'acf46f429e57dcbe6a73c9f6c3ed3bdab4c45f9cfd1eed918d04d0f5ab864849'],
];
$changes = [];
foreach (fp_service_seed() as $slug => $copy) {
    $post = get_page_by_path($slug, OBJECT, 'fp_service');
    if (!$post) { WP_CLI::error('Prestation absente : ' . $slug); }
    if ($post->post_content === $copy['content'] && $post->post_excerpt === $copy['excerpt']) { continue; }
    $known_local_floor = $slug === 'revetements-sols' && hash('sha256', $post->post_content) === '9408ba2aa4bf92afedfbf264de4edda35eed7be9ba34b3ce3da0395f82f3973a' && hash('sha256', $post->post_excerpt) === '0d98f00f1732a1bf74ccc334a7cac2e589929729e7667836997e639517a6f887';
    if (!$known_local_floor && (hash('sha256', $post->post_content) !== $expected[$slug][0] || hash('sha256', $post->post_excerpt) !== $expected[$slug][1])) {
        WP_CLI::error('Texte personnalisé à préserver : ' . $slug . '. Aucun changement appliqué.');
    }
    $changes[$post->ID] = ['old' => ['post_content' => $post->post_content, 'post_excerpt' => $post->post_excerpt], 'new' => ['post_content' => $copy['content'], 'post_excerpt' => $copy['excerpt']]];
}
$contact = get_page_by_path('contact');
if ($contact && $contact->post_title === 'Prendre rendez-vous') {
    $changes[$contact->ID] = ['old' => ['post_title' => $contact->post_title], 'new' => ['post_title' => 'Demander un rendez-vous']];
}
$id = fp_information_id();
$old_presentation = $id ? get_post_meta($id, 'presentation', true) : '';
$new_presentation = 'Christophe Feret échange directement avec vous sur votre projet. Il se déplace pour examiner les surfaces et évaluer les travaux avant d’établir le devis.';
$update_presentation = $old_presentation === 'Christophe Feret est peintre en bâtiment à Écouen, dans le Val-d’Oise.';
$backup = ['posts' => $changes, 'information_id' => $id, 'presentation' => $old_presentation];
$key = 'fp_copywriting_backup_20260916';
if (($changes || $update_presentation) && !get_option($key) && !add_option($key, $backup, '', false)) { WP_CLI::error('Sauvegarde impossible.'); }
foreach ($changes as $post_id => $change) {
    wp_save_post_revision($post_id);
    $result = wp_update_post(wp_slash(array_merge(['ID' => $post_id], $change['new'])), true);
    if (is_wp_error($result)) { WP_CLI::error($result->get_error_message()); }
}
if ($update_presentation) { update_post_meta($id, 'presentation', $new_presentation); }
WP_CLI::success(count($changes) . ' contenus mis à jour ; présentation ' . ($update_presentation ? 'actualisée' : 'conservée') . '.');
