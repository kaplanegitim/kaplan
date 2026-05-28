<?php
/**
 * SEO & sosyal meta — eklentisiz, Polylang-uyumlu.
 *
 * - Meta description (sayfaya özel)
 * - Open Graph + Twitter Card
 * - JSON-LD Organization schema (anasayfa)
 * - Anasayfa title tagline
 * - Dış kaynaklar için preconnect (fonts, cdnjs)
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

/**
 * Dış origin'ler için preconnect (crossorigin tam kontrol için doğrudan echo).
 * fonts.gstatic.com font dosyaları CORS ile çekildiğinden crossorigin şart.
 */
add_action('wp_head', function () {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" />' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />' . "\n";
    echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin />' . "\n";
}, 1);

/**
 * Google Analytics 4 (gtag.js) — Customizer'da ID girilmişse <head>'e ekle.
 * Customizer önizlemesinde tetiklenmez (istatistik kirlenmesin).
 */
add_action('wp_head', function () {
    if (is_customize_preview()) return;
    $id = get_theme_mod('kaplan_ga4_id', '');
    if (!$id) return;
    $id = esc_js($id);
    echo "\n<!-- Google Analytics 4 -->\n";
    echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr($id) . '"></script>' . "\n";
    echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{$id}');</script>\n";
}, 20);

/**
 * Anasayfa <title>'ına tagline ekle.
 */
add_filter('document_title_parts', function ($parts) {
    if (is_front_page() && empty($parts['tagline'])) {
        $parts['tagline'] = __('Eğitim · Danışmanlık · İş Zekası', 'kaplan');
    }
    return $parts;
});

/**
 * Sayfaya özel meta açıklaması üret.
 */
function kaplan_meta_description(): string {
    if (is_front_page()) {
        $tagline = get_bloginfo('description');
        if ($tagline) return $tagline;
        return __('Eğitim, danışmanlık ve iş zekası çözümleriyle şirketlere "Büyük Resmi Görebilmek" yolculuğunda destek veriyoruz.', 'kaplan');
    }
    if (is_singular()) {
        $post = get_queried_object();
        $text = $post->post_excerpt ?: $post->post_content;
        $text = wp_strip_all_tags(strip_shortcodes($text));
        $text = trim(preg_replace('/\s+/', ' ', $text));
        if ($text !== '') return wp_html_excerpt($text, 160, '…');
    }
    return get_bloginfo('description') ?: get_bloginfo('name');
}

/**
 * Paylaşım görseli (og:image) — öne çıkan görsel, yoksa tema varsayılanı.
 */
function kaplan_share_image(): string {
    if (is_singular() && has_post_thumbnail()) {
        $url = get_the_post_thumbnail_url(get_queried_object_id(), 'large');
        if ($url) return $url;
    }
    return apply_filters('kaplan_default_share_image', KAPLAN_URI . '/assets/img/hero/Resim-8.jpg');
}

/**
 * Geçerli sayfanın kanonik URL'i.
 */
function kaplan_current_url(): string {
    if (is_front_page()) return home_url('/');
    if (is_singular())   return get_permalink();
    return home_url('/');
}

/**
 * <head> içine SEO + sosyal meta yaz.
 */
add_action('wp_head', function () {
    $desc  = kaplan_meta_description();
    $title = wp_get_document_title();
    $url   = kaplan_current_url();
    $image = kaplan_share_image();
    $type  = (is_singular() && !is_front_page()) ? 'article' : 'website';
    $sitenm = get_bloginfo('name');

    // Dil (Polylang)
    $locale = function_exists('pll_current_language') ? pll_current_language('locale') : get_locale();

    echo "\n<!-- Kaplan SEO -->\n";
    printf('<meta name="description" content="%s" />' . "\n", esc_attr($desc));

    // Open Graph
    printf('<meta property="og:type" content="%s" />' . "\n", esc_attr($type));
    printf('<meta property="og:site_name" content="%s" />' . "\n", esc_attr($sitenm));
    printf('<meta property="og:title" content="%s" />' . "\n", esc_attr($title));
    printf('<meta property="og:description" content="%s" />' . "\n", esc_attr($desc));
    printf('<meta property="og:url" content="%s" />' . "\n", esc_url($url));
    printf('<meta property="og:image" content="%s" />' . "\n", esc_url($image));
    if ($locale) printf('<meta property="og:locale" content="%s" />' . "\n", esc_attr($locale));
    // Diğer dillerin locale'i (alternate)
    if (function_exists('pll_the_languages')) {
        $langs = pll_the_languages(['raw' => 1, 'hide_current' => 1]);
        if (is_array($langs)) {
            foreach ($langs as $l) {
                if (!empty($l['locale'])) printf('<meta property="og:locale:alternate" content="%s" />' . "\n", esc_attr($l['locale']));
            }
        }
    }

    // Twitter Card
    printf('<meta name="twitter:card" content="summary_large_image" />' . "\n");
    printf('<meta name="twitter:title" content="%s" />' . "\n", esc_attr($title));
    printf('<meta name="twitter:description" content="%s" />' . "\n", esc_attr($desc));
    printf('<meta name="twitter:image" content="%s" />' . "\n", esc_url($image));

    // JSON-LD Organization — yalnızca anasayfada
    if (is_front_page()) {
        $logo_id = get_theme_mod('custom_logo');
        $logo    = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : KAPLAN_URI . '/assets/img/logo_k.png';
        $same_as = [];
        if (function_exists('kaplan_opt')) {
            foreach (['kaplan_social_linkedin', 'kaplan_social_youtube', 'kaplan_social_instagram'] as $k) {
                $v = kaplan_opt($k);
                if ($v) $same_as[] = $v;
            }
        }
        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $sitenm,
            'url'      => home_url('/'),
            'logo'     => $logo,
            'description' => $desc,
        ];
        if (function_exists('kaplan_opt')) {
            if ($e = kaplan_opt('kaplan_email')) $schema['email'] = $e;
            if ($p = kaplan_opt('kaplan_phone')) $schema['telephone'] = $p;
        }
        if ($same_as) $schema['sameAs'] = $same_as;

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
    echo "<!-- /Kaplan SEO -->\n";
}, 5);
