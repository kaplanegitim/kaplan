<?php
/**
 * CTA Band — koyu gradient bant, iki buton.
 *
 * Usage:
 *   set_query_var('cta_title', 'Bir sonraki adımı planlayalım');
 *   set_query_var('cta_sub',   'Size en uygun çözümü konuşalım.');
 *   get_template_part('template-parts/cta-band');
 *
 * @package Kaplan
 */

$cta_title = get_query_var('cta_title', __('Bir sonraki adımı birlikte planlayalım', 'kaplan'));
$cta_sub   = get_query_var('cta_sub',   __('Eğitim, danışmanlık ya da iş zekası — size en uygun çözümü konuşalım.', 'kaplan'));
$cta_btn1  = get_query_var('cta_btn1',  __('Eğitim Talep Formu', 'kaplan'));
$cta_btn2  = get_query_var('cta_btn2',  __('İletişime Geç', 'kaplan'));

// Buton URL'leri (dil-duyarlı default'lar, override edilebilir).
$cta_url1  = get_query_var('cta_url1', '');
if ($cta_url1 === '') {
    $cta_url1 = function_exists('kpl_localized_url') ? kpl_localized_url('/egitim-talep-formu/') : home_url('/egitim-talep-formu/');
}
$cta_url2  = get_query_var('cta_url2', '');
if ($cta_url2 === '') {
    $email    = function_exists('kaplan_opt') ? kaplan_opt('kaplan_email') : 'bilgi@kaplanegitim.com';
    $cta_url2 = 'mailto:' . $email;
}
?>
<section class="cta-band" style="background: linear-gradient(135deg, var(--c-primary-700) 0%, var(--c-bg-dark) 100%);">
    <div class="container cta-band__inner">
        <div>
            <h2><?php echo esc_html($cta_title); ?></h2>
            <p><?php echo esc_html($cta_sub); ?></p>
        </div>
        <div class="cta-band__actions">
            <a class="btn btn--primary" href="<?php echo esc_url($cta_url1); ?>"><?php echo esc_html($cta_btn1); ?></a>
            <a class="btn btn--ghost-light" href="<?php echo esc_url($cta_url2); ?>"><?php echo esc_html($cta_btn2); ?></a>
        </div>
    </div>
</section>
