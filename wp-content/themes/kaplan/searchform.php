<?php
/**
 * Arama formu — Kaplan stilinde, newsletter-like pill input.
 *
 * @package Kaplan
 */
?>
<form role="search" method="get" class="newsletter" style="background: var(--c-bg-soft); border: 1px solid var(--c-line);" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="screen-reader-text" for="search-<?php echo esc_attr(uniqid()); ?>"><?php esc_html_e('Ara:', 'kaplan'); ?></label>
    <input
        type="search"
        id="search-<?php echo esc_attr(uniqid()); ?>"
        name="s"
        value="<?php echo esc_attr(get_search_query()); ?>"
        placeholder="<?php esc_attr_e('Sitede ara...', 'kaplan'); ?>"
        style="color: var(--c-ink);"
        required
    />
    <button type="submit" aria-label="<?php esc_attr_e('Ara', 'kaplan'); ?>">
        <i class="fa-solid fa-magnifying-glass"></i>
    </button>
</form>
