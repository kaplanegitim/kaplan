<?php
/**
 * Case grid — kpl_case CPT loop.
 *
 * Usage:
 *   set_query_var('case_limit', 4);   // optional, default -1
 *   set_query_var('case_show_excerpt', true); // optional, default true
 *   get_template_part('template-parts/case-grid');
 *
 * @package Kaplan
 */

$limit        = (int) get_query_var('case_limit', -1);
if ($limit === 0) $limit = -1;
$show_excerpt = get_query_var('case_show_excerpt', true);

$case_args = [
    'post_type'      => 'kpl_case',
    'posts_per_page' => $limit,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
];
$q = function_exists('kpl_query_with_lang_fallback')
    ? kpl_query_with_lang_fallback($case_args)
    : new WP_Query($case_args);

if (!$q->have_posts()) return;
$ci = 0;
?>
<div class="case-grid">
    <?php while ($q->have_posts()) : $q->the_post(); $ci++;
        $client = get_post_meta(get_the_ID(), '_kpl_client', true);
        $tags   = get_post_meta(get_the_ID(), '_kpl_tags', true);
        $thumb  = get_the_post_thumbnail_url(get_the_ID(), 'large');
        // Görseli yoksa marka gradient placeholder (indeks bazlı varyant).
        if ($thumb) {
            $media_cls   = '';
            $media_style = ' style="background-image:url(\'' . esc_url($thumb) . '\');"';
        } else {
            $media_cls   = ' case-card__media--ph case-card__media--ph-' . ((($ci - 1) % 3) + 1);
            $media_style = '';
        }
    ?>
        <article class="case-card">
            <div class="case-card__media<?php echo $media_cls; ?>"<?php echo $media_style; ?>>
                <?php if (!$thumb) : ?><i class="fa-solid fa-chart-column case-card__media-icon" aria-hidden="true"></i><?php endif; ?>
                <?php if ($client) : ?>
                    <span class="case-card__client"><?php echo esc_html($client); ?></span>
                <?php endif; ?>
            </div>
            <div class="case-card__body">
                <h3><?php the_title(); ?></h3>
                <?php if ($show_excerpt) : ?>
                    <p style="font-size:.92rem;color:var(--c-muted);"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22)); ?></p>
                <?php endif; ?>
                <?php if ($tags) : ?>
                    <div class="case-card__tags">
                        <?php foreach (preg_split('/\s*,\s*/u', $tags) as $tag) {
                            $tag = trim($tag);
                            if ($tag) echo '<span>' . esc_html($tag) . '</span>';
                        } ?>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    <?php endwhile; wp_reset_postdata(); ?>
</div>
