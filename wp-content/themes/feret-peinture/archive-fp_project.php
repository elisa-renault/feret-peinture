<?php get_header(); ?>
<div class="container"><?php fp_theme_breadcrumb(); ?></div>
<section class="page-intro container"><h1>Nos <em>réalisations</em></h1></section>
<section class="section section--topless container"><div class="project-grid"><?php fp_theme_project_cards(60); ?></div><?php if (!fp_theme_has_projects() && fp_theme_preview()) : ?><div class="editorial-note"><p>La galerie apparaîtra dès la publication d’un premier chantier avec des photographies autorisées.</p></div><?php endif; ?></section>
<?php fp_theme_contact_band(); get_footer(); ?>
