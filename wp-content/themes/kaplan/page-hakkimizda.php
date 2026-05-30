<?php
/**
 * Hakkımızda sayfa template'i (slug bazlı otomatik eşleşir).
 *
 * @package Kaplan
 */

get_header();

$img = KAPLAN_URI . '/assets/img';

set_query_var('hero_title', __('Büyük resmi <span>görebilmek</span> için yanınızdayız', 'kaplan'));
set_query_var('hero_sub',   __('Veri yönetimi, iş geliştirme, planlama ve liderlik alanlarında sürekli öğrenmeyi ilke edinmiş; dinamik, inovatif ve yaratıcı bir ekibiz.', 'kaplan'));
set_query_var('hero_crumb', __('Hakkımızda', 'kaplan'));
set_query_var('hero_bg',    'hero/Resim-6.jpg');
get_template_part('template-parts/page-hero');
?>

<!-- BIZ KIMIZ -->
<section class="section">
    <div class="container">
        <div class="about-intro__grid">
            <div class="about-intro__media about-intro__media--ph about-intro__media--ph-3">
                <i class="fa-solid fa-people-group" aria-hidden="true"></i>
            </div>
            <div class="about-intro__text">
                <span class="section-head__eyebrow"><?php esc_html_e('Biz Kimiz?', 'kaplan'); ?></span>
                <h2><?php esc_html_e('Şirketleri', 'kaplan'); ?> <span class="accent"><?php esc_html_e('öğrenen organizasyonlara', 'kaplan'); ?></span> <?php esc_html_e('dönüştürüyoruz.', 'kaplan'); ?></h2>
                <p><?php esc_html_e('Veri yönetimi, iş geliştirme, planlama, organizasyon, yönetim ve liderlik alanlarında sürekli öğrenmeyi ve öğretmeyi ilke edinmiş; süreçlerin verimliliğini arttırmaya yönelik otomasyonlar geliştiren; dinamik, inovatif ve yaratıcı bir ekibiz.', 'kaplan'); ?></p>
                <p><?php esc_html_e('Tecrübemiz ve alanlarında yetkin ekip arkadaşlarımızla, «Büyük Resmi Görebilmek» için çıkacağınız yolda sizin ve şirketinizin danışmanlığını yapmak, eğitim ve projelerimizle destek vermek için hizmetinizdeyiz.', 'kaplan'); ?></p>
                <ul class="check-list">
                    <li><i class="fa-solid fa-check"></i> <?php echo esc_html(sprintf(__('%s+ yıl sektörel deneyim', 'kaplan'), kaplan_opt('kaplan_stat_years'))); ?></li>
                    <li><i class="fa-solid fa-check"></i> <?php echo esc_html(sprintf(__('%s+ kurumsal müşteri', 'kaplan'), kaplan_opt('kaplan_stat_clients'))); ?></li>
                    <li><i class="fa-solid fa-check"></i> <?php echo esc_html(sprintf(__('%s+ tamamlanmış proje', 'kaplan'), kaplan_opt('kaplan_stat_projects'))); ?></li>
                    <li><i class="fa-solid fa-check"></i> <?php echo esc_html(sprintf(__('%s+ aktif eğitim programı', 'kaplan'), kaplan_opt('kaplan_stat_programs'))); ?></li>
                </ul>
                <a href="<?php echo esc_url(kpl_localized_url('/iletisim/')); ?>" class="btn btn--primary">
                    <?php esc_html_e('Bizimle iletişime geçin', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- SERVISLERIMIZ -->
<section class="section section--soft">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Servislerimiz', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Veriyle çalışan herkes için', 'kaplan'); ?></h2>
            <p><?php esc_html_e('Karar süreçlerinizden raporlarınıza, otomasyondan görselleştirmeye — uçtan uca destek.', 'kaplan'); ?></p>
            <span class="section-head__line"></span>
        </header>

        <div class="value-grid">
            <article class="value-card">
                <div class="value-card__num">01</div>
                <h3><?php esc_html_e('Kolay Anlaşılır', 'kaplan'); ?></h3>
                <p><?php esc_html_e('Pazarda ve şirket içinde üretilen verilerin karar süreçlerinize etkili şekilde dahil edilmesini, ekiplerinizin işlerini hangi verilerle nasıl yöneteceklerini netleştirir.', 'kaplan'); ?></p>
            </article>
            <article class="value-card">
                <div class="value-card__num">02</div>
                <h3><?php esc_html_e('Analitik', 'kaplan'); ?></h3>
                <p><?php esc_html_e('Verinin artan önemi ile dünyayı yönlendirdiği günümüzde, verilerinizi kolayca anlamanızda ve karar süreçlerinizi analitik hale getirmenizde destek oluyoruz.', 'kaplan'); ?></p>
            </article>
            <article class="value-card">
                <div class="value-card__num">03</div>
                <h3><?php esc_html_e('Fark Yaratan', 'kaplan'); ?></h3>
                <p><?php esc_html_e('Kontrol ihtiyacı olan, zaman alan işlerinizi otomatik hale getirir; 50 sayfalık raporlar yerine bir bakışta anlaşılan, fark yaratan çözümler üretiriz.', 'kaplan'); ?></p>
            </article>
            <article class="value-card">
                <div class="value-card__num">04</div>
                <h3><?php esc_html_e('Sıradışı Çözümler', 'kaplan'); ?></h3>
                <p><?php esc_html_e('Öğrenen organizasyonlar yaratmak isteyen farklı sektörlerden şirketlere; farkındalık, sürekli gelişim ve sıra dışı çözümler ile hizmet veriyoruz.', 'kaplan'); ?></p>
            </article>
        </div>
    </div>
</section>

<!-- EKIBIMIZ -->
<section class="section">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Ekibimiz', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Alanında uzman ekibimiz', 'kaplan'); ?></h2>
            <p><?php esc_html_e('Farklı sektör deneyimlerine sahip, alanında uzman ekip üyelerimiz.', 'kaplan'); ?></p>
            <span class="section-head__line"></span>
        </header>

        <?php get_template_part('template-parts/team-grid'); ?>
    </div>
</section>

<?php
set_query_var('cta_title', __('Birlikte ne yapabileceğimizi konuşalım', 'kaplan'));
set_query_var('cta_sub',   __('Eğitim, danışmanlık ya da iş zekası — size uygun çözümü beraber planlayalım.', 'kaplan'));
get_template_part('template-parts/cta-band');

get_footer();
