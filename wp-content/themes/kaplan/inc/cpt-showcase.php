<?php
/**
 * CPT: İnfografik & Sunum vitrini (kpl_showcase).
 *
 * page-infografik-ve-sunum.php galerilerini besler.
 *
 * Meta keys:
 *   _kpl_showcase_type : 'infografik' | 'sunum' (hangi galeride görüneceği)
 *
 * Başlık  → galeri etiketi (gallery-item__label)
 * Öne Çıkan Görsel → galeri görseli (büyük = arka plan, tam = lightbox)
 * Sıra (Page Attributes) → galeri sıralaması (menu_order ASC)
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_post_type('kpl_showcase', [
        'labels' => [
            'name'          => __('İnfografik & Sunum', 'kaplan'),
            'singular_name' => __('Vitrin Öğesi', 'kaplan'),
            'menu_name'     => __('İnfografik & Sunum', 'kaplan'),
            'add_new'       => __('Yeni Ekle', 'kaplan'),
            'add_new_item'  => __('Yeni Öğe Ekle', 'kaplan'),
            'edit_item'     => __('Öğeyi Düzenle', 'kaplan'),
            'all_items'     => __('Tüm Öğeler', 'kaplan'),
            'search_items'  => __('Öğe Ara', 'kaplan'),
            'not_found'     => __('Öğe bulunamadı.', 'kaplan'),
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'menu_position' => 22,
        'menu_icon'     => 'dashicons-images-alt2',
        'supports'      => ['title', 'thumbnail', 'page-attributes'],
        'has_archive'   => false,
        'rewrite'       => false,
        'hierarchical'  => false,
    ]);
});

/**
 * Geçerli vitrin tipleri (slug => görünen ad).
 */
function kpl_showcase_types(): array {
    return [
        'infografik' => __('İnfografik', 'kaplan'),
        'sunum'      => __('Sunum', 'kaplan'),
    ];
}

add_action('add_meta_boxes', function () {
    add_meta_box('kpl_showcase_fields', __('Vitrin Ayarı', 'kaplan'), 'kpl_showcase_meta_box_cb', 'kpl_showcase', 'side', 'high');
});

function kpl_showcase_meta_box_cb($post) {
    wp_nonce_field('kpl_showcase_save', 'kpl_showcase_nonce');
    $type = get_post_meta($post->ID, '_kpl_showcase_type', true) ?: 'infografik';
    ?>
    <p>
        <label><strong><?php esc_html_e('Galeri', 'kaplan'); ?></strong></label>
        <select name="kpl_showcase_type" style="width:100%">
            <?php foreach (kpl_showcase_types() as $slug => $label) : ?>
                <option value="<?php echo esc_attr($slug); ?>" <?php selected($type, $slug); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p><small><?php esc_html_e('Görsel = Öne Çıkan Görsel. Etiket = Başlık. Sıra = Sayfa Nitelikleri → Sıra.', 'kaplan'); ?></small></p>
    <?php
}

add_action('save_post_kpl_showcase', function ($post_id) {
    if (!isset($_POST['kpl_showcase_nonce']) || !wp_verify_nonce($_POST['kpl_showcase_nonce'], 'kpl_showcase_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['kpl_showcase_type'])) {
        $type = sanitize_key(wp_unslash($_POST['kpl_showcase_type']));
        if (!array_key_exists($type, kpl_showcase_types())) $type = 'infografik';
        update_post_meta($post_id, '_kpl_showcase_type', $type);
    }
});

add_filter('manage_kpl_showcase_posts_columns', function ($cols) {
    $new = [];
    foreach ($cols as $k => $v) {
        if ($k === 'date') {
            $new['kpl_showcase_type'] = __('Galeri', 'kaplan');
        }
        $new[$k] = $v;
    }
    return $new;
});
add_action('manage_kpl_showcase_posts_custom_column', function ($col, $post_id) {
    if ($col === 'kpl_showcase_type') {
        $types = kpl_showcase_types();
        $type  = get_post_meta($post_id, '_kpl_showcase_type', true);
        echo esc_html($types[$type] ?? '—');
    }
}, 10, 2);

/**
 * Bir vitrin tipinin galeri öğelerini döndürür.
 * page-infografik-ve-sunum.php buradan beslenir.
 *
 * @param string $type 'infografik' | 'sunum'
 * @return array<int, array{src:string, href:string, label:string}> Öğe yoksa boş dizi.
 */
function kpl_showcase_items(string $type): array {
    $q = function_exists('kpl_query_with_lang_fallback')
        ? kpl_query_with_lang_fallback([
            'post_type'      => 'kpl_showcase',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'meta_query'     => [['key' => '_kpl_showcase_type', 'value' => $type]],
        ])
        : new WP_Query([
            'post_type'      => 'kpl_showcase',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'meta_query'     => [['key' => '_kpl_showcase_type', 'value' => $type]],
        ]);

    $items = [];
    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            $tid  = get_post_thumbnail_id();
            $src  = $tid ? wp_get_attachment_image_url($tid, 'large') : '';
            $href = $tid ? wp_get_attachment_image_url($tid, 'full')  : '';
            $items[] = [
                'src'   => $src ?: '',
                'href'  => $href ?: $src ?: '',
                'label' => get_the_title(),
            ];
        }
        wp_reset_postdata();
    }
    return $items;
}
