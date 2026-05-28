<?php
/**
 * Ana index.php — blog/listing fallback.
 *
 * WP template hierarchy'nin en alttaki dosyası. Daha spesifik bir template
 * eşleşmediğinde devreye girer (örn. home / fallback / no-match-elsewhere).
 *
 * @package Kaplan
 */

get_header();

if (is_home() && !is_front_page()) {
    $hero_title = single_post_title('', false) ?: __('Blog', 'kaplan');
} else {
    $hero_title = __('Yazılar', 'kaplan');
}

set_query_var('hero_title', $hero_title);
set_query_var('hero_crumb', $hero_title);
get_template_part('template-parts/page-hero');
?>

<section class="section">
    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="grid grid-3">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('feature-card'); ?> style="text-align:left;">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" style="display:block; aspect-ratio:16/9; overflow:hidden; border-radius:var(--r-md); margin-bottom:1rem;">
                                <?php the_post_thumbnail('medium_large', ['style' => 'width:100%; height:100%; object-fit:cover;']); ?>
                            </a>
                        <?php endif; ?>
                        <h4>
                            <a href="<?php the_permalink(); ?>" style="color:var(--c-ink);"><?php the_title(); ?></a>
                        </h4>
                        <p style="font-size:.85rem; color:var(--c-muted); margin-bottom:.6rem;">
                            <i class="fa-regular fa-calendar"></i> <?php echo esc_html(get_the_date()); ?>
                        </p>
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?></p>
                        <a href="<?php the_permalink(); ?>" class="link-arrow"><?php esc_html_e('Devamını oku', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
                    </article>
                <?php endwhile; ?>
            </div>

            <div style="text-align:center; margin-top:3rem;">
                <?php
                the_posts_pagination([
                    'prev_text' => '<i class="fa-solid fa-arrow-left"></i> ' . __('Önceki', 'kaplan'),
                    'next_text' => __('Sonraki', 'kaplan') . ' <i class="fa-solid fa-arrow-right"></i>',
                ]);
                ?>
            </div>
        <?php else : ?>
            <p style="text-align:center; color:var(--c-muted);">
                <?php esc_html_e('Henüz yazı yok. Admin panelinden ilk yazınızı ekleyebilirsiniz.', 'kaplan'); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer();
