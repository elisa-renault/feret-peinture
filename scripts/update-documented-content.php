<?php
/** Explicit, repeatable editorial update. Run with wp eval-file; backs up changed fields in an option. */
if (!defined('WP_CLI') || !WP_CLI) { exit(1); }
$expected = [
    'peinture-interieure' => ['103b56439ace1e9b386a606c0f6f0bd70377bad14f9dfcd04f570d7a05ad7c62', '70a6d036b35141c393cb95acc163253e52be9ea391b80d84b92e21a5e1271602'],
    'peinture-exterieure' => ['ea9eac0fcf9c1bb881cec8501e6482e1a2f9e422bb81b2660ee6013b7e15bddb', 'e8f10246f9e0262eec11ea0046cb50bcc06a711ed10669392306cdd0ae7c2394'],
    'revetements-muraux' => ['16d054c18986649545859338d5133eafefbf00a309e343c4ab509b4d84827107', '584bfb7d25bf5926fb0e4ba110660327043d4267998e7d2eb64d27359170caec'],
    'revetements-sols' => ['a5b271d70c8b0c79aeae88e41929622499f8fd24a8e7af2aeafc060fd84c3abb', '2512e2aefd75a4b6dd377e324c459866e29e161c3bf6bc8d0414f7947e07739f'],
];
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
if ($changes && !get_option('fp_documented_content_backup_20260916')) {
    if (!add_option('fp_documented_content_backup_20260916', $changes, '', false)) { WP_CLI::error('Sauvegarde impossible.'); }
}
foreach ($changes as $id => $change) {
    wp_save_post_revision($id);
    $result = wp_update_post(wp_slash(['ID' => $id, 'post_content' => $change['new']['content'], 'post_excerpt' => $change['new']['excerpt']]), true);
    if (is_wp_error($result)) { WP_CLI::error($result->get_error_message()); }
}
WP_CLI::success(count($changes) . ' prestations mises à jour. Coordonnées et réglages de publication conservés.');
