<?php if (!defined('ABSPATH')) { exit; } ?>
</main>
<footer class="site-footer"><div class="container">
<div class="footer-top"><div><a class="brand footer-brand" href="<?php echo esc_url(home_url('/')); ?>"><span class="brand-name">Feret<span>Peinture</span></span></a><p>Christophe Feret - Peintre en bâtiment<br>Écouen, Val-d’Oise</p></div><div class="footer-contact"><p class="eyebrow">Un projet à présenter ?</p><?php fp_theme_phone_link('', 'phone-large'); fp_theme_email_link(); ?><a class="text-link" href="<?php echo esc_url(fp_theme_quote_url()); ?>" data-fp-event="click_quote">Prendre rendez-vous <?php echo fp_theme_arrow(); ?></a></div><nav aria-label="Navigation de pied de page"><a href="<?php echo esc_url(home_url('/prestations/')); ?>">Prestations</a><a href="<?php echo esc_url(home_url('/entreprise/')); ?>">L’entreprise</a><a href="<?php echo esc_url(home_url('/zone-intervention/')); ?>">Zone d’intervention</a></nav></div>
<div class="footer-bottom"><span>© <?php echo esc_html(wp_date('Y')); ?> Feret Peinture</span><div><a href="<?php echo esc_url(home_url('/mentions-legales/')); ?>">Mentions légales</a><a href="<?php echo esc_url(home_url('/confidentialite/')); ?>">Confidentialité</a></div><span>Site conçu par <a href="https://aliant.fr/" target="_blank" rel="noopener noreferrer">Aliant</a></span></div>
</div></footer>
<?php wp_footer(); ?>
</body></html>
