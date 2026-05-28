<?php
/**
 * CPT: Eğitim Programı (kpl_training).
 *
 * Meta keys:
 *   _kpl_chip      : kategori chip metni (ör: "Excel", "Veri Yönetim")
 *   _kpl_icon      : FontAwesome class fragmentı (ör: "fa-chart-column")
 *   _kpl_duration  : süre metni (ör: "2 gün")
 *   _kpl_package   : "1" → paket eğitim, boş → tekil
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_post_type('kpl_training', [
        'labels' => [
            'name'               => __('Eğitimler', 'kaplan'),
            'singular_name'      => __('Eğitim', 'kaplan'),
            'menu_name'          => __('Eğitimler', 'kaplan'),
            'add_new'            => __('Yeni Ekle', 'kaplan'),
            'add_new_item'       => __('Yeni Eğitim Ekle', 'kaplan'),
            'edit_item'          => __('Eğitimi Düzenle', 'kaplan'),
            'all_items'          => __('Tüm Eğitimler', 'kaplan'),
            'search_items'       => __('Eğitim Ara', 'kaplan'),
            'not_found'          => __('Eğitim bulunamadı.', 'kaplan'),
        ],
        'public'             => true,
        'show_in_rest'       => true,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-welcome-learn-more',
        'supports'           => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
        'has_archive'        => false,
        'rewrite'            => ['slug' => 'egitim', 'with_front' => false],
        'hierarchical'       => false,
    ]);
});

// Meta box
add_action('add_meta_boxes', function () {
    add_meta_box(
        'kpl_training_fields',
        __('Eğitim Detayları', 'kaplan'),
        'kpl_training_meta_box_cb',
        'kpl_training',
        'side',
        'high'
    );
});

function kpl_training_meta_box_cb($post) {
    wp_nonce_field('kpl_training_save', 'kpl_training_nonce');
    $chip     = get_post_meta($post->ID, '_kpl_chip', true);
    $icon     = get_post_meta($post->ID, '_kpl_icon', true);
    $duration = get_post_meta($post->ID, '_kpl_duration', true);
    $package  = get_post_meta($post->ID, '_kpl_package', true);
    ?>
    <p>
        <label><strong><?php esc_html_e('Kategori (chip)', 'kaplan'); ?></strong></label>
        <input type="text" name="kpl_chip" value="<?php echo esc_attr($chip); ?>" style="width:100%" placeholder="Veri Yönetim, Excel, …" />
    </p>
    <p>
        <label><strong><?php esc_html_e('İkon (Font Awesome)', 'kaplan'); ?></strong></label>
        <input type="text" name="kpl_icon" value="<?php echo esc_attr($icon); ?>" style="width:100%" placeholder="fa-chart-column" />
        <small><?php esc_html_e('Sadece icon adı (örn: fa-chart-column).', 'kaplan'); ?></small>
    </p>
    <p>
        <label><strong><?php esc_html_e('Süre', 'kaplan'); ?></strong></label>
        <input type="text" name="kpl_duration" value="<?php echo esc_attr($duration); ?>" style="width:100%" placeholder="2 gün" />
    </p>
    <p>
        <label>
            <input type="checkbox" name="kpl_package" value="1" <?php checked($package, '1'); ?> />
            <?php esc_html_e('Bu bir paket eğitim programıdır', 'kaplan'); ?>
        </label>
    </p>
    <?php
}

add_action('save_post_kpl_training', function ($post_id) {
    if (!isset($_POST['kpl_training_nonce']) || !wp_verify_nonce($_POST['kpl_training_nonce'], 'kpl_training_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['kpl_chip' => '_kpl_chip', 'kpl_icon' => '_kpl_icon', 'kpl_duration' => '_kpl_duration'];
    foreach ($fields as $input => $meta) {
        if (isset($_POST[$input])) {
            update_post_meta($post_id, $meta, sanitize_text_field(wp_unslash($_POST[$input])));
        }
    }
    update_post_meta($post_id, '_kpl_package', isset($_POST['kpl_package']) ? '1' : '');
});

// Admin list table — chip/duration sütunları
add_filter('manage_kpl_training_posts_columns', function ($cols) {
    $new = [];
    foreach ($cols as $k => $v) {
        $new[$k] = $v;
        if ($k === 'title') {
            $new['kpl_chip']     = __('Kategori', 'kaplan');
            $new['kpl_duration'] = __('Süre', 'kaplan');
            $new['kpl_package']  = __('Paket', 'kaplan');
        }
    }
    return $new;
});
add_action('manage_kpl_training_posts_custom_column', function ($col, $post_id) {
    if ($col === 'kpl_chip')     echo esc_html(get_post_meta($post_id, '_kpl_chip', true));
    if ($col === 'kpl_duration') echo esc_html(get_post_meta($post_id, '_kpl_duration', true));
    if ($col === 'kpl_package')  echo get_post_meta($post_id, '_kpl_package', true) === '1' ? '★' : '—';
}, 10, 2);
