<?php
/**
 * Team grid — kpl_team CPT loop.
 *
 * Usage:
 *   set_query_var('team_limit', 4);   // optional, default -1 (tümü)
 *   get_template_part('template-parts/team-grid');
 *
 * @package Kaplan
 */

$limit = (int) get_query_var('team_limit', -1);
if ($limit === 0) $limit = -1;

$q = function_exists('kpl_query_with_lang_fallback')
    ? kpl_query_with_lang_fallback([
        'post_type'      => 'kpl_team',
        'posts_per_page' => $limit,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ])
    : new WP_Query([
        'post_type'      => 'kpl_team',
        'posts_per_page' => $limit,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);

if (!$q->have_posts()) return;
?>
<div class="team-grid">
    <?php while ($q->have_posts()) : $q->the_post();
        $role     = get_post_meta(get_the_ID(), '_kpl_role', true);
        $linkedin = get_post_meta(get_the_ID(), '_kpl_linkedin', true);
        $email    = get_post_meta(get_the_ID(), '_kpl_email', true);
        $thumb    = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
    ?>
        <article class="team-card">
            <div class="team-card__photo">
                <?php if ($thumb) : ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" decoding="async" />
                <?php else : ?>
                    <div style="aspect-ratio:1/1;background:var(--c-bg-soft);display:grid;place-items:center;color:var(--c-muted);font-size:3rem;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                <?php endif; ?>
                <div class="team-card__social">
                    <a href="<?php echo esc_url($linkedin ?: '#'); ?>" aria-label="LinkedIn" <?php echo $linkedin ? 'target="_blank" rel="noopener"' : ''; ?>><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="<?php echo $email ? 'mailto:' . esc_attr($email) : '#'; ?>" aria-label="Mail"><i class="fa-solid fa-envelope"></i></a>
                </div>
            </div>
            <div class="team-card__body">
                <h3><?php the_title(); ?></h3>
                <?php if ($role) : ?>
                    <span class="team-card__role"><?php echo wp_kses_post($role); ?></span>
                <?php endif; ?>
                <p><?php echo wp_kses_post(wp_trim_words(get_the_content(), 32)); ?></p>
            </div>
        </article>
    <?php endwhile; wp_reset_postdata(); ?>
</div>
