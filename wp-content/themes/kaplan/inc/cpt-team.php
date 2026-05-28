<?php
/**
 * CPT: Ekip Üyesi (kpl_team).
 *
 * Meta keys:
 *   _kpl_role     : pozisyon / unvan (team-card__role)
 *   _kpl_linkedin : LinkedIn profili URL
 *   _kpl_email    : iletişim e-postası
 *
 * Featured image → vesikalık fotoğraf (348x348 önerilir).
 * menu_order → sıralama.
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_post_type('kpl_team', [
        'labels' => [
            'name'          => __('Ekip', 'kaplan'),
            'singular_name' => __('Ekip Üyesi', 'kaplan'),
            'menu_name'     => __('Ekip', 'kaplan'),
            'add_new'       => __('Yeni Ekle', 'kaplan'),
            'add_new_item'  => __('Yeni Ekip Üyesi Ekle', 'kaplan'),
            'edit_item'     => __('Ekip Üyesini Düzenle', 'kaplan'),
            'all_items'     => __('Tüm Ekip', 'kaplan'),
            'search_items'  => __('Ekip Ara', 'kaplan'),
            'not_found'     => __('Ekip üyesi bulunamadı.', 'kaplan'),
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'menu_position' => 22,
        'menu_icon'     => 'dashicons-groups',
        'supports'      => ['title', 'editor', 'thumbnail', 'page-attributes'],
        'has_archive'   => false,
        'rewrite'       => ['slug' => 'ekip', 'with_front' => false],
        'hierarchical'  => false,
    ]);
});

add_action('add_meta_boxes', function () {
    add_meta_box('kpl_team_fields', __('Ekip Üyesi Detayları', 'kaplan'), 'kpl_team_meta_box_cb', 'kpl_team', 'side', 'high');
});

function kpl_team_meta_box_cb($post) {
    wp_nonce_field('kpl_team_save', 'kpl_team_nonce');
    $role     = get_post_meta($post->ID, '_kpl_role', true);
    $linkedin = get_post_meta($post->ID, '_kpl_linkedin', true);
    $email    = get_post_meta($post->ID, '_kpl_email', true);
    ?>
    <p>
        <label><strong><?php esc_html_e('Pozisyon / Unvan', 'kaplan'); ?></strong></label>
        <input type="text" name="kpl_role" value="<?php echo esc_attr($role); ?>" style="width:100%" placeholder="Kurucu · Endüstri Mühendisi" />
    </p>
    <p>
        <label><strong>LinkedIn URL</strong></label>
        <input type="url" name="kpl_linkedin" value="<?php echo esc_attr($linkedin); ?>" style="width:100%" placeholder="https://www.linkedin.com/in/…" />
    </p>
    <p>
        <label><strong>E-posta</strong></label>
        <input type="email" name="kpl_email" value="<?php echo esc_attr($email); ?>" style="width:100%" placeholder="ad@kaplanegitim.com" />
    </p>
    <?php
}

add_action('save_post_kpl_team', function ($post_id) {
    if (!isset($_POST['kpl_team_nonce']) || !wp_verify_nonce($_POST['kpl_team_nonce'], 'kpl_team_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach (['kpl_role' => '_kpl_role', 'kpl_linkedin' => '_kpl_linkedin', 'kpl_email' => '_kpl_email'] as $input => $meta) {
        if (isset($_POST[$input])) {
            update_post_meta($post_id, $meta, sanitize_text_field(wp_unslash($_POST[$input])));
        }
    }
});

add_filter('manage_kpl_team_posts_columns', function ($cols) {
    $new = [];
    foreach ($cols as $k => $v) {
        $new[$k] = $v;
        if ($k === 'title') $new['kpl_role'] = __('Pozisyon', 'kaplan');
    }
    return $new;
});
add_action('manage_kpl_team_posts_custom_column', function ($col, $post_id) {
    if ($col === 'kpl_role') echo esc_html(get_post_meta($post_id, '_kpl_role', true));
}, 10, 2);
