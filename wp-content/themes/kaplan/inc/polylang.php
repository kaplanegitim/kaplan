<?php
/**
 * Polylang integration.
 *
 * Polylang kurulu DEĞİLSE bu dosya sessizce hiçbir şey yapmaz (defensive checks).
 * Kurulduğunda CPT'ler çevrilebilir hale gelir, hardcoded theme stringler
 * Polylang String Translations'a kaydolur.
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

/**
 * Polylang'a bildir: kpl_training, kpl_case, kpl_team, kpl_slide
 * post türleri çevirilebilir olsun.
 */
add_filter('pll_get_post_types', function ($post_types, $hide = false) {
    $post_types['kpl_training']   = 'kpl_training';
    $post_types['kpl_case']       = 'kpl_case';
    $post_types['kpl_team']       = 'kpl_team';
    $post_types['kpl_slide']      = 'kpl_slide';
    // kpl_submission ÇEVRİLMEZ — form gönderimleri tek dilde kalsın.
    return $post_types;
}, 10, 2);

/**
 * Polylang'a bildir: kpl_training_cat (eğitim kategorisi) çevrilebilir olsun.
 * Böylece her kategori term'inin bir dili olur; TR eğitim Türkçe kategoriyi,
 * EN eğitim İngilizce kategoriyi gösterir. Admin "Kategoriler" kutusu da
 * düzenlenen eğitimin diline göre filtrelenir.
 */
add_filter('pll_get_taxonomies', function ($taxonomies, $hide = false) {
    $taxonomies['kpl_training_cat'] = 'kpl_training_cat';
    return $taxonomies;
}, 10, 2);

/**
 * NOT: Topbar/footer çevrilebilir metinleri (marka açıklaması, bülten,
 * adres, çalışma saatleri, copyright vb.) doğrudan template'lerde
 * esc_html_e(..., 'kaplan') ile yazılır ve languages/en_US.l10n.php
 * üzerinden çevrilir. Ayrı bir Polylang String Translations kaydına
 * gerek yoktur — telefon/e-posta/sosyal URL'ler dile bağlı değildir.
 */

/**
 * Dil-duyarlı iç link.
 *
 * TR sayfa slug'ını alır, geçerli dildeki çevirisinin permalink'ini döner.
 * Böylece EN sayfada "/egitimler/" linki /en/trainings/'e gider.
 *
 * - Polylang yoksa düz home_url('/slug/') döner.
 * - Sayfa bulunamazsa düz home_url('/slug/') döner.
 * - Geçerli dilde çeviri yoksa orijinal (TR) sayfanın permalink'ine düşer.
 *
 * @param string $tr_path TR sayfa path'i (örn: '/egitimler/' veya 'egitimler').
 * @return string Tam URL.
 */
function kpl_localized_url(string $tr_path): string {
    $slug = trim($tr_path, '/');
    $fallback = home_url('/' . ($slug !== '' ? $slug . '/' : ''));

    if ($slug === '' || !function_exists('pll_current_language') || !function_exists('pll_get_post')) {
        return $fallback;
    }

    $page = get_page_by_path($slug);
    if (!$page) {
        return $fallback;
    }

    $cur    = pll_current_language();
    $target = $cur ? pll_get_post($page->ID, $cur) : 0;

    return get_permalink($target ?: $page->ID);
}

/**
 * Polylang dil-fallback'li WP_Query.
 *
 * Aktif dilde sorgular; sonuç yoksa varsayılan dile (TR — source of truth)
 * düşer. Böylece EN sayfada henüz çevrilmemiş CPT içeriği (slider, ekip vb.)
 * boş kalmaz; EN çeviri eklenince otomatik EN gösterir, duplicate olmaz.
 *
 * Polylang yoksa düz WP_Query döner.
 *
 * @param array $args WP_Query argümanları (lang HARİÇ).
 * @return WP_Query
 */
function kpl_query_with_lang_fallback(array $args): WP_Query {
    // Polylang yoksa: filtre yok, düz query.
    if (!function_exists('pll_current_language') || !function_exists('pll_default_language')) {
        return new WP_Query($args);
    }

    $current = pll_current_language();
    $default = pll_default_language();

    // Aktif dilde dene.
    $q = new WP_Query(array_merge($args, ['lang' => $current]));

    // Aktif dil varsayılan değilse ve sonuç boşsa → varsayılan dile düş.
    if (!$q->have_posts() && $current && $current !== $default) {
        $q = new WP_Query(array_merge($args, ['lang' => $default]));
    }
    return $q;
}

/**
 * Polylang'ın .lang nav-menu item'larını custom walker'da düzgün render etmek için
 * Polylang yoksa eski "EN" link'ini, varsa Polylang switcher item'larını kullan.
 *
 * Polylang otomatik olarak Ana Menü'ye dil item'ları ekler (eğer "Languages → Settings →
 * Display language names in menu" ya da menü editöründen "Language switcher" eklenirse).
 * Bizim Walker bunları default render edecek — extra logic gerekmiyor.
 */

/**
 * Tema home_url() çağrıları Polylang varsa otomatik dil-aware olur
 * (pll_home_url() filter'ı home_url'a hook'ludur). Ekstra koda gerek yok.
 */

/**
 * Header nav için otomatik language switcher items.
 *
 * Polylang yoksa veya menüde zaten "Language switcher" item'ı varsa boş string döner.
 * Aksi halde mevcut dilin haricindeki diller için `.lang` li'leri çıkarır.
 */
function kpl_lang_switcher_items(): string {
    if (!function_exists('pll_the_languages')) return '';

    $links = pll_the_languages([
        'raw'           => 1,
        'hide_if_empty' => 1,
        'hide_current'  => 1,   // mevcut dili gizle, sadece diğer dillere bağlantı
        // display_names_as varsayılan 'name' → title/aria-label "Türkçe"/"English".
        // Bayrak seçimi $link['slug']'a göre yapılır (raw=1 ikisini de döner).
    ]);
    if (empty($links) || !is_array($links)) return '';

    // Dil slug → bayrak dosyası eşlemesi (sadece bayrak gösterilir).
    $flags = [
        'tr' => 'tr.svg',
        'en' => 'gb.svg',
    ];

    $out = '';
    foreach ($links as $link) {
        $slug  = $link['slug'];
        $code  = strtoupper($slug);
        $url   = $link['url'] ?? '#';
        $title = $link['name'] ?? $code;
        $flag  = $flags[$slug] ?? '';

        if ($flag) {
            $inner = sprintf(
                '<img class="lang__flag" src="%s" alt="%s" width="22" height="15" />',
                esc_url(KAPLAN_URI . '/assets/img/flags/' . $flag),
                esc_attr($title)
            );
        } else {
            $inner = esc_html($code);
        }

        $out .= sprintf(
            '<li class="lang"><a href="%s" title="%s" aria-label="%s">%s</a></li>',
            esc_url($url),
            esc_attr($title),
            esc_attr($title),
            $inner
        );
    }
    return $out;
}

/**
 * Polylang template loader.
 *
 * page-{slug}.php WP template hierarchy slug-bazlı çalışır. EN translation
 * sayfalarının slug'ları farklı (about-us, contact vs.) — ama biz TR slug'lı
 * template dosyalarını paylaşmak istiyoruz. Bu filter:
 *   - Sayfa bir EN (veya non-TR) translation ise
 *   - TR eşleniğinin slug'ından page-{tr_slug}.php var mı bak
 *   - Varsa onu kullan
 * Böylece /about-us/ → page-hakkimizda.php, /contact/ → page-iletisim.php vb.
 */
add_filter('template_include', function ($template) {
    if (!is_page() || !function_exists('pll_get_post')) {
        return $template;
    }

    $post_id = get_queried_object_id();
    if (!$post_id) return $template;

    $current_lang = function_exists('pll_get_post_language') ? pll_get_post_language($post_id) : '';
    if (!$current_lang || $current_lang === 'tr') {
        return $template; // TR sayfa zaten slug-bazlı template buluyor
    }

    $tr_id = pll_get_post($post_id, 'tr');
    if (!$tr_id || $tr_id === $post_id) return $template;

    $tr_post = get_post($tr_id);
    if (!$tr_post) return $template;

    $tr_template = KAPLAN_DIR . '/page-' . $tr_post->post_name . '.php';
    if (file_exists($tr_template)) {
        return $tr_template;
    }
    return $template;
}, 99);
