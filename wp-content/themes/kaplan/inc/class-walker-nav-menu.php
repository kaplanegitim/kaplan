<?php
/**
 * Custom nav menu walker — outputs the demo prototype's BEM markup.
 *
 *   <ul class="nav__list">
 *     <li><a class="active" href="...">Anasayfa</a></li>
 *     <li class="has-sub">
 *       <a href="...">Eğitimler <i class="fa-solid fa-chevron-down"></i></a>
 *       <ul class="submenu">
 *         <li><a href="...">...</a></li>
 *       </ul>
 *     </li>
 *   </ul>
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kaplan_Walker_Nav_Menu extends Walker_Nav_Menu {

    public function start_lvl(&$output, $depth = 0, $args = null) {
        $output .= "\n<ul class=\"submenu\">\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= "</ul>\n";
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes      = (array) ($item->classes ?? []);
        $has_children = in_array('menu-item-has-children', $classes, true);
        $is_current   = in_array('current-menu-item', $classes, true)
                     || in_array('current_page_item', $classes, true);

        // Polylang dil switcher item'ı — .lang pill olarak render et
        $is_lang = false;
        foreach ($classes as $c) {
            if (is_string($c) && (strpos($c, 'lang-item') !== false || strpos($c, 'pll-parent-menu-item') !== false)) {
                $is_lang = true;
                break;
            }
        }

        if ($is_lang) {
            $li_class = ' class="lang"';
        } else {
            $li_class = $has_children ? ' class="has-sub"' : '';
        }
        $output  .= '<li' . $li_class . '>';

        $title    = apply_filters('the_title', $item->title, $item->ID);
        $url      = !empty($item->url) ? esc_url($item->url) : '#';
        $a_class  = $is_current ? ' class="active"' : '';
        $a_target = !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $a_rel    = !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';

        $output  .= '<a href="' . $url . '"' . $a_class . $a_target . $a_rel . '>' . esc_html($title);
        if ($has_children && !$is_lang) {
            $output .= ' <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>';
        }
        $output  .= '</a>';
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}
