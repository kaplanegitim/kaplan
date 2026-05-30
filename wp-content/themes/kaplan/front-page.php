<?php
/**
 * Anasayfa şablonu (front-page.php).
 *
 * Demo'nun index.html'inin tam karşılığı:
 *   Hero slider → Features → TEGEP video → Portfolio → Split (video + intro)
 *   → Stats → Hizmetler → Ekip → Clients marquee → CTA
 *
 * Faz 10'da Customizer'a bağlanacak (hero slides, team members, clients).
 *
 * @package Kaplan
 */

get_header();

$img       = KAPLAN_URI . '/assets/img';
$icon_base = KAPLAN_URI . '/assets/icons';
?>

<?php
// ===== HERO SLIDER (kpl_slide CPT'den) =====
$slides_q = kpl_query_with_lang_fallback([
    'post_type'      => 'kpl_slide',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
]);
if ($slides_q->have_posts()) :
    $slide_count = $slides_q->post_count;

    // SEO: inline style= attribute'larından kaçınmak için arka planları
    // önceden tek bir <style> bloğuna topla; her slayt benzersiz bir sınıf alır.
    $hero_bgs = [];
    $pre_idx  = 0;
    while ($slides_q->have_posts()) : $slides_q->the_post(); $pre_idx++;
        $thumb = get_the_post_thumbnail_url(get_the_ID(), 'full');
        if ($thumb) $hero_bgs[$pre_idx] = esc_url($thumb);
    endwhile;
    $slides_q->rewind_posts();

    if ($hero_bgs) {
        echo "<style>";
        foreach ($hero_bgs as $i => $u) {
            echo ".hero__slide--bg{$i}{background-image:url('{$u}');}";
        }
        echo "</style>\n";
    }
?>
<section class="hero">
    <div class="hero__slides">
        <?php $idx = 0; while ($slides_q->have_posts()) : $slides_q->the_post(); $idx++;
            $eyebrow    = get_post_meta(get_the_ID(), '_kpl_eyebrow', true);
            $accent     = get_post_meta(get_the_ID(), '_kpl_title_accent', true);
            $cta1_label = get_post_meta(get_the_ID(), '_kpl_cta1_label', true);
            $cta1_url   = get_post_meta(get_the_ID(), '_kpl_cta1_url', true);
            $cta2_label = get_post_meta(get_the_ID(), '_kpl_cta2_label', true);
            $cta2_url   = get_post_meta(get_the_ID(), '_kpl_cta2_url', true);
            $thumb      = get_the_post_thumbnail_url(get_the_ID(), 'full');
            $active     = $idx === 1 ? ' is-active' : '';
            // Görseli olmayan slide → marka gradient placeholder (her biri farklı varyant).
            if ($thumb) {
                $slide_cls = ' hero__slide--bg' . $idx; // arka plan üstteki <style> bloğunda.
            } else {
                $slide_cls = ' hero__slide--ph hero__slide--ph-' . ((($idx - 1) % 3) + 1);
            }
            // SEO: sayfa başına tek H1 — ilk slayt h1, diğerleri h2 (görsel olarak aynı, CSS .hero__title üzerinden).
            $htag = $idx === 1 ? 'h1' : 'h2';
        ?>
        <div class="hero__slide<?php echo $active . $slide_cls; ?>">
            <div class="hero__overlay"></div>
            <div class="container hero__content">
                <?php if ($eyebrow) : ?>
                    <span class="hero__eyebrow"><?php echo esc_html($eyebrow); ?></span>
                <?php endif; ?>
                <<?php echo $htag; ?> class="hero__title">
                    <?php echo esc_html(get_the_title()); ?>
                    <?php if ($accent) : ?>
                        <span><?php echo esc_html($accent); ?></span>
                    <?php endif; ?>
                </<?php echo $htag; ?>>
                <?php if (has_excerpt()) : ?>
                    <p class="hero__sub"><?php echo esc_html(get_the_excerpt()); ?></p>
                <?php endif; ?>
                <?php if ($cta1_label || $cta2_label) : ?>
                    <div class="hero__actions">
                        <?php if ($cta1_label) : ?>
                            <a class="btn btn--primary" href="<?php echo esc_url(kpl_slide_cta_href($cta1_url)); ?>"<?php echo kpl_slide_cta_external($cta1_url) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html($cta1_label); ?></a>
                        <?php endif; ?>
                        <?php if ($cta2_label) : ?>
                            <a class="btn btn--ghost" href="<?php echo esc_url(kpl_slide_cta_href($cta2_url)); ?>"<?php echo kpl_slide_cta_external($cta2_url) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html($cta2_label); ?></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php if ($slide_count > 1) : ?>
    <div class="hero__dots" id="hero-dots">
        <?php for ($i = 1; $i <= $slide_count; $i++) : ?>
            <button<?php echo $i === 1 ? ' class="is-active"' : ''; ?> aria-label="<?php echo esc_attr($i); ?>"></button>
        <?php endfor; ?>
    </div>
    <button class="hero__arrow hero__arrow--prev" id="hero-prev" aria-label="<?php esc_attr_e('Önceki', 'kaplan'); ?>"><i class="fa-solid fa-chevron-left"></i></button>
    <button class="hero__arrow hero__arrow--next" id="hero-next" aria-label="<?php esc_attr_e('Sonraki', 'kaplan'); ?>"><i class="fa-solid fa-chevron-right"></i></button>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- ===== FEATURES (4 col icons) ===== -->
<section class="section features">
    <div class="container">
        <div class="grid grid-4">
            <article class="feature-card">
                <div class="feature-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/ikonlar-site-için-08-125x125.png'); ?>" alt="" />
                </div>
                <h4><?php esc_html_e('Büyük Resmi Görebilmek', 'kaplan'); ?></h4>
                <p><?php esc_html_e('Yeni Mezun, Yönetici Geliştirme eğitim programları ve iş zekası çözümleri ile "Büyük Resmi Görebilmek" konusunda şirketlere destek veriyoruz.', 'kaplan'); ?></p>
            </article>
            <article class="feature-card">
                <div class="feature-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/ikonlar-site-için-07-125x125.png'); ?>" alt="" />
                </div>
                <h4><?php esc_html_e('1 Sayfa Konsepti', 'kaplan'); ?></h4>
                <p><?php esc_html_e('Veri görselleştirme, yalın anlatım, infografik ve kontrol panelleri ile süreçleri 1 sayfa konseptinde sunuyoruz.', 'kaplan'); ?></p>
            </article>
            <article class="feature-card">
                <div class="feature-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/ikonlar-site-için-06-125x125.png'); ?>" alt="" />
                </div>
                <h4><?php esc_html_e('Otomasyon', 'kaplan'); ?></h4>
                <p><?php esc_html_e('Analiz, yorumlama, sunum ve tahminleri tek tuş ile otomatik hazırlıyor; istenilen kişilere e-posta ile gönderiyoruz.', 'kaplan'); ?></p>
            </article>
            <article class="feature-card">
                <div class="feature-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/ikonlar-site-için-05-125x125.png'); ?>" alt="" />
                </div>
                <h4><?php esc_html_e('Sayılarla Şirket Yönetimi', 'kaplan'); ?></h4>
                <p><?php esc_html_e('Bütçeleme, talep/tüketim planlama, veri analizi ve karar destek sistemleri ile izlenebilir, yönetilebilir şirketlere dönüşüm.', 'kaplan'); ?></p>
            </article>
        </div>
    </div>
</section>

<!-- ===== TEGEP PROMO VIDEO ===== -->
<section class="section section--soft">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('TEGEP Zirvesi 2019', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Akış &amp; Oyun — Can Kaplan', 'kaplan'); ?></h2>
            <p><?php esc_html_e('TEGEP 9. Eğitim ve Gelişim Zirvesi Konuşmacı Röportajları', 'kaplan'); ?></p>
            <span class="section-head__line"></span>
        </header>
        <div class="video-frame">
            <iframe loading="lazy" src="https://www.youtube.com/embed/Eb23Wi59PSg" title="TEGEP" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</section>

<!-- ===== PORTFOLYO ===== -->
<section class="section" id="portfolyo">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Portfolyomuz', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Eğitim · Danışmanlık · İş Zekası', 'kaplan'); ?></h2>
            <span class="section-head__line"></span>
        </header>

        <div class="portfolio-grid">
            <?php
            $portfolio = [
                ['img' => 'Resim-4.jpg', 'title' => __('Gelişim Kitapları', 'kaplan'),                  'link' => '/egitimler/'],
                ['img' => 'Resim-7.jpg', 'title' => __('Veri Görselleştirme &amp; İnfografik', 'kaplan'), 'link' => '/infografik-ve-sunum/'],
                ['img' => 'Resim-5.jpg', 'title' => __('Proje Geliştirme &amp; Danışmanlık', 'kaplan'),  'link' => '/danismanlik-ve-projelerimiz/'],
                ['img' => 'Resim-8.jpg', 'title' => __('Eğitim Programları', 'kaplan'),                  'link' => '/egitimler/'],
                ['img' => 'Resim-6.jpg', 'title' => __('Mobil Uygulamalar', 'kaplan'),                   'link' => '/is-zekasi-yazilimlari/'],
                ['img' => 'Resim-9.jpg', 'title' => __('Bireysel Eğitimler', 'kaplan'),                  'link' => '/egitimler/'],
            ];
            // SEO: tüm arka planları tek <style> bloğunda topla (style= attribute kullanma).
            echo '<style>';
            foreach ($portfolio as $pidx => $tile) {
                $bg = $img . '/hero/' . $tile['img'];
                echo ".portfolio-tile--n{$pidx}{background-image:url('" . esc_url($bg) . "');}";
            }
            echo "</style>\n";
            foreach ($portfolio as $pidx => $tile) :
                $href = function_exists('kpl_localized_url') ? kpl_localized_url($tile['link']) : home_url($tile['link']);
                ?>
                <a class="portfolio-tile portfolio-tile--n<?php echo (int) $pidx; ?>" href="<?php echo esc_url($href); ?>">
                    <div class="portfolio-tile__body">
                        <h3><?php echo wp_kses_post($tile['title']); ?></h3>
                        <span class="portfolio-tile__line"></span>
                        <span class="portfolio-tile__cta"><?php esc_html_e('Keşfet', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== VIDEO + INTRO TEXT ===== -->
<section class="section split">
    <div class="container split__grid">
        <div class="split__media">
            <div class="video-frame">
                <iframe loading="lazy" src="https://www.youtube.com/embed/tprSybaqaL8" title="Kaplan Eğitim" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
        </div>
        <div class="split__text">
            <span class="section-head__eyebrow"><?php esc_html_e('Hakkımızda', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Büyük resmi', 'kaplan'); ?> <span class="accent"><?php esc_html_e('görmenizi', 'kaplan'); ?></span> <?php esc_html_e('sağlıyoruz.', 'kaplan'); ?></h2>
            <p><strong><?php esc_html_e('Kaplan Eğitim ve Danışmanlık', 'kaplan'); ?></strong> <?php esc_html_e('olarak; İZLENEBİLİR ve YÖNETİLEBİLİR bir şirkete dönüşme yolculuğunuzda eğitim, danışmanlık ve iş çözümleri ile destek veriyoruz.', 'kaplan'); ?></p>
            <ul class="check-list">
                <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Veri odaklı karar süreçleri', 'kaplan'); ?></li>
                <li><i class="fa-solid fa-check"></i> <?php esc_html_e('1 sayfa konseptiyle yalın anlatım', 'kaplan'); ?></li>
                <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Otomasyon &amp; RPA çözümleri', 'kaplan'); ?></li>
                <li><i class="fa-solid fa-check"></i> <?php esc_html_e('Yöneticiye özel gelişim programları', 'kaplan'); ?></li>
            </ul>
            <a href="<?php echo esc_url(kpl_localized_url('/danismanlik-ve-projelerimiz/')); ?>" class="btn btn--primary"><?php esc_html_e('Hizmetlerimizi inceleyin', 'kaplan'); ?></a>
        </div>
    </div>
</section>

<!-- ===== STATS ===== -->
<section class="stats">
    <div class="container">
        <div class="grid grid-4">
            <div class="stat">
                <div class="stat__icon"><i class="fa-solid fa-diagram-project"></i></div>
                <div class="stat__number" data-target="<?php echo esc_attr(kaplan_opt('kaplan_stat_projects')); ?>">0</div>
                <div class="stat__label"><?php esc_html_e('Proje', 'kaplan'); ?></div>
            </div>
            <div class="stat">
                <div class="stat__icon"><i class="fa-solid fa-handshake"></i></div>
                <div class="stat__number" data-target="<?php echo esc_attr(kaplan_opt('kaplan_stat_clients')); ?>">0</div>
                <div class="stat__label"><?php esc_html_e('Müşteri', 'kaplan'); ?></div>
            </div>
            <div class="stat">
                <div class="stat__icon"><i class="fa-solid fa-book-open"></i></div>
                <div class="stat__number" data-target="<?php echo esc_attr(kaplan_opt('kaplan_stat_sets')); ?>">0</div>
                <div class="stat__label"><?php esc_html_e('Gelişim Seti', 'kaplan'); ?></div>
            </div>
            <div class="stat">
                <div class="stat__icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                <div class="stat__number" data-target="<?php echo esc_attr(kaplan_opt('kaplan_stat_programs')); ?>">0</div>
                <div class="stat__label"><?php esc_html_e('Eğitim Programı', 'kaplan'); ?></div>
            </div>
        </div>
    </div>
</section>

<!-- ===== HİZMETLERİMİZ ===== -->
<section class="section section--soft" id="hizmetler">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Hizmetlerimiz', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Size özel çözümler', 'kaplan'); ?></h2>
            <p><?php esc_html_e('Sizin istediğiniz, sizin için tasarlanmış.', 'kaplan'); ?></p>
            <span class="section-head__line"></span>
        </header>

        <div class="grid grid-3" id="egitimler">
            <article class="service-card">
                <div class="service-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/workspace-icons-04-150x150.png'); ?>" alt="" />
                </div>
                <h3><?php esc_html_e('Eğitim', 'kaplan'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Verilerle Yönetim', 'kaplan'); ?></li>
                    <li><?php esc_html_e('Yeni Mezunun Yaşam Kiti', 'kaplan'); ?></li>
                    <li><?php esc_html_e('Yöneticinin Yol Haritası', 'kaplan'); ?></li>
                </ul>
                <a href="<?php echo esc_url(kpl_localized_url('/egitimler/')); ?>" class="link-arrow"><?php esc_html_e('Detay', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
            </article>

            <article class="service-card service-card--featured">
                <span class="service-card__badge"><?php esc_html_e('Popüler', 'kaplan'); ?></span>
                <div class="service-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/startup-ikon-29-150x150.png'); ?>" alt="" />
                </div>
                <h3><?php esc_html_e('Danışmanlık', 'kaplan'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Veri Görselleştirme', 'kaplan'); ?></li>
                    <li><?php esc_html_e('Entegre İş Planlama', 'kaplan'); ?></li>
                    <li><?php esc_html_e('Excel Koçluğu', 'kaplan'); ?></li>
                </ul>
                <a href="<?php echo esc_url(kpl_localized_url('/danismanlik-ve-projelerimiz/')); ?>" class="link-arrow"><?php esc_html_e('Detay', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
            </article>

            <article class="service-card">
                <div class="service-card__icon">
                    <img src="<?php echo esc_url($icon_base . '/workspace-icons-30-150x150.png'); ?>" alt="" />
                </div>
                <h3><?php esc_html_e('Proje Çözümleri', 'kaplan'); ?></h3>
                <ul>
                    <li><?php esc_html_e('İş Zekası', 'kaplan'); ?></li>
                    <li><?php esc_html_e('Otomasyon', 'kaplan'); ?></li>
                    <li><?php esc_html_e('Robotik Süreç (RPA)', 'kaplan'); ?></li>
                </ul>
                <a href="<?php echo esc_url(kpl_localized_url('/is-zekasi-yazilimlari/')); ?>" class="link-arrow"><?php esc_html_e('Detay', 'kaplan'); ?> <i class="fa-solid fa-arrow-right"></i></a>
            </article>
        </div>
    </div>
</section>

<!-- ===== EKİBİMİZ ===== -->
<section class="section">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Ekibimiz', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Alanında uzman ekibimiz', 'kaplan'); ?></h2>
            <p><?php esc_html_e('Farklı sektör deneyimine sahip ekip üyelerimizden bazıları.', 'kaplan'); ?></p>
            <span class="section-head__line"></span>
        </header>

        <?php get_template_part('template-parts/team-grid'); ?>
    </div>
</section>

<!-- ===== CLIENTS ===== -->
<section class="section section--soft clients">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Referanslar', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Birlikte çalıştığımız markalar', 'kaplan'); ?></h2>
            <span class="section-head__line"></span>
        </header>
        <div class="clients-marquee">
            <div class="clients-track">
                <?php
                // kpl_client CPT'den logolar; boşsa eski dosya listesine düş.
                $client_q = new WP_Query([
                    'post_type'      => 'kpl_client',
                    'posts_per_page' => -1,
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                ]);
                $logos = [];
                if ($client_q->have_posts()) {
                    while ($client_q->have_posts()) {
                        $client_q->the_post();
                        $logo_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                        if ($logo_url) {
                            $logos[] = ['src' => $logo_url, 'alt' => get_the_title(), 'url' => get_post_meta(get_the_ID(), '_kpl_client_url', true)];
                        }
                    }
                    wp_reset_postdata();
                }
                if (empty($logos)) {
                    foreach (['client_05.png','client_06.png','client_07.png','client_08.png','client_10.png','client_11.png','client_09.png','client_13.png','client_12.png','client_02.png','client_03.png','client_1rr.png','client_14.png'] as $f) {
                        $logos[] = ['src' => $img . '/clients/' . $f, 'alt' => '', 'url' => ''];
                    }
                }
                // İki kez yazıyoruz; seamless marquee için
                for ($i = 0; $i < 2; $i++) :
                    foreach ($logos as $logo) :
                        $tag_open  = $logo['url'] ? '<a href="' . esc_url($logo['url']) . '" target="_blank" rel="noopener">' : '';
                        $tag_close = $logo['url'] ? '</a>' : '';
                        echo $tag_open;
                        printf('<img src="%s" alt="%s" loading="lazy" decoding="async" />', esc_url($logo['src']), esc_attr($logo['alt']));
                        echo $tag_close;
                    endforeach;
                endfor; ?>
            </div>
        </div>
    </div>
</section>

<?php
set_query_var('cta_title', __('Bir sonraki adımı birlikte planlayalım', 'kaplan'));
set_query_var('cta_sub',   __('Eğitim, danışmanlık ya da iş zekası — size en uygun çözümü konuşalım.', 'kaplan'));
get_template_part('template-parts/cta-band');

get_footer();
