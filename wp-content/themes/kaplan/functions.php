<?php
/**
 * Kaplan Theme bootstrap.
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) {
    exit;
}

define('KAPLAN_VERSION', '0.4.3');
define('KAPLAN_DIR', get_template_directory());
define('KAPLAN_URI', get_template_directory_uri());

require_once KAPLAN_DIR . '/inc/class-walker-nav-menu.php';
require_once KAPLAN_DIR . '/inc/cpt-training.php';
require_once KAPLAN_DIR . '/inc/cpt-case.php';
require_once KAPLAN_DIR . '/inc/cpt-team.php';
require_once KAPLAN_DIR . '/inc/cpt-slide.php';
require_once KAPLAN_DIR . '/inc/cpt-submission.php';
require_once KAPLAN_DIR . '/inc/cpt-client.php';
require_once KAPLAN_DIR . '/inc/form-handler.php';
require_once KAPLAN_DIR . '/inc/polylang.php';
require_once KAPLAN_DIR . '/inc/customizer.php';
require_once KAPLAN_DIR . '/inc/seo.php';
require_once KAPLAN_DIR . '/inc/page-egitimler-meta.php';

/**
 * Theme support flags + i18n.
 */
add_action('after_setup_theme', function () {
    load_theme_textdomain('kaplan', KAPLAN_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // Use our own typography — skip core block defaults.
    // (wp-block-styles intentionally NOT added.)

    add_editor_style(['assets/css/tokens.css', 'assets/css/components.css']);

    register_nav_menus([
        'primary' => __('Ana Menü', 'kaplan'),
        'footer'  => __('Footer Menü', 'kaplan'),
    ]);
});

/**
 * Frontend asset enqueue.
 *
 * Order matters:
 *   1) Google Fonts (Inter + Manrope)
 *   2) Design tokens (CSS custom properties) — must load before style.css
 *   3) Theme stylesheet (declares the theme + minimal reset that references tokens)
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'kaplan-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'kaplan-tokens',
        KAPLAN_URI . '/assets/css/tokens.css',
        [],
        KAPLAN_VERSION
    );

    wp_enqueue_style(
        'kaplan-style',
        get_stylesheet_uri(),
        ['kaplan-tokens'],
        KAPLAN_VERSION
    );

    wp_enqueue_style(
        'kaplan-components',
        KAPLAN_URI . '/assets/css/components.css',
        ['kaplan-tokens', 'kaplan-style'],
        KAPLAN_VERSION
    );

    wp_enqueue_style(
        'kaplan-fa',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        [],
        '6.5.1'
    );

    wp_enqueue_script(
        'kaplan-main',
        KAPLAN_URI . '/assets/js/main.js',
        [],
        KAPLAN_VERSION,
        ['in_footer' => true, 'strategy' => 'defer']
    );
});

/**
 * Preconnect hints for Google Fonts.
 */
add_filter('wp_resource_hints', function ($urls, $relation) {
    if ($relation === 'preconnect') {
        $urls[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin'];
        $urls[] = 'https://fonts.googleapis.com';
    }
    return $urls;
}, 10, 2);
