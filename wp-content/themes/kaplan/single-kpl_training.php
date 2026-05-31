<?php
/**
 * Eğitim detay sayfası (single kpl_training).
 *
 * @package Kaplan
 */

get_header();

while (have_posts()) : the_post();
    $id       = get_the_ID();
    $chip     = function_exists('kpl_training_chip') ? kpl_training_chip($id) : get_post_meta($id, '_kpl_chip', true);
    $icon     = get_post_meta($id, '_kpl_icon', true) ?: 'fa-graduation-cap';
    $duration = get_post_meta($id, '_kpl_duration', true);
    $is_pkg   = get_post_meta($id, '_kpl_package', true) === '1';

    set_query_var('hero_title', get_the_title());
    set_query_var('hero_crumb', __('Eğitimler', 'kaplan'));
    set_query_var('hero_bg', 'hero/Resim-8.jpg');
    get_template_part('template-parts/page-hero');
    ?>

    <section class="section">
        <div class="container detail">
            <div class="detail__main">
                <?php if ($chip) : ?>
                    <span class="training-card__chip"><?php echo esc_html($chip); ?></span>
                <?php endif; ?>
                <div class="detail__content">
                    <?php the_content(); ?>
                </div>
            </div>

            <aside class="detail__aside">
                <div class="detail__card">
                    <div class="training-card__icon"><i class="fa-solid <?php echo esc_attr($icon); ?>"></i></div>
                    <ul class="detail__facts">
                        <?php if ($duration) : ?>
                            <li><i class="fa-regular fa-clock"></i> <span><?php esc_html_e('Süre', 'kaplan'); ?></span><strong><?php echo esc_html($duration); ?></strong></li>
                        <?php endif; ?>
                        <li><i class="fa-solid fa-layer-group"></i> <span><?php esc_html_e('Tür', 'kaplan'); ?></span><strong><?php echo $is_pkg ? esc_html__('Paket', 'kaplan') : esc_html__('Eğitim Programı', 'kaplan'); ?></strong></li>
                    </ul>
                    <a class="btn btn--primary" href="<?php echo esc_url(kpl_localized_url('/egitim-talep-formu/')); ?>"><?php esc_html_e('Talep Et', 'kaplan'); ?></a>
                </div>
            </aside>
        </div>
    </section>

    <?php
    // İlişkili eğitimler (mevcut hariç, aynı dil)
    $related = kpl_query_with_lang_fallback([
        'post_type'      => 'kpl_training',
        'posts_per_page' => 3,
        'post__not_in'   => [$id],
        'orderby'        => 'rand',
        'meta_query'     => [['key' => '_kpl_package', 'compare' => 'NOT EXISTS']],
    ]);
    if ($related->have_posts()) : ?>
    <section class="section section--soft">
        <div class="container">
            <header class="section-head">
                <span class="section-head__eyebrow"><?php esc_html_e('Eğitim Programları', 'kaplan'); ?></span>
                <h2><?php esc_html_e('Diğer eğitimler', 'kaplan'); ?></h2>
                <span class="section-head__line"></span>
            </header>
            <div class="training-grid">
                <?php while ($related->have_posts()) : $related->the_post();
                    $r_chip = function_exists('kpl_training_chip') ? kpl_training_chip(get_the_ID()) : get_post_meta(get_the_ID(), '_kpl_chip', true);
                    $r_icon = get_post_meta(get_the_ID(), '_kpl_icon', true) ?: 'fa-graduation-cap';
                    $r_dur  = get_post_meta(get_the_ID(), '_kpl_duration', true);
                ?>
                    <article class="training-card">
                        <div class="training-card__head">
                            <div class="training-card__icon"><i class="fa-solid <?php echo esc_attr($r_icon); ?>"></i></div>
                            <div>
                                <?php if ($r_chip) : ?>
                                    <div class="training-card__meta"><span class="training-card__chip"><?php echo esc_html($r_chip); ?></span></div>
                                <?php endif; ?>
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            </div>
                        </div>
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>
                        <div class="training-card__footer">
                            <?php if ($r_dur) : ?>
                                <span class="training-card__duration"><i class="fa-regular fa-clock"></i> <?php echo esc_html($r_dur); ?></span>
                            <?php else : ?><span></span><?php endif; ?>
                            <a href="<?php the_permalink(); ?>" class="link-arrow"><?php esc_html_e('Detay', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    set_query_var('cta_title', __('Eğitiminizi planlamaya başlayalım', 'kaplan'));
    set_query_var('cta_sub', __('Hangi eğitimin ekibinize uygun olduğunu birlikte belirleyelim.', 'kaplan'));
    get_template_part('template-parts/cta-band');
    ?>

<?php endwhile; ?>

<?php get_footer(); ?>
