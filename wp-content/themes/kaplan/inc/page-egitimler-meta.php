<?php
/**
 * Eğitimler sayfası — "Bireysel Sekmesi Görseli" meta box.
 *
 * Slug'ı `egitimler` olan sayfa(lar)a (TR + Polylang EN) bir görsel seçici ekler.
 * Bireysel sekmesindeki placeholder yerine bu görsel render edilir; boşsa ikon
 * fallback kalır. Sadece bu sayfayı etkiler — Paket/Tekil sekmeleri dokunulmaz.
 *
 * Meta key: _kpl_bireysel_image (attachment ID)
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

/** Bu post, Eğitimler sayfası mı? (slug-bazlı template page-egitimler.php) */
function kpl_is_egitimler_page($post): bool {
    return $post instanceof WP_Post
        && $post->post_type === 'page'
        && $post->post_name === 'egitimler';
}

// Meta box — yalnız Eğitimler sayfasının düzenleme ekranında.
add_action('add_meta_boxes_page', function ($post) {
    if (!kpl_is_egitimler_page($post)) return;
    add_meta_box(
        'kpl_bireysel_image',
        __('Bireysel Sekmesi Görseli', 'kaplan'),
        'kpl_bireysel_image_box_cb',
        'page',
        'side',
        'default'
    );
});

// Medya kütüphanesi JS'i — yalnız bu sayfanın edit ekranında.
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'post.php') return;
    $post = get_post();
    if (!kpl_is_egitimler_page($post)) return;
    wp_enqueue_media();
});

function kpl_bireysel_image_box_cb($post) {
    wp_nonce_field('kpl_bireysel_image', 'kpl_bireysel_image_nonce');
    $id  = (int) get_post_meta($post->ID, '_kpl_bireysel_image', true);
    $url = $id ? wp_get_attachment_image_url($id, 'medium') : '';
    ?>
    <input type="hidden" name="kpl_bireysel_image" id="kpl_bireysel_image" value="<?php echo esc_attr($id); ?>" />
    <div id="kpl_bir_img_preview" style="margin-bottom:10px;">
        <?php if ($url) : ?><img src="<?php echo esc_url($url); ?>" style="max-width:100%;height:auto;border-radius:8px;display:block;" /><?php endif; ?>
    </div>
    <button type="button" class="button" id="kpl_bir_img_select"><?php esc_html_e('Görsel Seç', 'kaplan'); ?></button>
    <button type="button" class="button" id="kpl_bir_img_remove" style="<?php echo $id ? '' : 'display:none;'; ?>"><?php esc_html_e('Kaldır', 'kaplan'); ?></button>
    <p class="description"><?php esc_html_e('Bireysel sekmesindeki görsel. Boşsa varsayılan ikon gösterilir. Önerilen oran 4:5 (örn. 800×1000).', 'kaplan'); ?></p>
    <script>
    (function ($) {
        var frame;
        $('#kpl_bir_img_select').on('click', function (e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: <?php echo wp_json_encode(__('Görsel Seç', 'kaplan')); ?>,
                button: { text: <?php echo wp_json_encode(__('Kullan', 'kaplan')); ?> },
                library: { type: 'image' },
                multiple: false
            });
            frame.on('select', function () {
                var a = frame.state().get('selection').first().toJSON();
                var u = (a.sizes && a.sizes.medium) ? a.sizes.medium.url : a.url;
                $('#kpl_bireysel_image').val(a.id);
                $('#kpl_bir_img_preview').html('<img src="' + u + '" style="max-width:100%;height:auto;border-radius:8px;display:block;" />');
                $('#kpl_bir_img_remove').show();
            });
            frame.open();
        });
        $('#kpl_bir_img_remove').on('click', function (e) {
            e.preventDefault();
            $('#kpl_bireysel_image').val('');
            $('#kpl_bir_img_preview').empty();
            $(this).hide();
        });
    })(jQuery);
    </script>
    <?php
}

// Kaydet.
add_action('save_post_page', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['kpl_bireysel_image_nonce']) || !wp_verify_nonce($_POST['kpl_bireysel_image_nonce'], 'kpl_bireysel_image')) return;
    if (!current_user_can('edit_page', $post_id)) return;

    $val = isset($_POST['kpl_bireysel_image']) ? absint($_POST['kpl_bireysel_image']) : 0;
    if ($val) {
        update_post_meta($post_id, '_kpl_bireysel_image', $val);
    } else {
        delete_post_meta($post_id, '_kpl_bireysel_image');
    }
});
