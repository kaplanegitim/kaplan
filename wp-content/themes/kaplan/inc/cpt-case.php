<?php
/**
 * CPT: İş Zekası Projesi / Case Study (kpl_case).
 *
 * Meta keys:
 *   _kpl_client : müşteri / marka adı (case-card__client)
 *   _kpl_tags   : virgül ayrımlı etiketler (case-card__tags)
 *
 * Featured image → arka plan görseli (case-card__media).
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_post_type('kpl_case', [
        'labels' => [
            'name'          => __('Projeler', 'kaplan'),
            'singular_name' => __('Proje', 'kaplan'),
            'menu_name'     => __('Projeler', 'kaplan'),
            'add_new'       => __('Yeni Ekle', 'kaplan'),
            'add_new_item'  => __('Yeni Proje Ekle', 'kaplan'),
            'edit_item'     => __('Projeyi Düzenle', 'kaplan'),
            'all_items'     => __('Tüm Projeler', 'kaplan'),
            'search_items'  => __('Proje Ara', 'kaplan'),
            'not_found'     => __('Proje bulunamadı.', 'kaplan'),
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'menu_position' => 21,
        'menu_icon'     => 'dashicons-portfolio',
        'supports'      => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
        'has_archive'   => false,
        'rewrite'       => ['slug' => 'proje', 'with_front' => false],
        'hierarchical'  => false,
    ]);
});

add_action('add_meta_boxes', function () {
    add_meta_box('kpl_case_fields', __('Proje Detayları', 'kaplan'), 'kpl_case_meta_box_cb', 'kpl_case', 'side', 'high');
});

function kpl_case_meta_box_cb($post) {
    wp_nonce_field('kpl_case_save', 'kpl_case_nonce');
    $client = get_post_meta($post->ID, '_kpl_client', true);
    $tags   = get_post_meta($post->ID, '_kpl_tags', true);
    ?>
    <p>
        <label><strong><?php esc_html_e('Müşteri / Marka', 'kaplan'); ?></strong></label>
        <input type="text" name="kpl_client" value="<?php echo esc_attr($client); ?>" style="width:100%" placeholder="Vodafone, Aras Kargo, …" />
    </p>
    <p>
        <label><strong><?php esc_html_e('Etiketler', 'kaplan'); ?></strong></label>
        <input type="text" name="kpl_tags" value="<?php echo esc_attr($tags); ?>" style="width:100%" placeholder="Otomatik Analiz, Otomatik Mail, …" />
        <small><?php esc_html_e('Virgül ile ayırın.', 'kaplan'); ?></small>
    </p>
    <?php
}

add_action('save_post_kpl_case', function ($post_id) {
    if (!isset($_POST['kpl_case_nonce']) || !wp_verify_nonce($_POST['kpl_case_nonce'], 'kpl_case_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach (['kpl_client' => '_kpl_client', 'kpl_tags' => '_kpl_tags'] as $input => $meta) {
        if (isset($_POST[$input])) {
            update_post_meta($post_id, $meta, sanitize_text_field(wp_unslash($_POST[$input])));
        }
    }
});

add_filter('manage_kpl_case_posts_columns', function ($cols) {
    $new = [];
    foreach ($cols as $k => $v) {
        $new[$k] = $v;
        if ($k === 'title') $new['kpl_client'] = __('Müşteri', 'kaplan');
    }
    return $new;
});
add_action('manage_kpl_case_posts_custom_column', function ($col, $post_id) {
    if ($col === 'kpl_client') echo esc_html(get_post_meta($post_id, '_kpl_client', true));
}, 10, 2);
