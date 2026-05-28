<?php
/**
 * Proje/vaka detay sayfası (single kpl_case).
 *
 * @package Kaplan
 */

get_header();

while (have_posts()) : the_post();
    $id     = get_the_ID();
    $client = get_post_meta($id, '_kpl_client', true);
    $tags   = get_post_meta($id, '_kpl_tags', true);

    set_query_var('hero_title', get_the_title());
    set_query_var('hero_crumb', __('Proje Örnekleri', 'kaplan'));
    set_query_var('hero_bg', 'hero/Resim-4.jpg');
    get_template_part('template-parts/page-hero');
    ?>

    <section class="section">
        <div class="container detail">
            <div class="detail__main">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="video-frame" style="margin-bottom:2rem; aspect-ratio:16/9;">
                        <?php the_post_thumbnail('large', ['style' => 'width:100%;height:100%;object-fit:cover;']); ?>
                    </div>
                <?php endif; ?>
                <div class="detail__content">
                    <?php the_content(); ?>
                </div>
                <?php if ($tags) : ?>
                    <div class="case-card__tags" style="margin-top:1.5rem;">
                        <?php foreach (preg_split('/\s*,\s*/u', $tags) as $tag) {
                            $tag = trim($tag);
                            if ($tag) echo '<span>' . esc_html($tag) . '</span>';
                        } ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="detail__aside">
                <div class="detail__card">
                    <ul class="detail__facts">
                        <?php if ($client) : ?>
                            <li><i class="fa-solid fa-building"></i> <span><?php esc_html_e('Müşteri / Marka', 'kaplan'); ?></span><strong><?php echo esc_html($client); ?></strong></li>
                        <?php endif; ?>
                    </ul>
                    <a class="btn btn--primary" href="<?php echo esc_url(kpl_localized_url('/egitim-talep-formu/')); ?>"><?php esc_html_e('Projenizi konuşalım', 'kaplan'); ?></a>
                </div>
            </aside>
        </div>
    </section>

    <?php
    $related = kpl_query_with_lang_fallback([
        'post_type'      => 'kpl_case',
        'posts_per_page' => 3,
        'post__not_in'   => [$id],
        'orderby'        => 'rand',
    ]);
    if ($related->have_posts()) :
        $fallback_bg = KAPLAN_URI . '/assets/img/hero/Resim-4.jpg';
    ?>
    <section class="section section--soft">
        <div class="container">
            <header class="section-head">
                <span class="section-head__eyebrow"><?php esc_html_e('Proje Örnekleri', 'kaplan'); ?></span>
                <h2><?php esc_html_e('Diğer projeler', 'kaplan'); ?></h2>
                <span class="section-head__line"></span>
            </header>
            <div class="portfolio-grid">
                <?php while ($related->have_posts()) : $related->the_post();
                    $r_client = get_post_meta(get_the_ID(), '_kpl_client', true);
                    $thumb    = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: $fallback_bg;
                ?>
                    <a class="case-card" href="<?php the_permalink(); ?>">
                        <div class="case-card__media" style="background-image:url('<?php echo esc_url($thumb); ?>');"></div>
                        <div class="case-card__body">
                            <?php if ($r_client) : ?><span class="case-card__client"><?php echo esc_html($r_client); ?></span><?php endif; ?>
                            <h3><?php the_title(); ?></h3>
                        </div>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    set_query_var('cta_title', __('Sizin için de bir çözüm tasarlayalım', 'kaplan'));
    set_query_var('cta_sub', __('Sizin için doğru çözümü birlikte tasarlayalım.', 'kaplan'));
    get_template_part('template-parts/cta-band');
    ?>

<?php endwhile; ?>

<?php get_footer(); ?>
