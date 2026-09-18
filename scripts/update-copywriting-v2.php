<?php
/** Targeted editorial migration with preflight and reversible content backup. */
if (!defined('WP_CLI') || !WP_CLI) { exit(1); }
$expected = ['revetements-muraux' => ['78917ff6f2adb2baa1d0083addf45ad0aa38147b95d2936787c0fa757ef29c46', 'a07f09570c098ff8facac25a56ce15e1bdcd76a092b94ca372958f047b7b28b5'], 'revetements-sols' => ['e19798326bed6496baedac8dd0f477eff678c4380d26a34753c72b4e636c17af']];
$changes = [];
foreach ($expected as $slug => $hashes) {
    $post = get_page_by_path($slug, OBJECT, 'fp_service');
    if (!$post) { WP_CLI::error('Prestation absente : ' . $slug); }
    $new = fp_service_seed()[$slug]['content'];
    if ($post->post_content === $new) { continue; }
    if (!in_array(hash('sha256', $post->post_content), $hashes, true)) { WP_CLI::error('Contenu personnalisé à préserver : ' . $slug); }
    $changes[$post->ID] = ['old' => ['post_content' => $post->post_content], 'new' => ['post_content' => $new]];
}
foreach (['merci' => ['Votre demande de devis', 'Votre demande de rendez-vous'], 'zone-intervention' => ['Votre projet et sa localisation', 'Zone d’intervention']] as $slug => $titles) {
    $post = get_page_by_path($slug);
    if ($post && $post->post_title === $titles[0]) { $changes[$post->ID] = ['old' => ['post_title' => $post->post_title], 'new' => ['post_title' => $titles[1]]]; }
}
$id = fp_information_id();
$old = $id ? get_post_meta($id, 'presentation', true) : '';
$new = 'Christophe Feret est votre contact pour présenter vos travaux et convenir d’un rendez-vous sur place.';
$known = ['Christophe échange directement avec vous sur votre projet. Il se déplace pour examiner les surfaces et évaluer les travaux avant d’établir le devis.', 'Christophe Feret échange directement avec vous sur votre projet. Il se déplace pour examiner les surfaces et évaluer les travaux avant d’établir le devis.'];
$update = in_array($old, $known, true);
$key = 'fp_copywriting_v2_backup_20260916';
if (($changes || $update) && !get_option($key) && !add_option($key, ['posts' => $changes, 'information_id' => $id, 'presentation' => $old], '', false)) { WP_CLI::error('Sauvegarde impossible.'); }
foreach ($changes as $post_id => $change) {
    wp_save_post_revision($post_id);
    $result = wp_update_post(wp_slash(array_merge(['ID' => $post_id], $change['new'])), true);
    if (is_wp_error($result)) { WP_CLI::error($result->get_error_message()); }
}
if ($update) { update_post_meta($id, 'presentation', $new); }
WP_CLI::success(count($changes) . ' contenus mis à jour ; présentation ' . ($update ? 'actualisée' : 'conservée') . '.');
