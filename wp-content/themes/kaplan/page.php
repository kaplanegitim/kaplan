<?php
/**
 * Standart sayfa şablonu (page.php).
 *
 * Page-hero + the_content + CTA. Front-page'de sadece content render edilir
 * (hero ve CTA üst-paneller front-page.php tarafından — Faz 4 — yönetilecek).
 *
 * @package Kaplan
 */

get_header();

$is_front = is_front_page();

if (!$is_front) {
    set_query_var('hero_title', get_the_title());
    set_query_var('hero_crumb', get_the_title());
    get_template_part('template-parts/page-hero');
}
?>

<section class="section">
    <div class="container" style="max-width: 800px;">
        <?php
        while (have_posts()) :
            the_post();

            if ($is_front && trim(strip_tags(get_the_content())) === '') {
                // Front page boşsa Faz 4 placeholder göster.
                ?>
                <div style="text-align:center; padding: 2rem;">
                    <span class="section-head__eyebrow"><?php esc_html_e('Tema kuruldu', 'kaplan'); ?></span>
                    <h2><?php esc_html_e('Anasayfa Faz 4 ile gelecek', 'kaplan'); ?></h2>
                    <p style="color: var(--c-muted);"><?php esc_html_e('Hero slider, feature kartları, portfolyo, ekip ve clients marquee — front-page.php bağlanınca devreye girecek.', 'kaplan'); ?></p>
                    <a class="btn btn--primary" href="<?php echo esc_url(kpl_localized_url('/bilesen-showcase/')); ?>" style="margin-top: 1rem;">
                        <?php esc_html_e('Bileşen Showcase\'i gör', 'kaplan'); ?>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <?php
            } else {
                the_content();
            }
        endwhile;
        ?>
    </div>
</section>

<?php
if (!$is_front) {
    set_query_var('cta_title', __('Bizimle iletişime geçin', 'kaplan'));
    set_query_var('cta_sub',   __('Sorularınız veya talepleriniz için ulaşabilirsiniz.', 'kaplan'));
    get_template_part('template-parts/cta-band');
}

get_footer();
