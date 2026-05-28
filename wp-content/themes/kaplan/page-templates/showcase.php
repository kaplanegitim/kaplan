<?php
/**
 * Template Name: Bileşen Showcase
 *
 * Bu page template, admin'de bir sayfaya atandığında tema bileşenlerini
 * birarada gösterir. Geliştirme / referans amaçlı.
 *
 * @package Kaplan
 */

get_header();
?>

<!-- PREVIEW + BUTTONS + FEATURES -->
<section class="section section--soft">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Bileşen Kütüphanesi', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Kaplan teması bileşenleri', 'kaplan'); ?></h2>
            <p><?php esc_html_e('Tüm component CSS hazır ve design tokens\'a bağlı.', 'kaplan'); ?></p>
            <span class="section-head__line"></span>
        </header>

        <div style="display:flex; gap:.8rem; justify-content:center; flex-wrap:wrap; margin-bottom:3rem;">
            <a href="#" class="btn btn--primary"><?php esc_html_e('Primary Buton', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
            <a href="#" class="btn btn--ghost"><?php esc_html_e('Ghost Buton', 'kaplan'); ?></a>
        </div>

        <?php $icon_base = KAPLAN_URI . '/assets/icons'; ?>
        <div class="grid grid-4">
            <article class="feature-card">
                <div class="feature-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/ikonlar-site-için-08-125x125.png'); ?>" alt="" />
                </div>
                <h4><?php esc_html_e('Büyük Resmi Görebilmek', 'kaplan'); ?></h4>
                <p><?php esc_html_e('Eğitim ve iş zekası çözümleri ile şirketlere destek.', 'kaplan'); ?></p>
            </article>
            <article class="feature-card">
                <div class="feature-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/ikonlar-site-için-07-125x125.png'); ?>" alt="" />
                </div>
                <h4><?php esc_html_e('1 Sayfa Konsepti', 'kaplan'); ?></h4>
                <p><?php esc_html_e('Veri görselleştirme ve infografik ile kompakt sunum.', 'kaplan'); ?></p>
            </article>
            <article class="feature-card">
                <div class="feature-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/ikonlar-site-için-06-125x125.png'); ?>" alt="" />
                </div>
                <h4><?php esc_html_e('Otomasyon', 'kaplan'); ?></h4>
                <p><?php esc_html_e('Analizler tek tuşla otomatik hazırlanır, e-posta gönderilir.', 'kaplan'); ?></p>
            </article>
            <article class="feature-card">
                <div class="feature-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/ikonlar-site-için-05-125x125.png'); ?>" alt="" />
                </div>
                <h4><?php esc_html_e('Sayılarla Şirket', 'kaplan'); ?></h4>
                <p><?php esc_html_e('Bütçeleme, planlama ve karar destek sistemleri.', 'kaplan'); ?></p>
            </article>
        </div>
    </div>
</section>

<!-- VALUE CARDS -->
<section class="section">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow">Value Cards</span>
            <h2><?php esc_html_e('Numaralı değer kartları', 'kaplan'); ?></h2>
            <span class="section-head__line"></span>
        </header>
        <div class="value-grid">
            <article class="value-card"><div class="value-card__num">01</div><h3>Kolay Anlaşılır</h3><p>Verilerin karar süreçlerine etkili şekilde dahil edilmesi.</p></article>
            <article class="value-card"><div class="value-card__num">02</div><h3>Analitik</h3><p>Verileri kolayca anlamanız ve analitik karar süreçleri.</p></article>
            <article class="value-card"><div class="value-card__num">03</div><h3>Fark Yaratan</h3><p>Zaman alan işleri otomatik hale getirme.</p></article>
            <article class="value-card"><div class="value-card__num">04</div><h3>Sıradışı</h3><p>Farkındalık, sürekli gelişim ve sıra dışı çözümler.</p></article>
        </div>
    </div>
</section>

<!-- STATS -->
<section class="stats">
    <div class="container">
        <div class="grid grid-4">
            <div class="stat">
                <div class="stat__icon"><i class="fa-solid fa-diagram-project"></i></div>
                <div class="stat__number" data-target="42">42</div>
                <div class="stat__label">Proje</div>
            </div>
            <div class="stat">
                <div class="stat__icon"><i class="fa-solid fa-handshake"></i></div>
                <div class="stat__number" data-target="28">28</div>
                <div class="stat__label">Müşteri</div>
            </div>
            <div class="stat">
                <div class="stat__icon"><i class="fa-solid fa-book-open"></i></div>
                <div class="stat__number" data-target="6">6</div>
                <div class="stat__label">Gelişim Seti</div>
            </div>
            <div class="stat">
                <div class="stat__icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                <div class="stat__number" data-target="20">20</div>
                <div class="stat__label">Eğitim Programı</div>
            </div>
        </div>
    </div>
</section>

<!-- SERVICE CARDS -->
<section class="section section--soft">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Hizmetlerimiz', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Service cards', 'kaplan'); ?></h2>
            <span class="section-head__line"></span>
        </header>
        <div class="grid grid-3">
            <article class="service-card">
                <div class="service-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/workspace-icons-04-150x150.png'); ?>" alt="" />
                </div>
                <h3>Eğitim</h3>
                <ul><li>Verilerle Yönetim</li><li>Yeni Mezun Yaşam Kiti</li><li>Yöneticinin Yol Haritası</li></ul>
                <a href="#" class="link-arrow"><?php esc_html_e('Detay', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
            </article>
            <article class="service-card service-card--featured">
                <span class="service-card__badge"><?php esc_html_e('Popüler', 'kaplan'); ?></span>
                <div class="service-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/startup-ikon-29-150x150.png'); ?>" alt="" />
                </div>
                <h3>Danışmanlık</h3>
                <ul><li>Veri Görselleştirme</li><li>Entegre İş Planlama</li><li>Excel Koçluğu</li></ul>
                <a href="#" class="link-arrow"><?php esc_html_e('Detay', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
            </article>
            <article class="service-card">
                <div class="service-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/workspace-icons-30-150x150.png'); ?>" alt="" />
                </div>
                <h3>Proje Çözümleri</h3>
                <ul><li>İş Zekası</li><li>Otomasyon</li><li>Robotik Süreç (RPA)</li></ul>
                <a href="#" class="link-arrow"><?php esc_html_e('Detay', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
            </article>
        </div>
    </div>
</section>

<?php
set_query_var('cta_title', __('Sonraki adımı birlikte planlayalım', 'kaplan'));
set_query_var('cta_sub',   __('Eğitim, danışmanlık ya da iş zekası — size en uygun çözümü konuşalım.', 'kaplan'));
get_template_part('template-parts/cta-band');

get_footer();
