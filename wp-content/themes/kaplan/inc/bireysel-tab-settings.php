<?php
/**
 * Eğitimler → "Bireysel Sekmesi" ayar sayfası.
 *
 * Eğitimler (kpl_training) menüsünün altında bir alt-sayfa; Bireysel sekmesinin
 * görselini dile göre yönetir. Seçilen görsel sekmedeki placeholder yerine
 * render edilir, boşsa ikon fallback. Yalnız Bireysel sekmesini etkiler.
 *
 * Option keys: kpl_bireysel_image_{lang}  (attachment ID) — örn. _tr, _en
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

/** Aktif dil sluglarını döndür (Polylang yoksa ['tr']). */
function kpl_bireysel_langs(): array {
    $langs = function_exists('pll_languages_list') ? pll_languages_list(['fields' => 'slug']) : [];
    if (empty($langs)) $langs = ['tr'];
    return $langs;
}

/** Bireysel sekmesi görselini (attachment ID) geçerli dile göre getir. */
function kpl_bireysel_image_id(): int {
    $lang = function_exists('pll_current_language') ? pll_current_language() : 'tr';
    $id   = (int) get_option('kpl_bireysel_image_' . $lang, 0);
    if (!$id) $id = (int) get_option('kpl_bireysel_image_tr', 0); // TR fallback
    return $id;
}

// Eğitimler menüsü altına alt-sayfa.
add_action('admin_menu', function () {
    $hook = add_submenu_page(
        'edit.php?post_type=kpl_training',
        __('Bireysel Sekmesi Görseli', 'kaplan'),
        __('Bireysel Sekmesi', 'kaplan'),
        'manage_options',
        'kpl-bireysel-sekmesi',
        'kpl_bireysel_settings_page'
    );
    $GLOBALS['kpl_bireysel_hook'] = $hook;
});

// Medya kütüphanesi JS — yalnız bu ayar ekranında.
add_action('admin_enqueue_scripts', function ($hook) {
    if (empty($GLOBALS['kpl_bireysel_hook']) || $hook !== $GLOBALS['kpl_bireysel_hook']) return;
    wp_enqueue_media();
});

function kpl_bireysel_settings_page() {
    if (!current_user_can('manage_options')) return;

    $langs = kpl_bireysel_langs();

    // Kaydet.
    if (isset($_POST['kpl_bireysel_save']) && check_admin_referer('kpl_bireysel_save')) {
        foreach ($langs as $slug) {
            $val = absint($_POST['kpl_bireysel_image_' . $slug] ?? 0);
            if ($val) update_option('kpl_bireysel_image_' . $slug, $val);
            else delete_option('kpl_bireysel_image_' . $slug);
        }
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Kaydedildi.', 'kaplan') . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Bireysel Sekmesi Görseli', 'kaplan'); ?></h1>
        <p class="description" style="max-width:640px;">
            <?php esc_html_e('Eğitimler sayfasındaki "Bireysel" sekmesinde gösterilen görsel. Boş bırakılırsa varsayılan ikon görünür. Önerilen oran 4:5 (örn. 800×1000).', 'kaplan'); ?>
        </p>
        <form method="post">
            <?php wp_nonce_field('kpl_bireysel_save'); ?>
            <table class="form-table" role="presentation">
                <?php foreach ($langs as $slug) :
                    $id  = (int) get_option('kpl_bireysel_image_' . $slug, 0);
                    $url = $id ? wp_get_attachment_image_url($id, 'medium') : '';
                ?>
                <tr>
                    <th scope="row"><?php echo esc_html(strtoupper($slug)); ?></th>
                    <td>
                        <div class="kpl-imgpick" data-field="kpl_bireysel_image_<?php echo esc_attr($slug); ?>">
                            <input type="hidden" name="kpl_bireysel_image_<?php echo esc_attr($slug); ?>" value="<?php echo esc_attr($id); ?>" />
                            <div class="kpl-imgpick__preview" style="margin-bottom:10px;">
                                <?php if ($url) : ?><img src="<?php echo esc_url($url); ?>" style="max-width:260px;height:auto;border-radius:8px;display:block;" /><?php endif; ?>
                            </div>
                            <button type="button" class="button kpl-imgpick__select"><?php esc_html_e('Görsel Seç', 'kaplan'); ?></button>
                            <button type="button" class="button kpl-imgpick__remove" style="<?php echo $id ? '' : 'display:none;'; ?>"><?php esc_html_e('Kaldır', 'kaplan'); ?></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php submit_button(__('Kaydet', 'kaplan'), 'primary', 'kpl_bireysel_save'); ?>
        </form>
    </div>
    <script>
    (function ($) {
        $('.kpl-imgpick__select').on('click', function (e) {
            e.preventDefault();
            var wrap = $(this).closest('.kpl-imgpick');
            var frame = wp.media({
                title: <?php echo wp_json_encode(__('Görsel Seç', 'kaplan')); ?>,
                button: { text: <?php echo wp_json_encode(__('Kullan', 'kaplan')); ?> },
                library: { type: 'image' },
                multiple: false
            });
            frame.on('select', function () {
                var a = frame.state().get('selection').first().toJSON();
                var u = (a.sizes && a.sizes.medium) ? a.sizes.medium.url : a.url;
                wrap.find('input[type=hidden]').val(a.id);
                wrap.find('.kpl-imgpick__preview').html('<img src="' + u + '" style="max-width:260px;height:auto;border-radius:8px;display:block;" />');
                wrap.find('.kpl-imgpick__remove').show();
            });
            frame.open();
        });
        $('.kpl-imgpick__remove').on('click', function (e) {
            e.preventDefault();
            var wrap = $(this).closest('.kpl-imgpick');
            wrap.find('input[type=hidden]').val('');
            wrap.find('.kpl-imgpick__preview').empty();
            $(this).hide();
        });
    })(jQuery);
    </script>
    <?php
}
