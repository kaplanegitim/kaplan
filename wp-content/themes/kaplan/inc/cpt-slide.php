<?php
/**
 * CPT: Hero Slider Slide (kpl_slide).
 *
 * Anasayfa hero slider'ı bu CPT'den beslenir.
 *
 * Slide alanları:
 *   post_title       : ana başlık (örn: "Robotik Süreç Otomasyonu")
 *   _kpl_title_accent: vurgu metni (cyan renkte yazılır — örn: "(RPA)")
 *   post_excerpt     : alt başlık paragrafı
 *   _kpl_eyebrow     : üst etiket (örn: "Yeni eklendi")
 *   _kpl_cta1_label / _kpl_cta1_url
 *   _kpl_cta2_label / _kpl_cta2_url
 *   featured image   : arka plan görseli
 *   menu_order       : sıralama
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_post_type('kpl_slide', [
        'labels' => [
            'name'          => __('Slide\'lar', 'kaplan'),
            'singular_name' => __('Slide', 'kaplan'),
            'menu_name'     => __('Hero Slider', 'kaplan'),
            'add_new'       => __('Yeni Slide', 'kaplan'),
            'add_new_item'  => __('Yeni Slide Ekle', 'kaplan'),
            'edit_item'     => __('Slide Düzenle', 'kaplan'),
            'all_items'     => __('Tüm Slide\'lar', 'kaplan'),
            'not_found'     => __('Slide bulunamadı.', 'kaplan'),
        ],
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'menu_position'      => 23,
        'menu_icon'          => 'dashicons-images-alt2',
        'supports'           => ['title', 'excerpt', 'thumbnail', 'page-attributes'],
        'has_archive'        => false,
        'rewrite'            => false,
        'hierarchical'       => false,
    ]);
});

add_action('add_meta_boxes', function () {
    add_meta_box('kpl_slide_fields', __('Slide Detayları', 'kaplan'), 'kpl_slide_meta_box_cb', 'kpl_slide', 'normal', 'high');
});

function kpl_slide_meta_box_cb($post) {
    wp_nonce_field('kpl_slide_save', 'kpl_slide_nonce');
    $eyebrow      = get_post_meta($post->ID, '_kpl_eyebrow', true);
    $accent       = get_post_meta($post->ID, '_kpl_title_accent', true);
    $cta1_label   = get_post_meta($post->ID, '_kpl_cta1_label', true);
    $cta1_url     = get_post_meta($post->ID, '_kpl_cta1_url', true);
    $cta2_label   = get_post_meta($post->ID, '_kpl_cta2_label', true);
    $cta2_url     = get_post_meta($post->ID, '_kpl_cta2_url', true);
    ?>
    <style>.kpl-slide-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}.kpl-slide-grid .full{grid-column:1/-1}.kpl-slide-grid label{display:block;font-weight:600;margin-bottom:.25rem}.kpl-slide-grid input{width:100%}</style>
    <div class="kpl-slide-grid">
        <div class="full">
            <label><?php esc_html_e('Üst etiket (eyebrow)', 'kaplan'); ?></label>
            <input type="text" name="kpl_eyebrow" value="<?php echo esc_attr($eyebrow); ?>" placeholder="<?php esc_attr_e('Yeni eklendi, Bireysel Eğitimler, İş Zekası...', 'kaplan'); ?>" />
        </div>
        <div class="full">
            <label><?php esc_html_e('Başlık vurgu metni (cyan span)', 'kaplan'); ?></label>
            <input type="text" name="kpl_title_accent" value="<?php echo esc_attr($accent); ?>" placeholder="<?php esc_attr_e('(RPA), Yaşam Kiti, Yönetim...', 'kaplan'); ?>" />
            <p class="description"><?php esc_html_e('Ana başlığa eklenen, cyan renkte vurgulanan kelime/öbek.', 'kaplan'); ?></p>
        </div>
        <div>
            <label><?php esc_html_e('CTA 1 metin', 'kaplan'); ?></label>
            <input type="text" name="kpl_cta1_label" value="<?php echo esc_attr($cta1_label); ?>" placeholder="<?php esc_attr_e('Eğitimler', 'kaplan'); ?>" />
        </div>
        <div>
            <label><?php esc_html_e('CTA 1 URL', 'kaplan'); ?></label>
            <input type="text" name="kpl_cta1_url" value="<?php echo esc_attr($cta1_url); ?>" placeholder="/egitimler/" />
        </div>
        <div>
            <label><?php esc_html_e('CTA 2 metin (opsiyonel)', 'kaplan'); ?></label>
            <input type="text" name="kpl_cta2_label" value="<?php echo esc_attr($cta2_label); ?>" placeholder="<?php esc_attr_e('Hizmetlerimiz', 'kaplan'); ?>" />
        </div>
        <div>
            <label><?php esc_html_e('CTA 2 URL', 'kaplan'); ?></label>
            <input type="text" name="kpl_cta2_url" value="<?php echo esc_attr($cta2_url); ?>" placeholder="/danismanlik-ve-projelerimiz/" />
        </div>
    </div>
    <p style="margin-top:1rem; padding:.75rem; background:#EAF7FE; border-left:3px solid #5AC8FB; border-radius:6px; font-size:.9rem;">
        <strong><?php esc_html_e('Görsel', 'kaplan'); ?>:</strong> <?php esc_html_e('Sağdaki "Öne Çıkan Görsel" bölümünden slide arkaplan fotoğrafını seçin (1920×900 önerilir).', 'kaplan'); ?><br>
        <strong><?php esc_html_e('Sıralama', 'kaplan'); ?>:</strong> <?php esc_html_e('"Sayfa Öznitelikleri" → "Sıra" alanından küçük → büyük sıralanır.', 'kaplan'); ?><br>
        <strong><?php esc_html_e('Alt başlık', 'kaplan'); ?>:</strong> <?php esc_html_e('Editörün üstündeki "Özet" alanı slide alt metni olarak kullanılır.', 'kaplan'); ?>
    </p>
    <?php
}

add_action('save_post_kpl_slide', function ($post_id) {
    if (!isset($_POST['kpl_slide_nonce']) || !wp_verify_nonce($_POST['kpl_slide_nonce'], 'kpl_slide_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = [
        'kpl_eyebrow'      => '_kpl_eyebrow',
        'kpl_title_accent' => '_kpl_title_accent',
        'kpl_cta1_label'   => '_kpl_cta1_label',
        'kpl_cta1_url'     => '_kpl_cta1_url',
        'kpl_cta2_label'   => '_kpl_cta2_label',
        'kpl_cta2_url'     => '_kpl_cta2_url',
    ];
    foreach ($fields as $input => $meta) {
        if (isset($_POST[$input])) {
            update_post_meta($post_id, $meta, sanitize_text_field(wp_unslash($_POST[$input])));
        }
    }
});

add_action('admin_head', function () {
    if (get_current_screen() && get_current_screen()->post_type === 'kpl_slide') {
        // Show excerpt by default on the slide editor
        add_filter('default_hidden_meta_boxes', function ($hidden) {
            return array_diff($hidden, ['postexcerpt']);
        });
    }
});

add_filter('manage_kpl_slide_posts_columns', function ($cols) {
    $new = [];
    foreach ($cols as $k => $v) {
        if ($k === 'date') {
            $new['kpl_eyebrow'] = __('Etiket', 'kaplan');
            $new['menu_order']  = __('Sıra', 'kaplan');
        }
        $new[$k] = $v;
    }
    return $new;
});
add_action('manage_kpl_slide_posts_custom_column', function ($col, $post_id) {
    if ($col === 'kpl_eyebrow') echo esc_html(get_post_meta($post_id, '_kpl_eyebrow', true));
    if ($col === 'menu_order')  echo (int) get_post_field('menu_order', $post_id);
}, 10, 2);
