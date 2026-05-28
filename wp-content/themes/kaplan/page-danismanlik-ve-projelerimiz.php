<?php
/**
 * Danışmanlık ve Projelerimiz sayfa template'i.
 *
 * Service grid + process timeline + case-study grid.
 *
 * @package Kaplan
 */

get_header();

$img       = KAPLAN_URI . '/assets/img';
$icon_base = KAPLAN_URI . '/assets/icons';

set_query_var('hero_title', __('<span>Danışmanlık</span> ve proje çözümlerimiz', 'kaplan'));
set_query_var('hero_sub',   __('İş zekası, otomasyon, veri görselleştirme ve süreç geliştirme; uçtan uca danışmanlık ve proje teslimi.', 'kaplan'));
set_query_var('hero_crumb', __('Danışmanlık ve Projeler', 'kaplan'));
set_query_var('hero_bg',    'hero/Resim-5.jpg');
get_template_part('template-parts/page-hero');
?>

<!-- SERVİSLER -->
<section class="section">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Danışmanlık Alanlarımız', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Veriyle yönetmek için her şey', 'kaplan'); ?></h2>
            <p><?php esc_html_e('Müşterilerimizin gerçek ihtiyacına özel; ölçeklenebilir ve sürdürülebilir çözümler tasarlıyoruz.', 'kaplan'); ?></p>
            <span class="section-head__line"></span>
        </header>

        <div class="grid grid-3">
            <article class="service-card">
                <div class="service-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/workspace-icons-04-150x150.png'); ?>" alt="" />
                </div>
                <h3><?php esc_html_e('Veri Görselleştirme', 'kaplan'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Dashboard tasarımı', 'kaplan'); ?></li>
                    <li><?php esc_html_e('1 sayfa rapor konsepti', 'kaplan'); ?></li>
                    <li><?php esc_html_e('Yönetici özet ekranları', 'kaplan'); ?></li>
                </ul>
                <a href="<?php echo esc_url(kpl_localized_url('/is-zekasi-yazilimlari/')); ?>" class="link-arrow"><?php esc_html_e('Detay', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
            </article>

            <article class="service-card service-card--featured">
                <span class="service-card__badge"><?php esc_html_e('Popüler', 'kaplan'); ?></span>
                <div class="service-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/startup-ikon-29-150x150.png'); ?>" alt="" />
                </div>
                <h3><?php esc_html_e('İş Zekası', 'kaplan'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Power BI projeleri', 'kaplan'); ?></li>
                    <li><?php esc_html_e('Web tabanlı raporlama', 'kaplan'); ?></li>
                    <li><?php esc_html_e('Bulut çözümleri', 'kaplan'); ?></li>
                </ul>
                <a href="<?php echo esc_url(kpl_localized_url('/is-zekasi-yazilimlari/')); ?>" class="link-arrow"><?php esc_html_e('Detay', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
            </article>

            <article class="service-card">
                <div class="service-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/workspace-icons-30-150x150.png'); ?>" alt="" />
                </div>
                <h3><?php esc_html_e('Süreç Otomasyonu', 'kaplan'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Robotik Süreç (RPA)', 'kaplan'); ?></li>
                    <li><?php esc_html_e('E-posta otomasyonu', 'kaplan'); ?></li>
                    <li><?php esc_html_e('Veri akışı entegrasyonu', 'kaplan'); ?></li>
                </ul>
                <a href="<?php echo esc_url(kpl_localized_url('/is-zekasi-yazilimlari/')); ?>" class="link-arrow"><?php esc_html_e('Detay', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
            </article>
        </div>
    </div>
</section>

<!-- SÜREÇ -->
<section class="section section--soft">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Çalışma Sürecimiz', 'kaplan'); ?></span>
            <h2><?php esc_html_e('4 adımda projeniz', 'kaplan'); ?></h2>
            <p><?php esc_html_e('Net bir takvim, paylaşılan hedef ve şeffaf raporlama ile.', 'kaplan'); ?></p>
            <span class="section-head__line"></span>
        </header>

        <div class="process-grid">
            <div class="process-step">
                <div class="process-step__circle">01</div>
                <h4><?php esc_html_e('Keşif &amp; Analiz', 'kaplan'); ?></h4>
                <p><?php esc_html_e('İhtiyacın net tanımlanması, mevcut süreçlerin haritalanması ve fırsatların belirlenmesi.', 'kaplan'); ?></p>
            </div>
            <div class="process-step">
                <div class="process-step__circle">02</div>
                <h4><?php esc_html_e('Tasarım', 'kaplan'); ?></h4>
                <p><?php esc_html_e('Çözümün mimarisi, dashboard ve süreç akışlarının taslakları; onay süreci.', 'kaplan'); ?></p>
            </div>
            <div class="process-step">
                <div class="process-step__circle">03</div>
                <h4><?php esc_html_e('Geliştirme', 'kaplan'); ?></h4>
                <p><?php esc_html_e('Veri kaynakları, ETL, görselleştirme katmanı, otomasyonlar ve test.', 'kaplan'); ?></p>
            </div>
            <div class="process-step">
                <div class="process-step__circle">04</div>
                <h4><?php esc_html_e('Devreye Alma', 'kaplan'); ?></h4>
                <p><?php esc_html_e('Yayına alma, kullanıcı eğitimi ve sürekli iyileştirme desteği.', 'kaplan'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- CASE STUDIES -->
<section class="section">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Proje Örnekleri', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Yaptığımız işler', 'kaplan'); ?></h2>
            <span class="section-head__line"></span>
        </header>

        <?php
        set_query_var('case_limit', 4);
        set_query_var('case_show_excerpt', false);
        get_template_part('template-parts/case-grid');
        ?>

        <div style="text-align: center; margin-top: 2.5rem;">
            <a href="<?php echo esc_url(kpl_localized_url('/is-zekasi-yazilimlari/')); ?>" class="btn btn--primary"><?php esc_html_e('Tüm projeleri gör', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<?php
set_query_var('cta_title', __('Projenizi konuşalım', 'kaplan'));
set_query_var('cta_sub',   __('Sizin için doğru çözümü birlikte tasarlayalım.', 'kaplan'));
get_template_part('template-parts/cta-band');

get_footer();
