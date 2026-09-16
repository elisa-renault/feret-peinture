<?php
if (!defined('WP_CLI') || !WP_CLI) { exit(1); }
$old=get_page_by_path('devis'); $new=get_page_by_path('contact');
if ($old && $new && $old->ID !== $new->ID) { WP_CLI::error('Deux pages distinctes : migration interrompue.'); }
if ($old) {
 $result=wp_update_post(['ID'=>$old->ID,'post_name'=>'contact','post_title'=>'Prendre rendez-vous'],true);
 if (is_wp_error($result)) { WP_CLI::error($result->get_error_message()); }
}
if (!get_page_by_path('contact')) { WP_CLI::error('Page contact introuvable.'); }
WP_CLI::success('Page disponible sous /contact/.');
