<?php
/**
 * Topbar (üst dar şerit) — Faz 10'da Customizer'a bağlanacak.
 *
 * @package Kaplan
 */
?>
<div class="topbar">
    <div class="container topbar__inner">
        <div class="topbar__contact">
            <?php $kpl_phone = kaplan_opt('kaplan_phone'); $kpl_email = kaplan_opt('kaplan_email'); ?>
            <?php if ($kpl_phone) : ?>
                <a href="<?php echo esc_attr(kaplan_tel_href($kpl_phone)); ?>"><i class="fa-solid fa-phone"></i> <?php echo esc_html($kpl_phone); ?></a>
            <?php endif; ?>
            <?php if ($kpl_email) : ?>
                <a href="mailto:<?php echo esc_attr($kpl_email); ?>"><i class="fa-solid fa-envelope"></i> <?php echo esc_html($kpl_email); ?></a>
            <?php endif; ?>
        </div>
        <?php set_query_var('social_class', 'topbar__social'); get_template_part('template-parts/social-links'); ?>
    </div>
</div>
