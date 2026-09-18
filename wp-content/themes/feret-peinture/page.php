<?php get_header(); while (have_posts()) : the_post(); $slug = get_post_field('post_name', get_the_ID()); ?>
<div class="container"><?php fp_theme_breadcrumb(); ?></div>
<?php if ($slug === 'contact') : ?>
<section class="page-intro quote-intro container"><h1><?php echo esc_html(fp_theme_copy('contact', 'title', 'Demander un rendez-vous')); ?></h1><p class="intro-copy"><?php echo esc_html(fp_theme_copy('contact', 'intro', 'Appelez Christophe Feret ou indiquez la commune du chantier, les travaux envisagés et un moyen de vous joindre. Il vous recontactera pour convenir d’une visite sur place, avant d’établir le devis.')); ?></p></section>
<div class="container quote-layout"><section class="quote-form-wrap" aria-label="Formulaire de demande de rendez-vous"><?php if (function_exists('fp_render_quote_form')) { fp_render_quote_form(); } else { echo '<p>Le formulaire est en cours de préparation.</p>'; } ?></section><aside class="quote-aside"><p class="eyebrow"><?php echo esc_html(fp_theme_copy('contact', 'aside_label', 'Coordonnées')); ?></p><h2>Christophe Feret</h2><?php fp_theme_phone_link('', 'phone-large'); fp_theme_email_link(); ?><p><?php echo esc_html(fp_theme_copy('contact', 'aside_text', 'Peintre à Écouen, Val-d’Oise')); ?></p></aside></div>
<?php elseif ($slug === 'entreprise') : ?>
<section class="page-intro container company-intro">
    <h1><?php echo esc_html(fp_theme_copy('entreprise', 'title', 'Christophe Feret, peintre à Écouen.')); ?></h1>
    <p class="intro-copy"><?php echo esc_html(fp_theme_copy('entreprise', 'intro', 'Peinture, décoration, revêtements de sols et murs : une entreprise locale créée en 2008.')); ?></p>
</section>
<section class="container company-story section section--topless" aria-labelledby="company-person-title">
    <aside class="company-person">
        <span class="company-signature" aria-hidden="true">F.</span>
        <h2 id="company-person-title">Christophe Feret</h2>
        <p><?php echo esc_html(fp_theme_info('presentation', 'Christophe Feret est votre contact pour présenter vos travaux et convenir d’un rendez-vous sur place.')); ?></p>
        <p><?php echo esc_html(fp_theme_copy('entreprise', 'person_text', 'Un interlocuteur pour vos travaux, de la préparation aux finitions.')); ?></p>
    </aside>
    <div class="company-approach prose">
        <h2><?php echo esc_html(fp_theme_copy('entreprise', 'preparation_title', 'Préparer les surfaces avant de peindre')); ?></h2>
        <p><?php echo esc_html(fp_theme_copy('entreprise', 'preparation_text', 'Avant la peinture, il y a le travail du support : lessiver, reboucher les fissures, enduire et poncer. Christophe Feret adapte cette préparation à l’état des murs, des plafonds ou des boiseries à rénover.')); ?></p>
        <h2><?php echo esc_html(fp_theme_copy('entreprise', 'quote_title', 'Un devis détaillé, pièce par pièce')); ?></h2>
        <p><?php echo esc_html(fp_theme_copy('entreprise', 'quote_text', 'La préparation, les finitions, les quantités et les prix sont décrits dans le devis. Vous savez ce qui est prévu pour chaque pièce ou chaque élément du chantier.')); ?></p>
        <div class="company-contact">
            <h2><?php echo esc_html(fp_theme_copy('entreprise', 'contact_title', 'Parlons de vos travaux')); ?></h2>
            <p><?php echo esc_html(fp_theme_copy('entreprise', 'contact_text', 'Quelques mots sur votre projet et la commune du chantier suffisent pour un premier échange avec Christophe Feret.')); ?></p>
            <?php fp_theme_quote_button(); ?>
        </div>
    </div>
</section>
<?php elseif ($slug === 'zone-intervention') : ?>
<section class="page-intro container"><p class="eyebrow">Zone d’intervention</p><h1><?php echo esc_html(fp_theme_copy('zone-intervention', 'title', 'Vos travaux dans le Val-d’Oise et les environs.')); ?></h1><p class="intro-copy"><?php echo esc_html(fp_theme_copy('zone-intervention', 'intro', 'Depuis Écouen, Christophe Feret intervient dans le Val-d’Oise, en Île-de-France et dans l’Oise, selon la commune de votre chantier.')); ?></p></section>
<section class="container locality-grid section section--topless"><div class="prose"><h2><?php echo esc_html(fp_theme_copy('zone-intervention', 'near_title', 'Un peintre près de chez vous')); ?></h2><p><?php echo esc_html(fp_theme_copy('zone-intervention', 'near_text', 'Écouen, Ézanville, Domont, Montmorency, Sarcelles ou L’Isle-Adam : Christophe Feret accompagne vos projets de peinture et de revêtements dans le Val-d’Oise. Dans l’Oise, les secteurs de Chantilly, Gouvieux, Senlis et Compiègne sont également accessibles.')); ?></p><h2><?php echo esc_html(fp_theme_copy('zone-intervention', 'far_title', 'Votre chantier est plus loin ?')); ?></h2><p><?php echo esc_html(fp_theme_copy('zone-intervention', 'far_text', 'Paris et les autres départements franciliens, mais aussi les secteurs d’Amiens, de Rouen ou de Reims peuvent être envisagés.')); ?></p><p><?php echo esc_html(fp_theme_copy('zone-intervention', 'question_text', 'Votre commune n’est pas citée ? Indiquez-la dans votre demande : Christophe Feret vous confirmera la possibilité d’intervention.')); ?></p><?php fp_theme_quote_button(); ?></div><div class="locality-card"><span class="locality-postcode">95440</span><span class="locality-name">Écouen</span><span class="locality-caption"><?php echo esc_html(fp_theme_copy('zone-intervention', 'card_caption', 'Le point de départ de vos projets')); ?></span><span class="locality-line" aria-hidden="true"></span></div></section>
<?php elseif ($slug === 'merci') : ?>
<section class="container thank-you-page"><p class="eyebrow">Votre demande de rendez-vous</p><h1><?php echo function_exists('fp_quote_success_verified') && fp_quote_success_verified() ? 'Merci pour votre demande.' : 'Parlons de votre projet.'; ?></h1><?php if (function_exists('fp_quote_success_verified') && fp_quote_success_verified()) : ?><p class="intro-copy" data-form-success>Votre message a été envoyé. Christophe Feret vous recontactera pour fixer le rendez-vous.</p><p>Vous avez une précision à ajouter à votre projet ? Vous pouvez écrire à Christophe Feret par email.</p><?php fp_theme_email_link(); ?><?php else : ?><p class="intro-copy">Pour transmettre votre projet à Christophe Feret, utilisez le formulaire de demande de rendez-vous.</p><?php fp_theme_quote_button(); ?><?php endif; ?><a class="text-link" href="<?php echo esc_url(home_url('/')); ?>">Retour à l’accueil <?php echo fp_theme_arrow(); ?></a></section>
<?php elseif (in_array($slug, array('mentions-legales', 'confidentialite'), true)) : ?>
<section class="page-intro container legal-intro"><p class="eyebrow">Les informations du site</p><h1><?php the_title(); ?></h1></section><article class="container prose legal-content"><?php if (trim(get_the_content())) { the_content(); } elseif ($slug === 'mentions-legales') { get_template_part('inc/legal-draft'); } else { get_template_part('inc/privacy-draft'); } ?></article>
<?php else : ?>
<section class="page-intro container"><h1><?php the_title(); ?></h1></section><article class="container prose standard-page"><?php the_content(); ?></article>
<?php endif; endwhile; get_footer(); ?>
