<?php
/**
 * Tekil post şablonu — blog yazıları için.
 *
 * @package Kaplan
 */

get_header();
?>

<?php while (have_posts()) : the_post(); ?>

    <?php
    set_query_var('hero_title', get_the_title());
    set_query_var('hero_crumb', get_the_title());
    get_template_part('template-parts/page-hero');
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class('section'); ?>>
        <div class="container" style="max-width: 800px;">

            <?php if (has_post_thumbnail()) : ?>
                <div class="video-frame" style="margin-bottom: 2rem; aspect-ratio: 16/9;">
                    <?php the_post_thumbnail('large', ['style' => 'width:100%; height:100%; object-fit:cover;']); ?>
                </div>
            <?php endif; ?>

            <div class="post-meta" style="display:flex; gap:1rem; color:var(--c-muted); font-size:.9rem; margin-bottom:1.5rem;">
                <span><i class="fa-regular fa-calendar"></i> <?php echo esc_html(get_the_date()); ?></span>
                <span><i class="fa-regular fa-user"></i> <?php the_author(); ?></span>
                <?php if (has_category()) : ?>
                    <span><i class="fa-solid fa-folder"></i> <?php the_category(', '); ?></span>
                <?php endif; ?>
            </div>

            <div class="post-content">
                <?php the_content(); ?>
            </div>

            <?php
            wp_link_pages([
                'before' => '<nav class="page-links" style="margin:2rem 0;">' . __('Sayfalar:', 'kaplan'),
                'after'  => '</nav>',
            ]);
            ?>

            <?php if (has_tag()) : ?>
                <div class="post-tags" style="margin-top:2rem;">
                    <?php the_tags('<span class="training-card__chip">', '</span> <span class="training-card__chip">', '</span>'); ?>
                </div>
            <?php endif; ?>

        </div>
    </article>

    <?php
    if (comments_open() || get_comments_number()) {
        echo '<section class="section section--soft"><div class="container" style="max-width:800px;">';
        comments_template();
        echo '</div></section>';
    }
    ?>

<?php endwhile; ?>

<?php get_footer();
