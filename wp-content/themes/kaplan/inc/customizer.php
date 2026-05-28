<?php
/**
 * Kaplan Customizer — İletişim & Sosyal ayarları.
 *
 * Telefon, e-posta ve sosyal medya URL'leri buradan yönetilir
 * (dile bağlı olmayan değerler). Adres / çalışma saatleri çevrilebilir
 * olduğu için template'lerde gettext olarak kalır.
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

/**
 * Varsayılan değerler — tek kaynak. Hem Customizer default'u hem de
 * theme_mod fallback'i olarak kullanılır.
 */
function kaplan_contact_defaults(): array {
    return [
        'kaplan_phone'             => '+90 530 967 23 66',
        'kaplan_email'             => 'bilgi@kaplanegitim.com',
        'kaplan_social_linkedin'   => 'https://www.linkedin.com/company/kaplan-e%C4%9Fitim-ve-dan%C4%B1%C5%9Fmanl%C4%B1k/',
        'kaplan_social_youtube'    => 'https://www.youtube.com/@kaplanegitimvedansmanlk9990',
        'kaplan_social_instagram'  => '',
        // Anasayfa istatistik sayaçları (etiketler gettext kalır, sadece sayılar düzenlenir).
        'kaplan_stat_projects'     => '42',
        'kaplan_stat_clients'      => '28',
        'kaplan_stat_sets'         => '6',
        'kaplan_stat_programs'     => '20',
    ];
}

/**
 * theme_mod okuma kısayolu (default'lu).
 */
function kaplan_opt(string $key): string {
    $defaults = kaplan_contact_defaults();
    return (string) get_theme_mod($key, $defaults[$key] ?? '');
}

/**
 * Telefon numarasını tel: href'i için sadeleştir (+ ve rakamlar).
 */
function kaplan_tel_href(string $phone): string {
    return 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
}

add_action('customize_register', function ($wp_customize) {
    $defaults = kaplan_contact_defaults();

    $wp_customize->add_section('kaplan_contact', [
        'title'    => __('İletişim & Sosyal', 'kaplan'),
        'priority' => 30,
    ]);

    // --- İletişim ---
    $wp_customize->add_setting('kaplan_phone', [
        'default'           => $defaults['kaplan_phone'],
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control('kaplan_phone', [
        'label'   => __('Telefon', 'kaplan'),
        'section' => 'kaplan_contact',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('kaplan_email', [
        'default'           => $defaults['kaplan_email'],
        'sanitize_callback' => 'sanitize_email',
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control('kaplan_email', [
        'label'   => __('E-posta', 'kaplan'),
        'section' => 'kaplan_contact',
        'type'    => 'email',
    ]);

    // --- Sosyal medya (boş bırakılan ikon gizlenir) ---
    foreach ([
        'kaplan_social_linkedin'  => 'LinkedIn',
        'kaplan_social_youtube'   => 'YouTube',
        'kaplan_social_instagram' => 'Instagram',
    ] as $key => $label) {
        $wp_customize->add_setting($key, [
            'default'           => $defaults[$key],
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ]);
        $wp_customize->add_control($key, [
            'label'       => sprintf(__('%s URL', 'kaplan'), $label),
            'description' => __('Boş bırakılırsa ikon gizlenir.', 'kaplan'),
            'section'     => 'kaplan_contact',
            'type'        => 'url',
        ]);
    }

    // --- Anasayfa istatistik sayaçları ---
    $wp_customize->add_section('kaplan_homepage', [
        'title'    => __('Anasayfa İstatistikleri', 'kaplan'),
        'priority' => 29,
    ]);
    foreach ([
        'kaplan_stat_projects' => __('Proje sayısı', 'kaplan'),
        'kaplan_stat_clients'  => __('Müşteri sayısı', 'kaplan'),
        'kaplan_stat_sets'     => __('Gelişim Seti sayısı', 'kaplan'),
        'kaplan_stat_programs' => __('Eğitim Programı sayısı', 'kaplan'),
    ] as $key => $label) {
        $wp_customize->add_setting($key, [
            'default'           => $defaults[$key],
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ]);
        $wp_customize->add_control($key, [
            'label'       => $label,
            'description' => __('Anasayfadaki sayaçta görünen rakam.', 'kaplan'),
            'section'     => 'kaplan_homepage',
            'type'        => 'number',
            'input_attrs' => ['min' => 0, 'step' => 1],
        ]);
    }

    // --- Entegrasyonlar: Google Analytics 4 ---
    $wp_customize->add_section('kaplan_integrations', [
        'title'    => __('Analitik & Entegrasyon', 'kaplan'),
        'priority' => 31,
    ]);
    $wp_customize->add_setting('kaplan_ga4_id', [
        'default'           => '',
        'sanitize_callback' => 'kaplan_sanitize_ga4_id',
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control('kaplan_ga4_id', [
        'label'       => __('Google Analytics 4 Ölçüm Kimliği', 'kaplan'),
        'description' => __('Örn: G-XXXXXXXXXX. Boş bırakılırsa analytics yüklenmez.', 'kaplan'),
        'section'     => 'kaplan_integrations',
        'type'        => 'text',
    ]);
});

/**
 * GA4 Measurement ID doğrula (G-XXXX biçimi). Geçersizse boş döner.
 */
function kaplan_sanitize_ga4_id($value): string {
    $value = trim((string) $value);
    return preg_match('/^G-[A-Z0-9]{4,}$/i', $value) ? strtoupper($value) : '';
}
