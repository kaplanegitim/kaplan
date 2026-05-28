<?php
/**
 * Sosyal medya ikonları — Customizer theme_mod'larından beslenir.
 * Boş URL'li ikon render edilmez.
 *
 * Usage:
 *   set_query_var('social_class', 'footer__social'); // veya 'topbar__social'
 *   get_template_part('template-parts/social-links');
 *
 * @package Kaplan
 */

$social_class = get_query_var('social_class', 'topbar__social');

$links = [
    'kaplan_social_linkedin'  => ['LinkedIn',  'fa-linkedin-in'],
    'kaplan_social_youtube'   => ['YouTube',   'fa-youtube'],
    'kaplan_social_instagram' => ['Instagram', 'fa-instagram'],
];

$out = '';
foreach ($links as $key => $meta) {
    $url = function_exists('kaplan_opt') ? kaplan_opt($key) : '';
    if ($url === '') continue;
    $out .= sprintf(
        '<a href="%s" target="_blank" rel="noopener" aria-label="%s"><i class="fa-brands %s"></i></a>',
        esc_url($url),
        esc_attr($meta[0]),
        esc_attr($meta[1])
    );
}

if ($out !== '') {
    printf('<div class="%s">%s</div>', esc_attr($social_class), $out);
}
