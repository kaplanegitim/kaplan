<?php
/**
 * CPT: Müşteri/Marka logosu (kpl_client).
 *
 * Anasayfa "Birlikte çalıştığımız markalar" marquee'sini besler.
 * Logolar dile bağlı DEĞİL — Polylang'da çevrilebilir yapılmaz
 * (tek kayıt her dilde görünür).
 *
 * Featured image → logo (PNG, şeffaf zemin önerilir).
 * _kpl_client_url → opsiyonel dış bağlantı.
 * menu_order → sıralama.
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_post_type('kpl_client', [
        'labels' => [
            'name'          => __('Müşteriler', 'kaplan'),
            'singular_name' => __('Müşteri', 'kaplan'),
            'menu_name'     => __('Müşteriler', 'kaplan'),
            'add_new'       => __('Yeni Ekle', 'kaplan'),
            'add_new_item'  => __('Yeni Müşteri Ekle', 'kaplan'),
            'edit_item'     => __('Müşteriyi Düzenle', 'kaplan'),
            'all_items'     => __('Tüm Müşteriler', 'kaplan'),
            'search_items'  => __('Müşteri Ara', 'kaplan'),
            'not_found'     => __('Müşteri bulunamadı.', 'kaplan'),
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'menu_position' => 24,
        'menu_icon'     => 'dashicons-awards',
        'supports'      => ['title', 'thumbnail', 'page-attributes'],
        'has_archive'   => false,
        'rewrite'       => false,
        'hierarchical'  => false,
    ]);
});

add_action('add_meta_boxes', function () {
    add_meta_box('kpl_client_fields', __('Müşteri Detayları', 'kaplan'), 'kpl_client_meta_box_cb', 'kpl_client', 'side', 'high');
});

function kpl_client_meta_box_cb($post) {
    wp_nonce_field('kpl_client_save', 'kpl_client_nonce');
    $url = get_post_meta($post->ID, '_kpl_client_url', true);
    ?>
    <p><?php esc_html_e('Logoyu sağdaki "Öne Çıkan Görsel" bölümünden ekleyin (şeffaf PNG önerilir).', 'kaplan'); ?></p>
    <p>
        <label><strong><?php esc_html_e('Web Sitesi (opsiyonel)', 'kaplan'); ?></strong></label>
        <input type="url" name="kpl_client_url" value="<?php echo esc_attr($url); ?>" style="width:100%" placeholder="https://…" />
    </p>
    <?php
}

add_action('save_post_kpl_client', function ($post_id) {
    if (!isset($_POST['kpl_client_nonce']) || !wp_verify_nonce($_POST['kpl_client_nonce'], 'kpl_client_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['kpl_client_url'])) {
        update_post_meta($post_id, '_kpl_client_url', esc_url_raw(wp_unslash($_POST['kpl_client_url'])));
    }
});
