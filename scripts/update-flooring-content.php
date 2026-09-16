<?php
/** Apply the source-verified flooring additions, preserving any unknown custom copy. */
if (!defined('WP_CLI') || !WP_CLI) { exit(1); }
$post = get_page_by_path('revetements-sols', OBJECT, 'fp_service');
if (!$post) { WP_CLI::error('Prestation sols absente.'); }
$copy = fp_service_seed()['revetements-sols'];
if ($post->post_content === $copy['content'] && $post->post_excerpt === $copy['excerpt']) {
    WP_CLI::success('Prestation sols déjà à jour.'); return;
}
if (hash('sha256', $post->post_content) !== '2f10310f1bafedb96755950268d73c63a4dc7edfe717ce9b0a988c0cb1f70f14' || hash('sha256', $post->post_excerpt) !== '0d98f00f1732a1bf74ccc334a7cac2e589929729e7667836997e639517a6f887') {
    WP_CLI::error('Texte personnalisé à préserver. Aucun changement appliqué.');
}
$backup = ['ID' => $post->ID, 'post_content' => $post->post_content, 'post_excerpt' => $post->post_excerpt];
$key = 'fp_flooring_content_backup_20260916';
if (!get_option($key) && !add_option($key, $backup, '', false)) { WP_CLI::error('Sauvegarde impossible.'); }
wp_save_post_revision($post->ID);
$result = wp_update_post(wp_slash(['ID' => $post->ID, 'post_content' => $copy['content'], 'post_excerpt' => $copy['excerpt']]), true);
if (is_wp_error($result)) { WP_CLI::error($result->get_error_message()); }
WP_CLI::success('Prestation sols mise à jour : PVC collé et carrelage.');
