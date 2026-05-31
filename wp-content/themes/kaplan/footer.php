<?php
/**
 * Theme footer — 4-col grid, bottom strip, scroll-top, wp_footer.
 *
 * @package Kaplan
 */
?>
</main><!-- #main-content -->

<footer class="footer">
    <div class="container footer__grid">
        <!-- Brand column -->
        <div class="footer__col">
            <img class="footer__logo" src="<?php echo esc_url(KAPLAN_URI . '/assets/img/logo_k.png'); ?>" alt="<?php bloginfo('name'); ?>" />
            <p><?php esc_html_e('İZLENEBİLİR ve YÖNETİLEBİLİR bir şirkete dönüşme yolculuğunuzda eğitim, danışmanlık ve iş zekası çözümleri ile destek veriyoruz.', 'kaplan'); ?></p>
            <?php set_query_var('social_class', 'footer__social'); get_template_part('template-parts/social-links'); ?>
        </div>

        <!-- Newsletter -->
        <div class="footer__col">
            <h4><?php esc_html_e('Bültene Üye Ol', 'kaplan'); ?></h4>
            <p><?php esc_html_e('Yeni eğitim, makale ve içeriklerden haberdar olun.', 'kaplan'); ?></p>
            <form class="newsletter" onsubmit="event.preventDefault();this.querySelector('.newsletter__msg').textContent='<?php echo esc_js(__('Teşekkürler! Kaydınız alındı.', 'kaplan')); ?>';">
                <input type="email" placeholder="<?php esc_attr_e('E-posta adresiniz', 'kaplan'); ?>" required />
                <button type="submit" aria-label="<?php esc_attr_e('Abone Ol', 'kaplan'); ?>"><i class="fa-solid fa-paper-plane"></i></button>
                <small class="newsletter__msg"></small>
            </form>
        </div>

        <!-- Quick links -->
        <div class="footer__col">
            <h4><?php esc_html_e('Hızlı Bağlantılar', 'kaplan'); ?></h4>
            <?php
            if (has_nav_menu('footer')) {
                wp_nav_menu([
                    'theme_location' => 'footer',
                    'container'      => false,
                    'menu_class'     => 'footer__links',
                    'depth'          => 1,
                    'fallback_cb'    => false,
                ]);
            } else {
                echo '<ul class="footer__links">';
                printf('<li><a href="%s">%s</a></li>', esc_url(admin_url('nav-menus.php')), esc_html__('Footer menüsünü atayın →', 'kaplan'));
                echo '</ul>';
            }
            ?>
        </div>

        <!-- Contact -->
        <div class="footer__col">
            <h4><?php esc_html_e('İletişim', 'kaplan'); ?></h4>
            <ul class="footer__contact">
                <li><i class="fa-solid fa-location-dot"></i> <?php esc_html_e('Varyap Meridian D Blok 68, Ataşehir/İstanbul', 'kaplan'); ?></li>
                <?php $kpl_phone = kaplan_opt('kaplan_phone'); $kpl_email = kaplan_opt('kaplan_email'); ?>
                <?php if ($kpl_phone) : ?><li><i class="fa-solid fa-phone"></i> <?php echo esc_html($kpl_phone); ?></li><?php endif; ?>
                <?php if ($kpl_email) : ?><li><i class="fa-solid fa-envelope"></i> <?php echo esc_html($kpl_email); ?></li><?php endif; ?>
                <li><i class="fa-regular fa-clock"></i> <?php esc_html_e('Hafta içi 09:00 — 18:00', 'kaplan'); ?></li>
            </ul>
        </div>
    </div>

    <div class="footer__bottom">
        <div class="container footer__bottom-inner">
            <span>© <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('Tüm hakları saklıdır.', 'kaplan'); ?></span>
            <?php
            // Gizlilik linki — yalnızca geçerli dildeki sayfa YAYINDA ise göster.
            $kpl_privacy = get_page_by_path('gizlilik-ilkesi');
            if ($kpl_privacy) {
                $kpl_cur = function_exists('pll_current_language') ? pll_current_language() : '';
                $kpl_pid = ($kpl_cur && function_exists('pll_get_post')) ? (pll_get_post($kpl_privacy->ID, $kpl_cur) ?: $kpl_privacy->ID) : $kpl_privacy->ID;
                if (get_post_status($kpl_pid) === 'publish') {
                    printf(
                        '<span><a href="%s">%s</a></span>',
                        esc_url(get_permalink($kpl_pid)),
                        esc_html__('Gizlilik Politikası', 'kaplan')
                    );
                }
            }
            ?>
        </div>
    </div>
</footer>

<a href="#" class="scroll-top" id="scroll-top" aria-label="<?php esc_attr_e('Yukarı', 'kaplan'); ?>">
    <i class="fa-solid fa-arrow-up"></i>
</a>

<?php wp_footer(); ?>
</body>
</html>
