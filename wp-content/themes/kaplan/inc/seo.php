<?php
/**
 * SEO & sosyal meta — eklentisiz, Polylang-uyumlu.
 *
 * - Anasayfa <title> tagline (B2B odaklı, 50-60 char ideal)
 * - Sayfaya özel meta description (per-page override desteği ile)
 * - <link rel="canonical">
 * - Open Graph + Twitter Card
 * - JSON-LD @graph: EducationalOrganization + (adres varsa) LocalBusiness
 * - GA4 (gtag.js) — Customizer'dan ID
 * - Dış kaynaklar için preconnect (fonts, cdnjs)
 * - Arama sonuçları noindex
 * - Per-page SEO meta-box (_kaplan_seo_title, _kaplan_seo_desc)
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

/**
 * Dış origin'ler için preconnect.
 */
add_action('wp_head', function () {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" />' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />' . "\n";
    echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin />' . "\n";
}, 1);

/**
 * Google Analytics 4 (gtag.js) — Customizer'da ID girilmişse <head>'e ekle.
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
 * Anasayfa <title>'ına tagline ekle (54 char hedef).
 */
add_filter('document_title_parts', function ($parts) {
    if (is_front_page() && empty($parts['tagline'])) {
        $parts['tagline'] = __('Kurumsal Eğitim, Danışmanlık & İş Zekası', 'kaplan');
    }
    return $parts;
});

/**
 * Yazı/sayfa için elle girilmiş SEO title varsa onu kullan.
 */
add_filter('pre_get_document_title', function ($title) {
    if (!is_singular()) return $title;
    $custom = (string) get_post_meta(get_queried_object_id(), '_kaplan_seo_title', true);
    if ($custom !== '') {
        return $custom . ' – ' . get_bloginfo('name');
    }
    return $title;
}, 5);

/**
 * Arama sonuçlarını noindex/nofollow yap.
 */
add_filter('wp_robots', function ($robots) {
    if (is_search()) {
        $robots['noindex']  = true;
        $robots['nofollow'] = true;
    }
    return $robots;
});

/**
 * Sayfaya özel meta açıklaması — per-page override > excerpt > content > tagline.
 */
function kaplan_meta_description(): string {
    // Per-page override.
    if (is_singular()) {
        $custom = (string) get_post_meta(get_queried_object_id(), '_kaplan_seo_desc', true);
        if ($custom !== '') return $custom;
    }
    if (is_front_page()) {
        // 140-150 char arası odaklı, B2B vurgulu.
        return __('Şirketlere kurumsal eğitim, yönetim danışmanlığı ve iş zekası yazılım çözümleriyle "Büyük Resmi Görebilmek" yolculuğunda destek veriyoruz.', 'kaplan');
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
    $desc   = kaplan_meta_description();
    $title  = wp_get_document_title();
    $url    = kaplan_current_url();
    $image  = kaplan_share_image();
    $type   = (is_singular() && !is_front_page()) ? 'article' : 'website';
    $sitenm = get_bloginfo('name');
    $locale = function_exists('pll_current_language') ? pll_current_language('locale') : get_locale();

    echo "\n<!-- Kaplan SEO -->\n";
    printf('<meta name="description" content="%s" />' . "\n", esc_attr($desc));

    // Anasayfa için canonical (WP core sadece singular için çıkartır).
    if (is_front_page()) {
        printf('<link rel="canonical" href="%s" />' . "\n", esc_url($url));
    }

    // Open Graph.
    printf('<meta property="og:type" content="%s" />' . "\n", esc_attr($type));
    printf('<meta property="og:site_name" content="%s" />' . "\n", esc_attr($sitenm));
    printf('<meta property="og:title" content="%s" />' . "\n", esc_attr($title));
    printf('<meta property="og:description" content="%s" />' . "\n", esc_attr($desc));
    printf('<meta property="og:url" content="%s" />' . "\n", esc_url($url));
    printf('<meta property="og:image" content="%s" />' . "\n", esc_url($image));
    if ($locale) printf('<meta property="og:locale" content="%s" />' . "\n", esc_attr($locale));
    if (function_exists('pll_the_languages')) {
        $langs = pll_the_languages(['raw' => 1, 'hide_current' => 1]);
        if (is_array($langs)) {
            foreach ($langs as $l) {
                if (!empty($l['locale'])) printf('<meta property="og:locale:alternate" content="%s" />' . "\n", esc_attr($l['locale']));
            }
        }
    }

    // Twitter Card.
    printf('<meta name="twitter:card" content="summary_large_image" />' . "\n");
    printf('<meta name="twitter:title" content="%s" />' . "\n", esc_attr($title));
    printf('<meta name="twitter:description" content="%s" />' . "\n", esc_attr($desc));
    printf('<meta name="twitter:image" content="%s" />' . "\n", esc_url($image));

    // JSON-LD — yalnızca anasayfada (EducationalOrganization + opsiyonel LocalBusiness).
    if (is_front_page()) {
        kaplan_print_org_schema($sitenm, $desc);
    }
    echo "<!-- /Kaplan SEO -->\n";
}, 5);

/**
 * EducationalOrganization + (adres varsa) LocalBusiness schema.
 */
function kaplan_print_org_schema(string $sitenm, string $desc): void {
    $home    = home_url('/');
    $logo_id = get_theme_mod('custom_logo');
    $logo    = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : KAPLAN_URI . '/assets/img/logo_k.png';

    // sameAs (boş URL'leri at).
    $same_as = [];
    if (function_exists('kaplan_opt')) {
        foreach (['kaplan_social_linkedin', 'kaplan_social_youtube', 'kaplan_social_instagram'] as $k) {
            $v = kaplan_opt($k);
            if ($v) $same_as[] = $v;
        }
    }

    $phone   = function_exists('kaplan_opt') ? kaplan_opt('kaplan_phone') : '';
    $email   = function_exists('kaplan_opt') ? kaplan_opt('kaplan_email') : '';
    $address = function_exists('kaplan_opt') ? kaplan_opt('kaplan_address') : '';

    // EducationalOrganization (her zaman).
    $org = [
        '@type'       => 'EducationalOrganization',
        '@id'         => $home . '#org',
        'name'        => $sitenm,
        'url'         => $home,
        'logo'        => $logo,
        'description' => $desc,
    ];
    if ($email) $org['email']     = $email;
    if ($phone) $org['telephone'] = $phone;
    if ($same_as) $org['sameAs']  = $same_as;

    $graph = [$org];

    // LocalBusiness yalnızca adres varsa — yerel arama kartı için anlamlı.
    if ($address) {
        $lb = [
            '@type' => 'LocalBusiness',
            '@id'   => $home . '#localbusiness',
            'name'  => $sitenm,
            'url'   => $home,
            'logo'  => $logo,
            'image' => $logo,
            'description' => $desc,
            'address' => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $address,
                'addressCountry'  => 'TR',
            ],
        ];
        if ($email) $lb['email']     = $email;
        if ($phone) $lb['telephone'] = $phone;
        if ($same_as) $lb['sameAs']  = $same_as;
        $graph[] = $lb;
    }

    $payload = [
        '@context' => 'https://schema.org',
        '@graph'   => $graph,
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
}

/* -------------------------------------------------------------------------
 * Per-page SEO meta-box — yazı/sayfa/CPT'lere title & description override.
 * Boş bırakılırsa otomatik (yukarıdaki kurallar) geçerli kalır.
 * ---------------------------------------------------------------------- */
add_action('add_meta_boxes', function () {
    $types = ['post', 'page', 'kpl_training', 'kpl_case', 'kpl_team'];
    foreach ($types as $t) {
        add_meta_box('kaplan_seo', __('SEO (Kaplan)', 'kaplan'), 'kaplan_seo_meta_box_cb', $t, 'normal', 'default');
    }
});

function kaplan_seo_meta_box_cb($post): void {
    wp_nonce_field('kaplan_seo_save', 'kaplan_seo_nonce');
    $t = (string) get_post_meta($post->ID, '_kaplan_seo_title', true);
    $d = (string) get_post_meta($post->ID, '_kaplan_seo_desc', true);
    echo '<style>.kpl-seo label{display:block;font-weight:600;margin:10px 0 3px}.kpl-seo input,.kpl-seo textarea{width:100%}.kpl-seo .c{color:#646970;font-size:12px;margin:2px 0 0}</style>';
    echo '<div class="kpl-seo">';
    echo '<label>' . esc_html__('SEO Başlık', 'kaplan') . '</label>';
    printf('<input type="text" name="kaplan_seo_title" value="%s" maxlength="80" placeholder="%s">', esc_attr($t), esc_attr__('Boş bırakırsanız WP başlığı kullanılır', 'kaplan'));
    echo '<p class="c">' . esc_html__('İdeal 50-60 karakter. SERP\'te tam görünür.', 'kaplan') . '</p>';
    echo '<label>' . esc_html__('SEO Açıklama', 'kaplan') . '</label>';
    printf('<textarea name="kaplan_seo_desc" rows="3" maxlength="200" placeholder="%s">%s</textarea>', esc_attr__('Boş bırakırsanız özet/içerikten otomatik üretilir', 'kaplan'), esc_textarea($d));
    echo '<p class="c">' . esc_html__('İdeal 120-160 karakter. Meta + Open Graph + Twitter\'a yansır.', 'kaplan') . '</p>';
    echo '</div>';
}

add_action('save_post', function ($post_id) {
    if (!isset($_POST['kaplan_seo_nonce']) || !wp_verify_nonce($_POST['kaplan_seo_nonce'], 'kaplan_seo_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['kaplan_seo_title'])) {
        update_post_meta($post_id, '_kaplan_seo_title', sanitize_text_field(wp_unslash($_POST['kaplan_seo_title'])));
    }
    if (isset($_POST['kaplan_seo_desc'])) {
        update_post_meta($post_id, '_kaplan_seo_desc', sanitize_textarea_field(wp_unslash($_POST['kaplan_seo_desc'])));
    }
});
