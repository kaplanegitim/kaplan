<?php
/**
 * 404 — sayfa bulunamadı.
 *
 * @package Kaplan
 */

get_header();

set_query_var('hero_title', __('Sayfa <span>bulunamadı</span>', 'kaplan'));
set_query_var('hero_sub',   __('Aradığınız sayfa taşınmış veya silinmiş olabilir.', 'kaplan'));
set_query_var('hero_crumb', '404');
get_template_part('template-parts/page-hero');
?>

<section class="section">
    <div class="container" style="text-align:center; max-width:680px;">
        <div style="font-family: var(--font-heading); font-size: clamp(6rem, 18vw, 12rem); font-weight: 800; line-height: 1; background: linear-gradient(135deg, var(--c-primary), var(--c-primary-700)); -webkit-background-clip: text; background-clip: text; color: transparent; margin-bottom: 1rem;">
            404
        </div>
        <h2><?php esc_html_e('Yolu kaybettik 😅', 'kaplan'); ?></h2>
        <p><?php esc_html_e('Aşağıdan arama yapabilir veya anasayfaya dönebilirsiniz.', 'kaplan'); ?></p>

        <div style="max-width:480px; margin: 2rem auto;">
            <?php get_search_form(); ?>
        </div>

        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn--primary">
            <i class="fa-solid fa-house"></i> <?php esc_html_e('Anasayfaya dön', 'kaplan'); ?>
        </a>
    </div>
</section>

<?php get_footer();
