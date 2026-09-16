<?php
/** Apply source-verified service corrections, preserving unrelated edits. */
if (!defined('WP_CLI') || !WP_CLI) { exit(1); }
$expected = [
    'revetements-muraux' => '260f4902a3a00288df1c2e2d3d3a8967e4512ec6292e59e8486d1203c742463a',
    'revetements-sols' => '2f10310f1bafedb96755950268d73c63a4dc7edfe717ce9b0a988c0cb1f70f14',
 ];
$changes=[];
foreach ($expected as $slug=>$hash) {
 $post=get_page_by_path($slug, OBJECT, 'fp_service');
 if (!$post) { WP_CLI::error('Prestation absente : '.$slug); }
 $new=fp_service_seed()[$slug]['content'];
 if ($post->post_content===$new) { continue; }
 if (hash('sha256',$post->post_content)!==$hash) { WP_CLI::error('Texte personnalisé à préserver : '.$slug); }
 $changes[$post->ID]=['old'=>$post->post_content,'new'=>$new];
}
if ($changes && !get_option('fp_service_verification_backup_20260916')) { add_option('fp_service_verification_backup_20260916',$changes,'',false); }
foreach ($changes as $id=>$change) {
 wp_save_post_revision($id);
 $result=wp_update_post(wp_slash(['ID'=>$id,'post_content'=>$change['new']]),true);
 if (is_wp_error($result)) { WP_CLI::error($result->get_error_message()); }
}
WP_CLI::success(count($changes).' prestations corrigées.');
