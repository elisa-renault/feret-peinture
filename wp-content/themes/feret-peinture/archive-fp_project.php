<?php get_header(); ?>
<div class="container"><?php fp_theme_breadcrumb(); ?></div>
<section class="page-intro container"><p class="eyebrow">Les réalisations</p><h1>Le travail,<br><em>en images.</em></h1><p class="intro-copy">Des chantiers présentés par Christophe Feret, avec leur commune et la nature des travaux réalisés.</p></section>
<section class="section section--topless container"><div class="project-grid"><?php fp_theme_project_cards(60); ?></div><?php if (!fp_theme_has_projects() && fp_theme_preview()) : ?><div class="editorial-note"><p>La galerie apparaîtra dès la publication d’un premier chantier avec des photographies autorisées. Aucun chantier fictif n’est présenté ici.</p></div><?php endif; ?></section>
<?php fp_theme_contact_band(); get_footer(); ?>
