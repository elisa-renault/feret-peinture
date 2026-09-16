<?php
/** Sync reviewed legal templates into pages, retaining a private rollback snapshot. */
if (!defined('WP_CLI') || !WP_CLI) { exit(1); }
if (untrailingslashit(home_url()) !== 'https://feret-peinture.fr') { WP_CLI::error('Unexpected target site.'); }
$updates = [];
foreach (['mentions-legales' => 'legal-draft.php', 'confidentialite' => 'privacy-draft.php'] as $slug => $file) {
    $page = get_page_by_path($slug);
    if (!$page) { WP_CLI::error('Missing page: ' . $slug); }
    ob_start();
    include get_stylesheet_directory() . '/inc/' . $file;
    $content = trim(ob_get_clean());
    if (strlen(wp_strip_all_tags($content)) < 150) { WP_CLI::error('Incomplete content: ' . $slug); }
    $updates[] = ['ID' => $page->ID, 'old' => $page->post_content, 'content' => $content];
}
$backup = 'fp_legal_snapshot_' . gmdate('Ymd_His');
if (!add_option($backup, $updates, '', false)) { WP_CLI::error('Snapshot failed.'); }
foreach ($updates as $update) {
    $result = wp_update_post(wp_slash(['ID' => $update['ID'], 'post_content' => $update['content']]), true);
    if (is_wp_error($result)) { WP_CLI::error($result->get_error_message()); }
    if (get_post_field('post_content', $update['ID']) !== $update['content']) { WP_CLI::error('Content mismatch.'); }
}
// Do not set legal, privacy, delivery or launch approvals: outstanding infrastructure facts remain.
WP_CLI::success('Two legal pages synchronized. Snapshot: ' . $backup . '. Approval flags unchanged.');
