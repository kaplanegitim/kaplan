<?php
/**
 * İnfografik ve Sunum sayfa template'i — gallery galleries.
 *
 * @package Kaplan
 */

get_header();

$img = KAPLAN_URI . '/assets/img';

set_query_var('hero_title', __('<span>İnfografik</span> ve sunum projelerimiz', 'kaplan'));
set_query_var('hero_sub',   __('Karmaşık bilgiyi yalın ve görsel olarak sunmak için tasarladığımız infografik ve sunum çalışmaları.', 'kaplan'));
set_query_var('hero_crumb', __('İnfografik ve Sunum', 'kaplan'));
set_query_var('hero_bg',    'hero/Resim-7.jpg');
get_template_part('template-parts/page-hero');

$ig = $img . '/infographics';
// 'img' dolu = gerçek görsel; '' = henüz eklenmemiş, placeholder.
// Masonry layout: portre + yatay görseller doğal oranıyla, kırpılmadan akar.
$infografik = [
    ['img' => 'kervan-gida-infografik.jpg', 'label' => __('Kervan Gıda · İnfografik', 'kaplan')],
    ['img' => 'vodafone-infografik.jpg',    'label' => __('Vodafone · İnfografik', 'kaplan')],
    ['img' => 'ekol-infografik.jpg',        'label' => __('Ekol Lojistik · İnfografik', 'kaplan')],
    ['img' => 'aras-kargo-infografik.png',  'label' => __('Aras Kargo · İnfografik', 'kaplan')],
];
$sunum = [
    ['img' => 'ik-butce-sunumu.png',    'label' => __('Aras Kargo · İK Bütçe Yönetici Sunumu', 'kaplan')],
    ['img' => 'kervan-sunum.jpg',       'label' => __('Kervan · Halka Arz Sunumu', 'kaplan')],
    ['img' => 'teb-sunum.png',          'label' => __('TEB · Sunum', 'kaplan')],
];

// Uniform galeri tile (tüm kartlar eşit boyut; görsel cover ile yerleşir).
$kpl_gallery_tile = function (array $g) use ($ig) {
    if (!empty($g['img'])) {
        $src = $ig . '/' . $g['img'];
        printf(
            '<a href="%1$s" target="_blank" rel="noopener" class="gallery-item" style="background-image:url(\'%1$s\');"><span class="gallery-item__zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span><span class="gallery-item__label">%2$s</span></a>',
            esc_url($src),
            esc_html($g['label'])
        );
    } else {
        printf(
            '<div class="gallery-item gallery-item--placeholder"><i class="fa-regular fa-image" aria-hidden="true"></i><span class="gallery-item__label">%s</span><span class="gallery-item__soon">%s</span></div>',
            esc_html($g['label']),
            esc_html__('Görsel yakında', 'kaplan')
        );
    }
};
?>

<section class="section">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('İnfografik Projeleri', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Görsel anlatımın gücü', 'kaplan'); ?></h2>
            <p><?php esc_html_e('Veri ve bilgiyi tek bakışta anlaşılır hale getiren tasarımlar.', 'kaplan'); ?></p>
            <span class="section-head__line"></span>
        </header>

        <div class="gallery-grid">
            <?php foreach ($infografik as $g) { $kpl_gallery_tile($g); } ?>
        </div>
    </div>
</section>

<section class="section section--soft">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Sunum Projeleri', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Yönetici özetlerinde fark', 'kaplan'); ?></h2>
            <p><?php esc_html_e('Veriyi hikayeleştiren, izlenebilir, ikna eden sunumlar.', 'kaplan'); ?></p>
            <span class="section-head__line"></span>
        </header>

        <div class="gallery-grid gallery-grid--wide">
            <?php foreach ($sunum as $g) { $kpl_gallery_tile($g); } ?>
        </div>
    </div>
</section>

<!-- NEDEN -->
<section class="section">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow"><?php esc_html_e('Neden Önemli?', 'kaplan'); ?></span>
            <h2><?php esc_html_e('Bilgi yalınlaştığında karar hızlanır', 'kaplan'); ?></h2>
            <span class="section-head__line"></span>
        </header>

        <div class="feature-list">
            <div class="feature-list__item">
                <div class="feature-list__icon"><i class="fa-solid fa-eye"></i></div>
                <div><h4><?php esc_html_e('Tek Bakışta Anlaşılır', 'kaplan'); ?></h4><p><?php esc_html_e('30 sayfayı tek sayfada özetleyen tasarımlar.', 'kaplan'); ?></p></div>
            </div>
            <div class="feature-list__item">
                <div class="feature-list__icon"><i class="fa-solid fa-bolt"></i></div>
                <div><h4><?php esc_html_e('Hızlı Karar', 'kaplan'); ?></h4><p><?php esc_html_e('Yöneticilere hız kazandıran görsel ipuçları.', 'kaplan'); ?></p></div>
            </div>
            <div class="feature-list__item">
                <div class="feature-list__icon"><i class="fa-solid fa-share-nodes"></i></div>
                <div><h4><?php esc_html_e('Kolay Paylaşım', 'kaplan'); ?></h4><p><?php esc_html_e('PDF, e-posta veya web olarak dağıtıma uygun.', 'kaplan'); ?></p></div>
            </div>
            <div class="feature-list__item">
                <div class="feature-list__icon"><i class="fa-solid fa-handshake-angle"></i></div>
                <div><h4><?php esc_html_e('İkna Eder', 'kaplan'); ?></h4><p><?php esc_html_e('Sayılar, görsellerle hikayeye dönüşür.', 'kaplan'); ?></p></div>
            </div>
        </div>
    </div>
</section>

<?php
set_query_var('cta_title', __('İnfografik / Sunum çalışması ister misiniz?', 'kaplan'));
set_query_var('cta_sub',   __('Hangi konuda görselleştirme yapacağımızı birlikte konuşalım.', 'kaplan'));
get_template_part('template-parts/cta-band');

get_footer();
