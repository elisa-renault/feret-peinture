<?php
/** Update approved travel-area copy without changing launch or mail settings. */
if (!defined('WP_CLI') || !WP_CLI) { exit(1); }
$id = fp_information_id();
if (!$id) { WP_CLI::error('Fiche informations absente.'); }
$old = get_post_meta($id, 'confirmed_area', true);
$new = 'Depuis Écouen, Christophe Feret intervient dans le Val-d’Oise, en Île-de-France et dans l’Oise, selon la commune de votre chantier.';
if ($old === $new) { WP_CLI::success('Zone déjà mise à jour.'); return; }
if ($old !== 'Jusqu’à deux heures de trajet depuis Écouen') { WP_CLI::error('Zone personnalisée à préserver.'); }
if (!get_option('fp_area_copy_backup_20260916')) { add_option('fp_area_copy_backup_20260916', ['confirmed_area' => $old], '', false); }
update_post_meta($id, 'confirmed_area', $new);
WP_CLI::success('Zone d’intervention reformulée.');
