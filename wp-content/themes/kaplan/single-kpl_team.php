<?php
/**
 * Ekip üyesi detay sayfası (single kpl_team).
 *
 * @package Kaplan
 */

get_header();

while (have_posts()) : the_post();
    $id       = get_the_ID();
    $role     = get_post_meta($id, '_kpl_role', true);
    $linkedin = get_post_meta($id, '_kpl_linkedin', true);
    $email    = get_post_meta($id, '_kpl_email', true);
    $thumb    = get_the_post_thumbnail_url($id, 'medium_large');

    set_query_var('hero_title', get_the_title());
    set_query_var('hero_crumb', __('Ekibimiz', 'kaplan'));
    get_template_part('template-parts/page-hero');
    ?>

    <section class="section">
        <div class="container detail detail--team">
            <aside class="detail__aside">
                <div class="team-card__photo team-card__photo--lg">
                    <?php if ($thumb) : ?>
                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                    <?php else : ?>
                        <div style="aspect-ratio:1/1;background:var(--c-bg-soft);display:grid;place-items:center;color:var(--c-muted);font-size:3rem;"><i class="fa-solid fa-user"></i></div>
                    <?php endif; ?>
                </div>
                <div class="detail__team-social">
                    <?php if ($linkedin) : ?>
                        <a class="btn btn--ghost" href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin-in"></i> LinkedIn</a>
                    <?php endif; ?>
                    <?php if ($email) : ?>
                        <a class="btn btn--ghost" href="mailto:<?php echo esc_attr($email); ?>"><i class="fa-solid fa-envelope"></i> <?php esc_html_e('E-posta', 'kaplan'); ?></a>
                    <?php endif; ?>
                </div>
            </aside>

            <div class="detail__main">
                <?php if ($role) : ?>
                    <span class="team-card__role"><?php echo wp_kses_post($role); ?></span>
                <?php endif; ?>
                <div class="detail__content">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </section>

    <?php
    $others = kpl_query_with_lang_fallback([
        'post_type'      => 'kpl_team',
        'posts_per_page' => 4,
        'post__not_in'   => [$id],
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);
    if ($others->have_posts()) : ?>
    <section class="section section--soft">
        <div class="container">
            <header class="section-head">
                <span class="section-head__eyebrow"><?php esc_html_e('Ekibimiz', 'kaplan'); ?></span>
                <h2><?php esc_html_e('Alanında uzman ekibimiz', 'kaplan'); ?></h2>
                <span class="section-head__line"></span>
            </header>
            <div class="team-grid">
                <?php while ($others->have_posts()) : $others->the_post();
                    $o_role  = get_post_meta(get_the_ID(), '_kpl_role', true);
                    $o_thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
                ?>
                    <article class="team-card">
                        <div class="team-card__photo">
                            <?php if ($o_thumb) : ?>
                                <a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($o_thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" /></a>
                            <?php else : ?>
                                <div style="aspect-ratio:1/1;background:var(--c-bg-soft);display:grid;place-items:center;color:var(--c-muted);font-size:3rem;"><i class="fa-solid fa-user"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="team-card__body">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <?php if ($o_role) : ?><span class="team-card__role"><?php echo wp_kses_post($o_role); ?></span><?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    set_query_var('cta_title', __('Birlikte ne yapabileceğimizi konuşalım', 'kaplan'));
    get_template_part('template-parts/cta-band');
    ?>

<?php endwhile; ?>

<?php get_footer(); ?>
