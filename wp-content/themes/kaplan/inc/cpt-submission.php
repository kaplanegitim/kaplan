<?php
/**
 * CPT: Form Gönderimleri (kpl_submission).
 *
 * Admin-only — sitede listelenmez. Gelen tüm form gönderimleri burada saklanır.
 *
 * Meta keys:
 *   _kpl_form_type  : 'training' | 'contact'
 *   _kpl_data       : JSON-encoded form data (ad/email/telefon/mesaj vb.)
 *   _kpl_ip         : istemci IP (basit fraud audit)
 *   _kpl_user_agent : tarayıcı user agent
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_post_type('kpl_submission', [
        'labels' => [
            'name'          => __('Form Gönderimleri', 'kaplan'),
            'singular_name' => __('Gönderim', 'kaplan'),
            'menu_name'     => __('Form Gönderimleri', 'kaplan'),
            'all_items'     => __('Tüm Gönderimler', 'kaplan'),
            'edit_item'     => __('Gönderim Detayı', 'kaplan'),
            'not_found'     => __('Henüz gönderim yok.', 'kaplan'),
        ],
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_admin_bar'  => false,
        'show_in_rest'       => false,
        'menu_position'      => 25,
        'menu_icon'          => 'dashicons-email-alt',
        'supports'           => ['title'], // sadece title — submission detayı meta box'ta render edilir
        'has_archive'        => false,
        'rewrite'            => false,
        'capabilities' => [
            'create_posts' => 'do_not_allow', // admin'den yeni eklenemez, sadece form ile
        ],
        'map_meta_cap' => true,
    ]);
});

// Read-only display: gönderim detayını meta box olarak göster (editör yerine)
add_action('add_meta_boxes', function () {
    add_meta_box('kpl_submission_detail', __('Gönderim Detayı', 'kaplan'), 'kpl_submission_detail_cb', 'kpl_submission', 'normal', 'high');
});

function kpl_submission_detail_cb($post) {
    $type = get_post_meta($post->ID, '_kpl_form_type', true);
    $data = get_post_meta($post->ID, '_kpl_data', true);
    $ip   = get_post_meta($post->ID, '_kpl_ip', true);
    $ua   = get_post_meta($post->ID, '_kpl_user_agent', true);

    $parsed = $data ? json_decode($data, true) : [];
    if (!is_array($parsed)) $parsed = [];

    $type_label = $type === 'training' ? __('Eğitim Talep Formu', 'kaplan') : __('İletişim Formu', 'kaplan');
    ?>
    <table class="widefat" style="border:0;background:transparent;">
        <tr>
            <th style="width:200px;text-align:left;padding:.6em 0;border-bottom:1px solid #eee;"><?php esc_html_e('Form Türü', 'kaplan'); ?></th>
            <td style="padding:.6em 0;border-bottom:1px solid #eee;"><strong style="color:#129BD8;"><?php echo esc_html($type_label); ?></strong></td>
        </tr>
        <tr>
            <th style="text-align:left;padding:.6em 0;border-bottom:1px solid #eee;"><?php esc_html_e('Tarih', 'kaplan'); ?></th>
            <td style="padding:.6em 0;border-bottom:1px solid #eee;"><?php echo esc_html(get_the_date('d.m.Y H:i', $post)); ?></td>
        </tr>
        <?php foreach ($parsed as $key => $value) :
            if (in_array($key, ['nonce', 'action', '_kpl_website'], true)) continue;
            $label = ucfirst(str_replace(['_','-'], ' ', $key));
        ?>
        <tr>
            <th style="text-align:left;vertical-align:top;padding:.6em 0;border-bottom:1px solid #eee;"><?php echo esc_html($label); ?></th>
            <td style="padding:.6em 0;border-bottom:1px solid #eee;white-space:pre-wrap;word-break:break-word;"><?php echo esc_html(is_array($value) ? implode(', ', $value) : (string)$value); ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <th style="text-align:left;padding:.6em 0;border-bottom:1px solid #eee;font-size:.85em;color:#888;">IP</th>
            <td style="padding:.6em 0;border-bottom:1px solid #eee;font-size:.85em;color:#888;"><?php echo esc_html($ip); ?></td>
        </tr>
        <tr>
            <th style="text-align:left;padding:.6em 0;font-size:.85em;color:#888;">User Agent</th>
            <td style="padding:.6em 0;font-size:.85em;color:#888;word-break:break-all;"><?php echo esc_html($ua); ?></td>
        </tr>
    </table>
    <?php
}

// Admin list table: tip ve özet sütunları
add_filter('manage_kpl_submission_posts_columns', function ($cols) {
    return [
        'cb'             => $cols['cb'],
        'title'          => __('Gönderen', 'kaplan'),
        'kpl_form_type'  => __('Form', 'kaplan'),
        'kpl_summary'    => __('Özet', 'kaplan'),
        'date'           => __('Tarih', 'kaplan'),
    ];
});
add_action('manage_kpl_submission_posts_custom_column', function ($col, $post_id) {
    if ($col === 'kpl_form_type') {
        $t = get_post_meta($post_id, '_kpl_form_type', true);
        $color = $t === 'training' ? '#129BD8' : '#6B7A90';
        echo '<span style="background:' . $color . ';color:#fff;font-size:.75em;padding:.2em .7em;border-radius:999px;font-weight:600;">';
        echo esc_html($t === 'training' ? 'Eğitim Talep' : 'İletişim');
        echo '</span>';
    }
    if ($col === 'kpl_summary') {
        $data = get_post_meta($post_id, '_kpl_data', true);
        $parsed = $data ? json_decode($data, true) : [];
        $email   = $parsed['email'] ?? '';
        $phone   = $parsed['phone'] ?? '';
        $message = $parsed['message'] ?? ($parsed['training'] ?? '');
        if ($email) echo '<span style="color:#129BD8;">' . esc_html($email) . '</span><br>';
        if ($phone) echo '<small style="color:#888;">' . esc_html($phone) . '</small><br>';
        if ($message) echo '<small>' . esc_html(wp_trim_words($message, 10)) . '</small>';
    }
}, 10, 2);

// Yeni gönderim varsa admin bar'a sayı yaz
add_action('admin_bar_menu', function ($bar) {
    if (!current_user_can('edit_posts')) return;
    $unread = wp_count_posts('kpl_submission');
    $count  = isset($unread->publish) ? (int)$unread->publish : 0;
    if (!$count) return;
    $bar->add_node([
        'id'    => 'kpl-submissions',
        'title' => '<span class="dashicons dashicons-email-alt" style="font-family:dashicons;font-size:18px;line-height:32px;"></span> ' . $count,
        'href'  => admin_url('edit.php?post_type=kpl_submission'),
        'meta'  => ['title' => __('Form Gönderimleri', 'kaplan')],
    ]);
}, 80);
