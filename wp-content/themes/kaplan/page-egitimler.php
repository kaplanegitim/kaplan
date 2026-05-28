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
            <p><?php esc_html_e('Tekil eğitimler ve paket programlar arasından seçebilir, isteğe göre özelleştirebilirsiniz.', 'kaplan'); ?></p>
            <span class="section-head__line"></span>
        </header>

        <div data-tabs>
            <div class="tabs" role="tablist">
                <button class="is-active" data-tab="tekil"><?php esc_html_e('Tekil Eğitimler', 'kaplan'); ?></button>
                <button data-tab="paket"><?php esc_html_e('Paket Programlar', 'kaplan'); ?></button>
                <button data-tab="kurumsal" id="kurumsal"><?php esc_html_e('Kurumsal', 'kaplan'); ?></button>
                <button data-tab="bireysel" id="bireysel"><?php esc_html_e('Bireysel', 'kaplan'); ?></button>
            </div>

            <!-- TEKİL -->
            <div class="tab-panel is-active" data-panel="tekil">
                <?php if ($tekil_q->have_posts()) : ?>
                <div class="training-grid">
                    <?php while ($tekil_q->have_posts()) : $tekil_q->the_post();
                        $chip     = get_post_meta(get_the_ID(), '_kpl_chip', true);
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
            <div class="tab-panel" data-panel="paket">
                <?php if ($paket_q->have_posts()) : ?>
                <div class="package-grid">
                    <?php $idx = 0; while ($paket_q->have_posts()) : $paket_q->the_post(); $idx++; ?>
                        <article class="package-card">
                            <div class="package-card__num"><?php echo esc_html(str_pad($idx, 2, '0', STR_PAD_LEFT)); ?></div>
                            <h3><?php the_title(); ?></h3>
                            <?php
                            $body  = wp_strip_all_tags(get_the_content());
                            // İlk paragraf ana açıklama
                            $parts = preg_split('/' . preg_quote(__('Dahil Olan Eğitimler:', 'kaplan'), '/') . '\s*/u', $body);
                            $desc  = trim($parts[0] ?? '');
                            $list  = trim($parts[1] ?? '');
                            ?>
                            <?php if ($desc) : ?>
                                <p><?php echo esc_html($desc); ?></p>
                            <?php endif; ?>
                            <?php if ($list) : ?>
                                <ul class="package-card__list">
                                    <?php foreach (preg_split('/\s*·\s*/u', $list) as $item) {
                                        $item = trim($item);
                                        if ($item) echo '<li>' . esc_html($item) . '</li>';
                                    } ?>
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

            <!-- KURUMSAL -->
            <div class="tab-panel" data-panel="kurumsal">
                <div class="about-intro__grid">
                    <div class="about-intro__media about-intro__media--ph about-intro__media--ph-1">
                        <i class="fa-solid fa-building" aria-hidden="true"></i>
                    </div>
                    <div class="about-intro__text">
                        <span class="section-head__eyebrow"><?php esc_html_e('Kurumsal Eğitimler', 'kaplan'); ?></span>
                        <h2><?php esc_html_e('Ekibinize', 'kaplan'); ?> <span class="accent"><?php esc_html_e('özel', 'kaplan'); ?></span> <?php esc_html_e('programlar', 'kaplan'); ?></h2>
                        <p><?php esc_html_e('Şirketinizin ihtiyaçlarına göre özelleştirilebilen sınıf-içi ve online eğitim programları sunuyoruz. Sektörünüze ve hedeflerinize göre içerik özelleştirme yapıyoruz.', 'kaplan'); ?></p>
                        <ul class="check-list">
                            <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Talep odaklı içerik özelleştirme', 'kaplan'); ?></li>
                            <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Sektör örnekleri ile uygulamalı pratik', 'kaplan'); ?></li>
                            <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Sınıf-içi veya online format', 'kaplan'); ?></li>
                            <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Sertifikalı katılım', 'kaplan'); ?></li>
                        </ul>
                        <a href="<?php echo esc_url(kpl_localized_url('/egitim-talep-formu/')); ?>" class="btn btn--primary"><?php esc_html_e('Teklif iste', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- BİREYSEL -->
            <div class="tab-panel" data-panel="bireysel">
                <div class="about-intro__grid">
                    <div class="about-intro__media about-intro__media--ph about-intro__media--ph-2">
                        <i class="fa-solid fa-user-graduate" aria-hidden="true"></i>
                    </div>
                    <div class="about-intro__text">
                        <span class="section-head__eyebrow"><?php esc_html_e('Bireysel Eğitimler', 'kaplan'); ?></span>
                        <h2><span class="accent"><?php esc_html_e('Yeni Mezun', 'kaplan'); ?></span> <?php esc_html_e('Yaşam Kiti', 'kaplan'); ?></h2>
                        <p><?php esc_html_e('Kariyere güçlü başlangıç için pratik araçlar, çalışma defterleri ve videolar. Bireysel olarak katılabileceğiniz açık eğitimler ve self-paced online içerikler.', 'kaplan'); ?></p>
                        <ul class="check-list">
                            <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Online video kütüphanesi', 'kaplan'); ?></li>
                            <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Çalışma defterleri ve şablonlar', 'kaplan'); ?></li>
                            <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Açık sınıf eğitimleri', 'kaplan'); ?></li>
                            <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Mentor desteği', 'kaplan'); ?></li>
                        </ul>
                        <a href="<?php echo esc_url(kpl_localized_url('/egitim-talep-formu/')); ?>" class="btn btn--primary"><?php esc_html_e('Detaylı bilgi', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
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
