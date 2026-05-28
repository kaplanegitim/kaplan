<?php
/**
 * Page hero (compact banner) — inner pages için.
 *
 * Usage:
 *   set_query_var('hero_title',  '<span>Eğitim</span> programları');
 *   set_query_var('hero_sub',    'Veriyle çalışan herkes için');
 *   set_query_var('hero_crumb',  'Eğitimler');
 *   get_template_part('template-parts/page-hero');
 *
 * Defaults: title = get_the_title(), sub = ''.
 * Not: minimal stil — arka plan görseli yok (hero_bg artık kullanılmıyor).
 *
 * @package Kaplan
 */

$hero_title = get_query_var('hero_title', '');
$hero_sub   = get_query_var('hero_sub', '');
$hero_crumb = get_query_var('hero_crumb', '');

if ($hero_title === '' && is_singular()) {
    $hero_title = get_the_title();
}
if ($hero_crumb === '' && is_singular()) {
    $hero_crumb = get_the_title();
}
?>
<section class="page-hero">
    <div class="container page-hero__inner">
        <div class="breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Anasayfa', 'kaplan'); ?></a>
            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            <span class="current"><?php echo esc_html($hero_crumb); ?></span>
        </div>
        <h1><?php echo wp_kses_post($hero_title); ?></h1>
        <?php if ($hero_sub) : ?>
            <p class="page-hero__sub"><?php echo esc_html($hero_sub); ?></p>
        <?php endif; ?>
        <span class="page-hero__line"></span>
    </div>
</section>
