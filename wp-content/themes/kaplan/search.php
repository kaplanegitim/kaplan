<?php
/**
 * Arama sonuçları.
 *
 * @package Kaplan
 */

get_header();

set_query_var('hero_title', sprintf(__('"<span>%s</span>" için sonuçlar', 'kaplan'), esc_html(get_search_query())));
set_query_var('hero_crumb', __('Arama', 'kaplan'));
get_template_part('template-parts/page-hero');
?>

<section class="section">
    <div class="container">
        <div style="max-width:600px; margin:0 auto 2.5rem;">
            <?php get_search_form(); ?>
        </div>

        <?php if (have_posts()) : ?>
            <div class="grid grid-3">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('feature-card'); ?> style="text-align:left;">
                        <h4>
                            <a href="<?php the_permalink(); ?>" style="color:var(--c-ink);"><?php the_title(); ?></a>
                        </h4>
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22)); ?></p>
                        <a href="<?php the_permalink(); ?>" class="link-arrow"><?php esc_html_e('Aç', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
                    </article>
                <?php endwhile; ?>
            </div>

            <div style="text-align:center; margin-top:3rem;">
                <?php the_posts_pagination(); ?>
            </div>
        <?php else : ?>
            <p style="text-align:center; color:var(--c-muted);">
                <?php esc_html_e('Hiçbir sonuç bulunamadı. Farklı bir kelimeyle deneyebilirsiniz.', 'kaplan'); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer();
