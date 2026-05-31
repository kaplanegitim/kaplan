<?php
/**
 * Eğitimler sayfa template'i — kpl_training CPT'sinden beslenir.
 *
 * @package Kaplan
 */

get_header();

$img = KAPLAN_URI . '/assets/img';

set_query_var('hero_title', __('<span>Veriyle</span> çalışan herkes için eğitim programları', 'kaplan'));
set_query_var('hero_sub',   __('Bireysel ve kurumsal ihtiyaçlara özel; veri yönetimi, yönetici gelişimi ve yeni mezun programlarımız.', 'kaplan'));
set_query_var('hero_crumb', __('Eğitimler', 'kaplan'));
set_query_var('hero_bg',    'hero/Resim-8.jpg');
get_template_part('template-parts/page-hero');

// Tekil eğitimler (paket olmayan)
$kpl_run_query = function (array $args): WP_Query {
    return function_exists('kpl_query_with_lang_fallback')
        ? kpl_query_with_lang_fallback($args)
        : new WP_Query($args);
};

$tekil_q = $kpl_run_query([
    'post_type'      => 'kpl_training',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'meta_query'     => [
        [
            'relation' => 'OR',
            ['key' => '_kpl_package', 'value' => '1', 'compare' => '!='],
            ['key' => '_kpl_package', 'compare' => 'NOT EXISTS'],
        ],
    ],
]);

// Paket programlar
$paket_q = $kpl_run_query([
    'post_type'      => 'kpl_training',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'meta_query'     => [['key' => '_kpl_package', 'value' => '1']],
]);
?>

<section class="section">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Eğitim Çözümleri', 'kaplan'); ?></span>
            <h2><?php esc_html_e('İhtiyacınıza özel programlar', 'kaplan'); ?></h2>
            <p><?php esc_html_e('Özgün içerikleriyle değer katan eğitimler ve paket programlar sunuyoruz.', 'kaplan'); ?></p>
            <span class="section-head__line"></span>
        </header>

        <div data-tabs>
            <div class="tabs" role="tablist">
                <button class="is-active" data-tab="paket"><?php esc_html_e('Paket Programlar', 'kaplan'); ?></button>
                <button data-tab="tekil"><?php esc_html_e('Tekil Eğitimler', 'kaplan'); ?></button>
                <button data-tab="bireysel" id="bireysel"><?php esc_html_e('Bireysel', 'kaplan'); ?></button>
            </div>

            <!-- TEKİL -->
            <div class="tab-panel" data-panel="tekil">
                <?php if ($tekil_q->have_posts()) : ?>
                <div class="training-grid">
                    <?php while ($tekil_q->have_posts()) : $tekil_q->the_post();
                        $chip     = function_exists('kpl_training_chip') ? kpl_training_chip(get_the_ID()) : get_post_meta(get_the_ID(), '_kpl_chip', true);
                        $icon     = get_post_meta(get_the_ID(), '_kpl_icon', true) ?: 'fa-graduation-cap';
                        $duration = get_post_meta(get_the_ID(), '_kpl_duration', true);
                    ?>
                        <article class="training-card">
                            <div class="training-card__head">
                                <div class="training-card__icon"><i class="fa-solid <?php echo esc_attr($icon); ?>"></i></div>
                                <div>
                                    <?php if ($chip) : ?>
                                        <div class="training-card__meta"><span class="training-card__chip"><?php echo esc_html($chip); ?></span></div>
                                    <?php endif; ?>
                                    <h3><?php the_title(); ?></h3>
                                </div>
                            </div>
                            <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 28)); ?></p>
                            <div class="training-card__footer">
                                <?php if ($duration) : ?>
                                    <span class="training-card__duration"><i class="fa-regular fa-clock"></i> <?php echo esc_html($duration); ?></span>
                                <?php else : ?>
                                    <span></span>
                                <?php endif; ?>
                                <a href="<?php echo esc_url(kpl_localized_url('/egitim-talep-formu/')); ?>" class="link-arrow"><?php esc_html_e('Talep et', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <?php else : ?>
                    <p style="text-align:center;color:var(--c-muted);"><?php esc_html_e('Henüz tekil eğitim eklenmemiş.', 'kaplan'); ?></p>
                <?php endif; ?>
            </div>

            <!-- PAKET -->
            <div class="tab-panel is-active" data-panel="paket">
                <?php if ($paket_q->have_posts()) : ?>
                <div class="package-grid">
                    <?php $idx = 0; while ($paket_q->have_posts()) : $paket_q->the_post(); $idx++; ?>
                        <article class="package-card">
                            <div class="package-card__num"><?php echo esc_html(str_pad($idx, 2, '0', STR_PAD_LEFT)); ?></div>
                            <h3><?php the_title(); ?></h3>
                            <?php
                            $desc      = trim(wp_strip_all_tags(get_the_content()));
                            $items_raw = get_post_meta(get_the_ID(), '_kpl_pkg_items', true);
                            $list      = $items_raw !== ''
                                ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $items_raw))))
                                : [];
                            // Geriye uyum: meta boşsa eski gövde-formatından (TR/EN marker) ayrıştır.
                            if (!$list) {
                                $parts = preg_split('/(?:' . preg_quote(__('Dahil Olan Eğitimler:', 'kaplan'), '/') . '|Included Trainings:)\s*/u', $desc, 2);
                                $desc  = trim($parts[0] ?? '');
                                $raw   = trim($parts[1] ?? '');
                                if ($raw !== '') $list = array_values(array_filter(array_map('trim', preg_split('/\s*·\s*/u', $raw))));
                            }
                            ?>
                            <?php if ($desc) : ?>
                                <p><?php echo esc_html($desc); ?></p>
                            <?php endif; ?>
                            <?php if ($list) : ?>
                                <ul class="package-card__list">
                                    <?php foreach ($list as $item) echo '<li>' . esc_html($item) . '</li>'; ?>
                                </ul>
                            <?php endif; ?>
                            <a href="<?php echo esc_url(kpl_localized_url('/egitim-talep-formu/')); ?>" class="btn btn--primary"><?php esc_html_e('Talep Et', 'kaplan'); ?></a>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <?php else : ?>
                    <p style="text-align:center;color:var(--c-muted);"><?php esc_html_e('Henüz paket program eklenmemiş.', 'kaplan'); ?></p>
                <?php endif; ?>
            </div>

            <!-- BİREYSEL -->
            <div class="tab-panel" data-panel="bireysel">
                <div class="about-intro__grid">
                    <?php $kpl_bir_img = function_exists('kpl_bireysel_image_id') ? kpl_bireysel_image_id() : 0; ?>
                    <?php if ($kpl_bir_img) : ?>
                    <div class="about-intro__media">
                        <?php echo wp_get_attachment_image($kpl_bir_img, 'large', false, ['loading' => 'lazy', 'alt' => '']); ?>
                    </div>
                    <?php else : ?>
                    <div class="about-intro__media about-intro__media--ph about-intro__media--ph-2">
                        <i class="fa-solid fa-user-graduate" aria-hidden="true"></i>
                    </div>
                    <?php endif; ?>
                    <div class="about-intro__text">
                        <span class="section-head__eyebrow"><?php esc_html_e('Bireysel Eğitimler', 'kaplan'); ?></span>
                        <h2><span class="accent"><?php esc_html_e('Yeni Mezun', 'kaplan'); ?></span> <?php esc_html_e('Yaşam Kiti', 'kaplan'); ?></h2>
                        <p><?php esc_html_e('Kariyere güçlü başlangıç için pratik araçlar, çalışma defterleri ve videolar. Bireysel olarak katılabileceğiniz açık eğitimler ve kendi hızınızda ilerleyebileceğiniz online içerikler.', 'kaplan'); ?></p>
                        <ul class="check-list">
                            <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Online video kütüphanesi', 'kaplan'); ?></li>
                            <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Çalışma defterleri ve şablonlar', 'kaplan'); ?></li>
                            <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Açık sınıf eğitimleri', 'kaplan'); ?></li>
                            <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Mentor desteği', 'kaplan'); ?></li>
                        </ul>
                        <form class="kpl-form">
                            <input type="hidden" name="form_type" value="bireysel" />
                            <div aria-hidden="true" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">
                                <input type="text" name="_kpl_website" tabindex="-1" autocomplete="off" />
                            </div>
                            <div class="form-grid">
                                <div class="form-field">
                                    <label><?php esc_html_e('Ad Soyad', 'kaplan'); ?></label>
                                    <input type="text" name="name" />
                                </div>
                                <div class="form-field">
                                    <label><?php esc_html_e('E-posta', 'kaplan'); ?> <span class="req">*</span></label>
                                    <input type="email" name="email" required />
                                </div>
                                <div class="form-field form-field--full">
                                    <label><?php esc_html_e('İlgilendiğiniz program', 'kaplan'); ?> <span class="req">*</span></label>
                                    <input type="text" name="program" required placeholder="<?php esc_attr_e('Örn: Yeni Mezunun Yaşam Kiti', 'kaplan'); ?>" />
                                </div>
                            </div>
                            <div class="form-actions">
                                <span class="form-note"><?php esc_html_e('Bilgilerinizi alıp sizi bireysel eğitim sayfamıza yönlendireceğiz.', 'kaplan'); ?></span>
                                <button type="submit" class="btn btn--primary"><?php esc_html_e('Gönder ve devam et', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></button>
                            </div>
                            <div class="kpl-form__status" role="status" aria-live="polite"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
set_query_var('cta_title', __('Eğitiminizi planlamaya başlayalım', 'kaplan'));
set_query_var('cta_sub',   __('Hangi eğitimin ekibinize uygun olduğunu birlikte belirleyelim.', 'kaplan'));
get_template_part('template-parts/cta-band');

get_footer();
