<?php
/**
 * Eğitimler → "Bireysel Sekmesi" ayar sayfası.
 *
 * Eğitimler (kpl_training) menüsünün altında bir alt-sayfa; Bireysel sekmesinin
 * tüm metinlerini + görselini dile göre yönetir. Boş bırakılan alanlar şablondaki
 * çeviri varsayılanına (.l10n.php) düşer. Yalnız Bireysel sekmesini etkiler.
 *
 * Option keys (her dil için): kpl_bireysel_{field}_{lang}
 *   field: eyebrow | title_accent | title | desc | list | image
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

/** Geçerli dil slug'ı. */
function kpl_bireysel_cur_lang(): string {
    return function_exists('pll_current_language') ? (pll_current_language() ?: 'tr') : 'tr';
}

/** Metin alanı — option doluysa onu, değilse verilen (çevrilmiş) varsayılanı döndür. */
function kpl_bireysel_text(string $field, string $default): string {
    $val = get_option('kpl_bireysel_' . $field . '_' . kpl_bireysel_cur_lang(), '');
    return ($val !== '' && $val !== false) ? $val : $default;
}

/** Liste alanı — satır-satır option; boşsa varsayılan diziyi döndür. */
function kpl_bireysel_list(array $default): array {
    $raw = get_option('kpl_bireysel_list_' . kpl_bireysel_cur_lang(), '');
    if ($raw === '' || $raw === false) return $default;
    $items = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw))));
    return $items ?: $default;
}

/** Bireysel sekmesi görseli (attachment ID), geçerli dile göre — yoksa TR fallback. */
function kpl_bireysel_image_id(): int {
    $id = (int) get_option('kpl_bireysel_image_' . kpl_bireysel_cur_lang(), 0);
    if (!$id) $id = (int) get_option('kpl_bireysel_image_tr', 0);
    return $id;
}

// Eğitimler menüsü altına alt-sayfa.
add_action('admin_menu', function () {
    $hook = add_submenu_page(
        'edit.php?post_type=kpl_training',
        __('Bireysel Sekmesi İçeriği', 'kaplan'),
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
        $text_fields = [
            'eyebrow'      => 'sanitize_text_field',
            'title_accent' => 'sanitize_text_field',
            'title'        => 'sanitize_text_field',
            'desc'         => 'sanitize_textarea_field',
            'list'         => 'sanitize_textarea_field',
        ];
        foreach ($langs as $slug) {
            foreach ($text_fields as $field => $san) {
                $key = 'kpl_bireysel_' . $field . '_' . $slug;
                $val = call_user_func($san, wp_unslash($_POST[$key] ?? ''));
                if ($val !== '') update_option($key, $val);
                else delete_option($key);
            }
            $imgkey = 'kpl_bireysel_image_' . $slug;
            $img = absint($_POST[$imgkey] ?? 0);
            if ($img) update_option($imgkey, $img);
            else delete_option($imgkey);
        }
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Kaydedildi.', 'kaplan') . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Bireysel Sekmesi İçeriği', 'kaplan'); ?></h1>
        <p class="description" style="max-width:680px;">
            <?php esc_html_e('Eğitimler sayfasındaki "Bireysel" sekmesinin metinleri ve görseli. Boş bırakılan alanlar varsayılan metni kullanır. Görsel için önerilen oran 4:5 (örn. 800×1000).', 'kaplan'); ?>
        </p>
        <form method="post">
            <?php wp_nonce_field('kpl_bireysel_save'); ?>
            <?php foreach ($langs as $slug) :
                $eyebrow = (string) get_option('kpl_bireysel_eyebrow_' . $slug, '');
                $accent  = (string) get_option('kpl_bireysel_title_accent_' . $slug, '');
                $title   = (string) get_option('kpl_bireysel_title_' . $slug, '');
                $desc    = (string) get_option('kpl_bireysel_desc_' . $slug, '');
                $list    = (string) get_option('kpl_bireysel_list_' . $slug, '');
                $img_id  = (int) get_option('kpl_bireysel_image_' . $slug, 0);
                $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'medium') : '';
            ?>
            <h2 style="margin-top:1.6em;border-bottom:1px solid #ccd0d4;padding-bottom:.3em;"><?php echo esc_html(strtoupper($slug)); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="eyebrow_<?php echo esc_attr($slug); ?>"><?php esc_html_e('Üst etiket', 'kaplan'); ?></label></th>
                    <td><input type="text" class="regular-text" id="eyebrow_<?php echo esc_attr($slug); ?>" name="kpl_bireysel_eyebrow_<?php echo esc_attr($slug); ?>" value="<?php echo esc_attr($eyebrow); ?>" placeholder="<?php esc_attr_e('Bireysel Eğitimler', 'kaplan'); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="accent_<?php echo esc_attr($slug); ?>"><?php esc_html_e('Başlık — vurgu (cyan)', 'kaplan'); ?></label></th>
                    <td><input type="text" class="regular-text" id="accent_<?php echo esc_attr($slug); ?>" name="kpl_bireysel_title_accent_<?php echo esc_attr($slug); ?>" value="<?php echo esc_attr($accent); ?>" placeholder="<?php esc_attr_e('Yeni Mezun', 'kaplan'); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="title_<?php echo esc_attr($slug); ?>"><?php esc_html_e('Başlık — devamı', 'kaplan'); ?></label></th>
                    <td><input type="text" class="regular-text" id="title_<?php echo esc_attr($slug); ?>" name="kpl_bireysel_title_<?php echo esc_attr($slug); ?>" value="<?php echo esc_attr($title); ?>" placeholder="<?php esc_attr_e('Yaşam Kiti', 'kaplan'); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="desc_<?php echo esc_attr($slug); ?>"><?php esc_html_e('Açıklama', 'kaplan'); ?></label></th>
                    <td><textarea class="large-text" rows="3" id="desc_<?php echo esc_attr($slug); ?>" name="kpl_bireysel_desc_<?php echo esc_attr($slug); ?>"><?php echo esc_textarea($desc); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="list_<?php echo esc_attr($slug); ?>"><?php esc_html_e('Liste maddeleri', 'kaplan'); ?></label></th>
                    <td>
                        <textarea class="large-text code" rows="5" id="list_<?php echo esc_attr($slug); ?>" name="kpl_bireysel_list_<?php echo esc_attr($slug); ?>" placeholder="<?php esc_attr_e('Her satıra bir madde', 'kaplan'); ?>"><?php echo esc_textarea($list); ?></textarea>
                        <p class="description"><?php esc_html_e('Her satıra bir madde yazın. Boşsa varsayılan 4 madde kullanılır.', 'kaplan'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Görsel', 'kaplan'); ?></th>
                    <td>
                        <div class="kpl-imgpick">
                            <input type="hidden" name="kpl_bireysel_image_<?php echo esc_attr($slug); ?>" value="<?php echo esc_attr($img_id); ?>" />
                            <div class="kpl-imgpick__preview" style="margin-bottom:10px;">
                                <?php if ($img_url) : ?><img src="<?php echo esc_url($img_url); ?>" style="max-width:260px;height:auto;border-radius:8px;display:block;" /><?php endif; ?>
                            </div>
                            <button type="button" class="button kpl-imgpick__select"><?php esc_html_e('Görsel Seç', 'kaplan'); ?></button>
                            <button type="button" class="button kpl-imgpick__remove" style="<?php echo $img_id ? '' : 'display:none;'; ?>"><?php esc_html_e('Kaldır', 'kaplan'); ?></button>
                        </div>
                    </td>
                </tr>
            </table>
            <?php endforeach; ?>
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
