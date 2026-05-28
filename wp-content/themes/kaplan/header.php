<?php
/**
 * Theme header — DOCTYPE, <head>, topbar, sticky header/nav.
 *
 * @package Kaplan
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php
    // Favicon fallback — Site Icon set edilmediyse tema varsayılanını ver.
    if ( ! has_site_icon() ) {
        printf('<link rel="icon" href="%s" />' . "\n", esc_url(KAPLAN_URI . '/assets/img/logo_k.png'));
    }
    ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="screen-reader-text" href="#main-content" style="position:absolute; left:-9999px;">
    <?php esc_html_e('İçeriğe geç', 'kaplan'); ?>
</a>

<?php get_template_part('template-parts/topbar'); ?>

<header class="header" id="site-header">
    <div class="container header__inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="brand" rel="home">
            <?php
            $custom_logo_id = get_theme_mod('custom_logo');
            if ($custom_logo_id) {
                $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                if ($logo) {
                    printf('<img src="%s" alt="%s" />', esc_url($logo[0]), esc_attr(get_bloginfo('name')));
                }
            } else {
                printf('<img src="%s" alt="%s" />', esc_url(KAPLAN_URI . '/assets/img/logo_k.png'), esc_attr(get_bloginfo('name')));
            }
            ?>
        </a>

        <nav class="nav" id="primary-nav" aria-label="<?php esc_attr_e('Ana menü', 'kaplan'); ?>">
            <?php
            if (has_nav_menu('primary')) {
                // Menü walker'ı kendi içinde lang-item olan menu item'larını .lang olarak render eder.
                // Polylang menüde "Language switcher" item'ı eklenmemişse, aşağıdaki otomatik fallback devreye girer.
                wp_nav_menu([
                    'theme_location'  => 'primary',
                    'container'       => false,
                    'menu_class'      => 'nav__list',
                    'menu_id'         => '',
                    'walker'          => new Kaplan_Walker_Nav_Menu(),
                    'fallback_cb'     => false,
                    'depth'           => 2,
                    'items_wrap'      => '<ul class="%2$s">%3$s' . kpl_lang_switcher_items() . '</ul>',
                ]);
            } else {
                echo '<ul class="nav__list">';
                printf(
                    '<li><a href="%s">%s</a></li>',
                    esc_url(admin_url('nav-menus.php')),
                    esc_html__('Ana menüyü atayın →', 'kaplan')
                );
                echo kpl_lang_switcher_items();
                echo '</ul>';
            }
            ?>
        </nav>

        <button class="nav-toggle" id="nav-toggle" aria-label="<?php esc_attr_e('Menüyü aç/kapat', 'kaplan'); ?>" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<main id="main-content">
