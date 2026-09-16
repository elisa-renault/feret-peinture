<?php if (!defined('ABSPATH')) { exit; } ?><!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#contenu">Aller au contenu</a>
<header class="site-header"><div class="container header-inner">
<a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Feret Peinture - accueil"><span class="brand-name">Feret<span>Peinture</span></span><span class="brand-signature">Christophe Feret · Écouen</span></a>
<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="navigation-principale"><span>Menu</span><svg width="23" height="18" viewBox="0 0 23 18" aria-hidden="true"><path d="M1 3h21M1 9h21M1 15h21" stroke="currentColor" stroke-width="1.5"/></svg></button>
<nav class="primary-nav" id="navigation-principale" aria-label="Navigation principale"><?php fp_theme_navigation(); ?><?php fp_theme_quote_button('Prendre rendez-vous', 'button--small button--primary nav-quote'); ?></nav>
</div></header>
<?php $message = fp_theme_info('temporary_message'); if ($message) : ?><div class="temporary-message"><div class="container"><?php echo esc_html($message); ?></div></div><?php endif; ?>
<main id="contenu" tabindex="-1">
