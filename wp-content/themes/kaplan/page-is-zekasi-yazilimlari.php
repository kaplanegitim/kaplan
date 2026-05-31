<?php
/**
 * İş Zekası Yazılımları — kpl_case CPT'sinden beslenir.
 *
 * @package Kaplan
 */

get_header();

set_query_var('hero_title', __('<span>İş Zekası</span> projelerimiz', 'kaplan'));
set_query_var('hero_sub',   __('Power BI, web tabanlı çözümler ve özel raporlama otomasyonlarıyla müşterilerimizin karar süreçlerini hızlandırıyoruz.', 'kaplan'));
set_query_var('hero_crumb', __('İş Zekası Yazılımları', 'kaplan'));
set_query_var('hero_bg',    'hero/Resim-5.jpg');
get_template_part('template-parts/page-hero');

?>

<section class="section">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Proje Vitrini', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Tamamladığımız iş zekası projeleri', 'kaplan'); ?></h2>
            <span class="section-head__line"></span>
        </header>

        <?php get_template_part('template-parts/case-grid'); ?>
    </div>
</section>

<!-- TEKNOLOJİLER -->
<section class="section section--soft">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Yetkinliklerimiz', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Çalıştığımız teknolojiler', 'kaplan'); ?></h2>
            <span class="section-head__line"></span>
        </header>

        <div class="feature-list">
            <div class="feature-list__item">
                <div class="feature-list__icon"><i class="fa-solid fa-chart-pie"></i></div>
                <div><h4>Microsoft Power BI</h4><p><?php esc_html_e('Desktop, Service ve Embedded çözümler; DAX, Power Query, paylaşım yönetimi.', 'kaplan'); ?></p></div>
            </div>
            <div class="feature-list__item">
                <div class="feature-list__icon"><i class="fa-brands fa-microsoft"></i></div>
                <div><h4>Excel &amp; VBA</h4><p><?php esc_html_e('Otomatize raporlama, makro tabanlı süreç hızlandırma, Power Query.', 'kaplan'); ?></p></div>
            </div>
            <div class="feature-list__item">
                <div class="feature-list__icon"><i class="fa-solid fa-cloud"></i></div>
                <div><h4><?php esc_html_e('Bulut Çözümleri', 'kaplan'); ?></h4><p><?php esc_html_e('Azure, Google Cloud üzerinde veri saklama ve raporlama mimarileri.', 'kaplan'); ?></p></div>
            </div>
            <div class="feature-list__item">
                <div class="feature-list__icon"><i class="fa-solid fa-database"></i></div>
                <div><h4>SQL &amp; ETL</h4><p><?php esc_html_e('Çoklu kaynaktan veri çekme, dönüştürme, tek yere yükleme.', 'kaplan'); ?></p></div>
            </div>
            <div class="feature-list__item">
                <div class="feature-list__icon"><i class="fa-solid fa-robot"></i></div>
                <div><h4><?php esc_html_e('Yapay Zeka Araçları', 'kaplan'); ?></h4><p><?php esc_html_e('UiPath, Power Automate ile tekrarlanan işlerin otomatize edilmesi.', 'kaplan'); ?></p></div>
            </div>
            <div class="feature-list__item">
                <div class="feature-list__icon"><i class="fa-solid fa-globe"></i></div>
                <div><h4><?php esc_html_e('Web Tabanlı Raporlama', 'kaplan'); ?></h4><p><?php esc_html_e('Tarayıcıdan erişilebilen, yetkilendirme yönetimli kontrol panelleri.', 'kaplan'); ?></p></div>
            </div>
        </div>
    </div>
</section>

<?php
set_query_var('cta_title', __('Sizin için de bir çözüm tasarlayalım', 'kaplan'));
set_query_var('cta_sub',   __('Süreçlerinize uygun iş zekası mimarisini birlikte planlayalım.', 'kaplan'));
get_template_part('template-parts/cta-band');

get_footer();
