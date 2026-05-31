<?php
/**
 * CPT: Eğitim Programı (kpl_training).
 *
 * Kategori: kpl_training_cat taxonomy'si (admin → Eğitimler → Kategoriler).
 *           Eski _kpl_chip meta'sı yalnız geriye uyum/fallback için okunur.
 *
 * Meta keys:
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

    // Taxonomy: Eğitim Kategorisi — admin'den eklenir/düzenlenir (Eğitimler → Kategoriler).
    register_taxonomy('kpl_training_cat', 'kpl_training', [
        'labels' => [
            'name'          => __('Kategoriler', 'kaplan'),
            'singular_name' => __('Kategori', 'kaplan'),
            'menu_name'     => __('Kategoriler', 'kaplan'),
            'all_items'     => __('Tüm Kategoriler', 'kaplan'),
            'edit_item'     => __('Kategoriyi Düzenle', 'kaplan'),
            'update_item'   => __('Kategoriyi Güncelle', 'kaplan'),
            'add_new_item'  => __('Yeni Kategori Ekle', 'kaplan'),
            'new_item_name' => __('Yeni Kategori Adı', 'kaplan'),
            'search_items'  => __('Kategori Ara', 'kaplan'),
            'not_found'     => __('Kategori bulunamadı.', 'kaplan'),
        ],
        'hierarchical'       => true,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => false,
        'query_var'          => false,
        'rewrite'            => false,
    ]);
});

/**
 * _kpl_chip metinlerini kpl_training_cat term'lerine taşı (ortam başına bir kez).
 *
 * Dil-duyarlı + kendini-onaran (Polylang):
 *   - Tüm dillerdeki eğitimleri tarar (lang='' → Polylang dil filtresini bypass eder).
 *   - Her eğitim için kendi dilinde bir term oluşturur/atar (pll_set_term_language).
 *   - TR ↔ EN eğitim çiftlerinin term'lerini çeviri olarak bağlar.
 *   - Başlamadan önce eski (dilsiz) term'leri siler — kaynak _kpl_chip meta'sı korunur.
 */
add_action('admin_init', function () {
    if (get_option('kpl_training_cat_migrated') === '2') return;
    if (!taxonomy_exists('kpl_training_cat')) return;

    $has_pll = function_exists('pll_get_post_language')
        && function_exists('pll_set_term_language')
        && function_exists('pll_save_term_translations')
        && function_exists('pll_get_post');

    // 1) Eski/dilsiz term'leri temizle — _kpl_chip kaynak veri olarak duruyor.
    $existing = get_terms(['taxonomy' => 'kpl_training_cat', 'hide_empty' => false, 'fields' => 'ids', 'lang' => '']);
    if (!is_wp_error($existing)) {
        foreach ($existing as $tid) wp_delete_term($tid, 'kpl_training_cat');
    }

    // 2) Tüm dillerdeki eğitimleri al; chip'ten dile özel term oluştur/ata.
    $ids = get_posts([
        'post_type'      => 'kpl_training',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
        'lang'           => '',
    ]);

    $made      = [];   // "lang|name" => term_id (aynı çalışmada tekrar kullan)
    $post_term = [];   // post_id => term_id

    $term_for = function ($name, $lang) use (&$made, $has_pll) {
        $key = $lang . '|' . $name;
        if (isset($made[$key])) return $made[$key];
        $slug = sanitize_title($name) . ($lang ? '-' . $lang : '');
        $res  = wp_insert_term($name, 'kpl_training_cat', ['slug' => $slug]);
        if (is_wp_error($res)) {
            $ex = get_term_by('slug', $slug, 'kpl_training_cat');
            if (!$ex) return 0;
            $tid = (int) $ex->term_id;
        } else {
            $tid = (int) $res['term_id'];
        }
        if ($has_pll && $lang) pll_set_term_language($tid, $lang);
        $made[$key] = $tid;
        return $tid;
    };

    foreach ($ids as $pid) {
        $chip = get_post_meta($pid, '_kpl_chip', true);
        if ($chip === '') continue;
        $lang = $has_pll ? (pll_get_post_language($pid) ?: '') : '';
        $tid  = $term_for($chip, $lang);
        if ($tid) {
            wp_set_object_terms($pid, $tid, 'kpl_training_cat', false);
            $post_term[$pid] = $tid;
        }
    }

    // 3) TR ↔ EN term çevirilerini bağla (TR eğitimden EN çevirisini bul).
    if ($has_pll) {
        $linked = [];
        foreach ($post_term as $pid => $tr_term) {
            if (pll_get_post_language($pid) !== 'tr') continue;
            $en_post = pll_get_post($pid, 'en');
            if ($en_post && isset($post_term[$en_post])) {
                $en_term = $post_term[$en_post];
                $pair = $tr_term . '-' . $en_term;
                if (isset($linked[$pair])) continue;
                pll_save_term_translations(['tr' => $tr_term, 'en' => $en_term]);
                $linked[$pair] = 1;
            }
        }
    }

    update_option('kpl_training_cat_migrated', '2');
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

// Admin'de ikon ızgarasının render olması için Font Awesome'ı yalnız eğitim
// düzenleme ekranına yükle (frontend ile aynı CDN/sürüm — functions.php:97).
add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) return;
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'kpl_training') return;
    wp_enqueue_style(
        'kaplan-fa',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        [],
        '6.5.1'
    );
});

/**
 * Eğitimin kategori chip metnini döndürür.
 * Önce kpl_training_cat term'i; yoksa eski _kpl_chip meta'sı (geriye uyum).
 */
function kpl_training_chip($post_id) {
    $terms = get_the_terms($post_id, 'kpl_training_cat');
    if ($terms && !is_wp_error($terms)) {
        return $terms[0]->name;
    }
    return get_post_meta($post_id, '_kpl_chip', true);
}

/** Admin ikon ızgarasının kürelü FA 6.5.1 (solid) seçenekleri. */
function kpl_training_icon_options() {
    return [
        'fa-chart-column', 'fa-chart-line', 'fa-chart-pie', 'fa-magnifying-glass-chart',
        'fa-table', 'fa-table-cells', 'fa-database', 'fa-file-excel', 'fa-diagram-project',
        'fa-graduation-cap', 'fa-user-graduate', 'fa-users', 'fa-user-tie', 'fa-briefcase',
        'fa-lightbulb', 'fa-gear', 'fa-brain', 'fa-robot', 'fa-laptop-code', 'fa-calculator',
        'fa-clipboard-list', 'fa-bullseye', 'fa-handshake', 'fa-book', 'fa-certificate', 'fa-folder',
    ];
}

function kpl_training_meta_box_cb($post) {
    wp_nonce_field('kpl_training_save', 'kpl_training_nonce');
    $icon      = get_post_meta($post->ID, '_kpl_icon', true);
    $duration  = get_post_meta($post->ID, '_kpl_duration', true);
    $package   = get_post_meta($post->ID, '_kpl_package', true);
    $pkg_items = get_post_meta($post->ID, '_kpl_pkg_items', true);
    $is_pkg    = ($package === '1');
    // İkon seçenekleri — kayıtlı ikon listede yoksa korunsun diye başa eklenir.
    $icon_options = kpl_training_icon_options();
    if ($icon !== '' && !in_array($icon, $icon_options, true)) {
        array_unshift($icon_options, $icon);
    }
    ?>
    <p class="kpl-pkg-toggle">
        <label>
            <input type="checkbox" name="kpl_package" id="kpl_package_cb" value="1" <?php checked($package, '1'); ?> />
            <strong><?php esc_html_e('Bu bir paket eğitim programıdır', 'kaplan'); ?></strong>
        </label>
    </p>

    <!-- TEKİL eğitim alanları (paket DEĞİLKEN) -->
    <div class="kpl-fields kpl-fields--single"<?php echo $is_pkg ? ' style="display:none"' : ''; ?>>
        <p style="margin-top:0;color:#646970;font-size:12px;">
            <?php esc_html_e('Kategori, yandaki “Kategoriler” kutusundan seçilir veya yeni eklenir.', 'kaplan'); ?>
        </p>
        <div class="kpl-icon-field">
            <label><strong><?php esc_html_e('İkon', 'kaplan'); ?></strong></label>
            <input type="hidden" name="kpl_icon" id="kpl_icon_value" value="<?php echo esc_attr($icon); ?>" />
            <div class="kpl-icon-grid" role="listbox" aria-label="<?php esc_attr_e('İkon seç', 'kaplan'); ?>">
                <?php foreach ($icon_options as $ic) : ?>
                    <button type="button" class="kpl-icon-tile<?php echo $icon === $ic ? ' is-selected' : ''; ?>" data-icon="<?php echo esc_attr($ic); ?>" title="<?php echo esc_attr($ic); ?>" aria-label="<?php echo esc_attr($ic); ?>">
                        <i class="fa-solid <?php echo esc_attr($ic); ?>"></i>
                    </button>
                <?php endforeach; ?>
            </div>
            <small><?php esc_html_e('Karta çıkacak ikonu seçin.', 'kaplan'); ?></small>
        </div>
        <p>
            <label><strong><?php esc_html_e('Süre', 'kaplan'); ?></strong></label>
            <input type="text" name="kpl_duration" value="<?php echo esc_attr($duration); ?>" style="width:100%" placeholder="2 gün" />
        </p>
    </div>

    <!-- PAKET program alanları (paket İKEN) -->
    <div class="kpl-fields kpl-fields--package"<?php echo $is_pkg ? '' : ' style="display:none"'; ?>>
        <p>
            <label><strong><?php esc_html_e('Paket İçeriği (Dahil Olan Eğitimler)', 'kaplan'); ?></strong></label>
            <textarea name="kpl_pkg_items" rows="6" style="width:100%" placeholder="<?php esc_attr_e('Her satıra bir eğitim adı', 'kaplan'); ?>"><?php echo esc_textarea($pkg_items); ?></textarea>
            <small><?php esc_html_e('Her satır, kartta ayrı bir madde olarak listelenir. Paket açıklaması için soldaki ana içerik editörünü kullanın.', 'kaplan'); ?></small>
        </p>
    </div>

    <style>
        .kpl-pkg-toggle{padding:.5rem .6rem;background:#f6f7f7;border:1px solid #e0e0e0;border-radius:6px;margin-top:0}
        .kpl-icon-field label{display:block;font-weight:600;margin-bottom:.25rem}
        .kpl-icon-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(36px,1fr));gap:6px;margin:.4rem 0}
        .kpl-icon-tile{display:flex;align-items:center;justify-content:center;height:36px;border:1px solid #dcdcde;border-radius:6px;background:#fff;cursor:pointer;font-size:16px;color:#50575e;transition:border-color .12s,background .12s,color .12s}
        .kpl-icon-tile:hover{border-color:#5AC8FB;color:#1d2327}
        .kpl-icon-tile.is-selected{border-color:#5AC8FB;background:#EAF7FE;color:#0F1B2D;box-shadow:0 0 0 1px #5AC8FB}
    </style>
    <script>
    (function(){
        // İkon ızgarası
        var grid=document.querySelector('.kpl-icon-grid');
        if(grid){
            var input=document.getElementById('kpl_icon_value');
            grid.addEventListener('click',function(e){
                var tile=e.target.closest('.kpl-icon-tile');
                if(!tile)return;
                grid.querySelectorAll('.kpl-icon-tile').forEach(function(t){t.classList.remove('is-selected');});
                tile.classList.add('is-selected');
                input.value=tile.getAttribute('data-icon');
            });
        }
        // Paket / tekil alan değişimi
        var cb=document.getElementById('kpl_package_cb');
        if(cb){
            var single=document.querySelector('.kpl-fields--single');
            var pkg=document.querySelector('.kpl-fields--package');
            cb.addEventListener('change',function(){
                if(single) single.style.display = cb.checked ? 'none' : '';
                if(pkg)    pkg.style.display    = cb.checked ? '' : 'none';
            });
        }
    })();
    </script>
    <?php
}

add_action('save_post_kpl_training', function ($post_id) {
    if (!isset($_POST['kpl_training_nonce']) || !wp_verify_nonce($_POST['kpl_training_nonce'], 'kpl_training_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['kpl_icon' => '_kpl_icon', 'kpl_duration' => '_kpl_duration'];
    foreach ($fields as $input => $meta) {
        if (isset($_POST[$input])) {
            update_post_meta($post_id, $meta, sanitize_text_field(wp_unslash($_POST[$input])));
        }
    }
    update_post_meta($post_id, '_kpl_package', isset($_POST['kpl_package']) ? '1' : '');
    if (isset($_POST['kpl_pkg_items'])) {
        update_post_meta($post_id, '_kpl_pkg_items', sanitize_textarea_field(wp_unslash($_POST['kpl_pkg_items'])));
    }
});

/**
 * Eski paket içerik formatını ('… Dahil Olan Eğitimler: A · B · C') yeni
 * _kpl_pkg_items meta'sına taşı ve gövdeyi yalnız açıklama olacak şekilde temizle.
 * Ortam başına bir kez; TR + EN marker'ları destekler.
 */
add_action('admin_init', function () {
    if (get_option('kpl_pkg_items_migrated')) return;
    if (!post_type_exists('kpl_training')) return;

    $ids = get_posts([
        'post_type'      => 'kpl_training',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
        'lang'           => '',
        'meta_query'     => [['key' => '_kpl_package', 'value' => '1']],
    ]);
    foreach ($ids as $pid) {
        if (get_post_meta($pid, '_kpl_pkg_items', true) !== '') continue; // taşınmış
        $content = wp_strip_all_tags(get_post_field('post_content', $pid));
        $parts   = preg_split('/(?:Dahil Olan Eğitimler:|Included Trainings:)\s*/u', $content, 2);
        if (count($parts) < 2) continue; // marker yok
        $desc  = trim($parts[0]);
        $items = array_values(array_filter(array_map('trim', preg_split('/\s*·\s*/u', trim($parts[1])))));
        if ($items) {
            update_post_meta($pid, '_kpl_pkg_items', implode("\n", $items));
            wp_update_post(['ID' => $pid, 'post_content' => $desc]);
        }
    }
    update_option('kpl_pkg_items_migrated', 1);
});

// Admin list table — süre/paket sütunları (Kategori sütunu taxonomy'den otomatik gelir).
add_filter('manage_kpl_training_posts_columns', function ($cols) {
    $new = [];
    foreach ($cols as $k => $v) {
        $new[$k] = $v;
        if ($k === 'title') {
            $new['kpl_duration'] = __('Süre', 'kaplan');
            $new['kpl_package']  = __('Paket', 'kaplan');
        }
    }
    return $new;
});
add_action('manage_kpl_training_posts_custom_column', function ($col, $post_id) {
    if ($col === 'kpl_duration') echo esc_html(get_post_meta($post_id, '_kpl_duration', true));
    if ($col === 'kpl_package')  echo get_post_meta($post_id, '_kpl_package', true) === '1' ? '★' : '—';
}, 10, 2);
