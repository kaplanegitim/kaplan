<?php
/**
 * Form handler — AJAX endpoint.
 *
 * Endpoint: admin-ajax.php?action=kpl_submit
 *
 * Akış:
 *   1. Honeypot kontrolü (_kpl_website doluysa silent fail)
 *   2. Nonce doğrula
 *   3. Form type ve zorunlu alan validation
 *   4. Sanitize
 *   5. CPT post olarak kaydet (kpl_submission)
 *   6. wp_mail ile admin'e bildirim gönder (varsa)
 *   7. JSON success/error response
 *
 * @package Kaplan
 */

if (!defined('ABSPATH')) exit;

/**
 * Form data tanımları: hangi alanlar zorunlu, hangileri toplanır.
 */
function kpl_form_schemas(): array {
    return [
        'training' => [
            'label'    => __('Eğitim Talep Formu', 'kaplan'),
            'required' => ['first_name', 'last_name', 'email', 'phone', 'training'],
            'fields'   => ['first_name', 'last_name', 'email', 'phone', 'company', 'position', 'training', 'message'],
        ],
        'contact' => [
            'label'    => __('İletişim Formu', 'kaplan'),
            'required' => ['name', 'email', 'message'],
            'fields'   => ['name', 'email', 'subject', 'message'],
        ],
        'bireysel' => [
            'label'    => __('Bireysel Eğitim Bilgi Formu', 'kaplan'),
            'required' => ['email', 'program'],
            'fields'   => ['name', 'email', 'program', 'message'],
        ],
    ];
}

/** Form türü → başarı sonrası yönlendirilecek URL (yoksa null). */
function kpl_form_redirect_url(string $form_type): ?string {
    if ($form_type === 'bireysel') {
        return 'https://bireysel.kaplanegitim.com/';
    }
    return null;
}

/** Form alan anahtarı → Türkçe etiket (admin detay + mail). */
function kpl_form_field_label(string $key): string {
    $map = [
        'first_name' => __('Ad', 'kaplan'),
        'last_name'  => __('Soyad', 'kaplan'),
        'name'       => __('Ad Soyad', 'kaplan'),
        'email'      => __('E-posta', 'kaplan'),
        'phone'      => __('Telefon', 'kaplan'),
        'company'    => __('Şirket', 'kaplan'),
        'position'   => __('Pozisyon', 'kaplan'),
        'training'   => __('Eğitim', 'kaplan'),
        'program'    => __('İlgilenilen Program', 'kaplan'),
        'subject'    => __('Konu', 'kaplan'),
        'message'    => __('Mesaj', 'kaplan'),
    ];
    return $map[$key] ?? ucfirst(str_replace(['_', '-'], ' ', $key));
}

function kpl_form_send_response(bool $ok, string $msg, array $extra = []) {
    wp_send_json(array_merge(['ok' => $ok, 'message' => $msg], $extra), $ok ? 200 : 400);
}

function kpl_form_handle_submission() {
    // 1. Honeypot
    if (!empty($_POST['_kpl_website'])) {
        // Bot. Sessizce başarı dön (zaman kaybettirme).
        kpl_form_send_response(true, __('Teşekkürler, kaydınız alındı.', 'kaplan'));
    }

    // 2. Nonce
    $nonce = isset($_POST['_kpl_nonce']) ? sanitize_text_field(wp_unslash($_POST['_kpl_nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'kpl_form_submit')) {
        kpl_form_send_response(false, __('Güvenlik doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.', 'kaplan'));
    }

    // 3. Form type
    $form_type = isset($_POST['form_type']) ? sanitize_key($_POST['form_type']) : '';
    $schemas   = kpl_form_schemas();
    if (!isset($schemas[$form_type])) {
        kpl_form_send_response(false, __('Geçersiz form türü.', 'kaplan'));
    }
    $schema = $schemas[$form_type];

    // 4. Validation: required fields
    $data = [];
    foreach ($schema['fields'] as $field) {
        $raw = $_POST[$field] ?? '';
        if (is_array($raw)) $raw = implode(', ', array_map('sanitize_text_field', $raw));
        if ($field === 'email')    $value = sanitize_email(wp_unslash($raw));
        elseif ($field === 'message') $value = sanitize_textarea_field(wp_unslash($raw));
        else                       $value = sanitize_text_field(wp_unslash($raw));
        $data[$field] = $value;
    }

    foreach ($schema['required'] as $req) {
        if (empty($data[$req])) {
            kpl_form_send_response(false, sprintf(
                __('"%s" alanı zorunludur.', 'kaplan'),
                ucfirst(str_replace('_', ' ', $req))
            ));
        }
    }
    if (!empty($data['email']) && !is_email($data['email'])) {
        kpl_form_send_response(false, __('Lütfen geçerli bir e-posta adresi girin.', 'kaplan'));
    }

    // 5. CPT'ye kaydet
    $title_parts = [];
    if (!empty($data['first_name']))     $title_parts[] = $data['first_name'];
    if (!empty($data['last_name']))      $title_parts[] = $data['last_name'];
    if (empty($title_parts) && !empty($data['name'])) $title_parts[] = $data['name'];
    if (empty($title_parts)) $title_parts[] = '(bilinmeyen)';
    $title = trim(implode(' ', $title_parts));

    $post_id = wp_insert_post([
        'post_type'    => 'kpl_submission',
        'post_status'  => 'publish',
        'post_title'   => $title,
        'post_content' => $data['message'] ?? '',
    ], true);

    if (is_wp_error($post_id)) {
        kpl_form_send_response(false, __('Kayıt sırasında hata oluştu, lütfen tekrar deneyin.', 'kaplan'));
    }

    update_post_meta($post_id, '_kpl_form_type', $form_type);
    update_post_meta($post_id, '_kpl_data',      wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    update_post_meta($post_id, '_kpl_ip',        kpl_form_client_ip());
    update_post_meta($post_id, '_kpl_user_agent', isset($_SERVER['HTTP_USER_AGENT']) ? substr(sanitize_text_field($_SERVER['HTTP_USER_AGENT']), 0, 500) : '');

    // 6. Mail
    kpl_form_send_admin_email($form_type, $schema['label'], $data, $post_id);

    // 7. Success
    if ($form_type === 'training') {
        $success_msg = __('Talebiniz alındı, en kısa sürede dönüş yapacağız.', 'kaplan');
    } elseif ($form_type === 'bireysel') {
        $success_msg = __('Bilgileriniz alındı, sizi bireysel eğitim sayfamıza yönlendiriyoruz…', 'kaplan');
    } else {
        $success_msg = __('Mesajınız alındı, en kısa sürede dönüş yapacağız.', 'kaplan');
    }
    $extra   = [];
    $redirect = kpl_form_redirect_url($form_type);
    if ($redirect) $extra['redirect'] = $redirect;
    kpl_form_send_response(true, $success_msg, $extra);
}

add_action('wp_ajax_kpl_submit',        'kpl_form_handle_submission');
add_action('wp_ajax_nopriv_kpl_submit', 'kpl_form_handle_submission');

function kpl_form_client_ip(): string {
    foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $k) {
        if (!empty($_SERVER[$k])) {
            $ip = trim(explode(',', $_SERVER[$k])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return '';
}

function kpl_form_send_admin_email(string $form_type, string $form_label, array $data, int $post_id): void {
    $to      = get_option('admin_email');
    $sitename = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    $subject = sprintf('[%s] %s — yeni gönderim', $sitename, $form_label);

    $lines = ["{$form_label} gönderimi alındı.", ''];
    foreach ($data as $k => $v) {
        if (in_array($k, ['nonce', 'action', '_kpl_website'], true)) continue;
        $label = kpl_form_field_label($k);
        $lines[] = "{$label}: {$v}";
    }
    $lines[] = '';
    $lines[] = '---';
    $lines[] = 'Detay: ' . admin_url('post.php?post=' . $post_id . '&action=edit');

    $body = implode("\n", $lines);

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
    ];
    if (!empty($data['email']) && is_email($data['email'])) {
        $reply_from = !empty($data['first_name']) ? trim($data['first_name'] . ' ' . ($data['last_name'] ?? '')) : ($data['name'] ?? '');
        $headers[] = 'Reply-To: ' . ($reply_from ? "{$reply_from} <{$data['email']}>" : $data['email']);
    }

    @wp_mail($to, $subject, $body, $headers);
}

// Frontend'e ajaxurl + nonce sağla
add_action('wp_enqueue_scripts', function () {
    $form_slugs = ['egitim-talep-formu', 'iletisim', 'egitimler'];
    $load = is_front_page() || is_page($form_slugs);
    // Polylang: EN çeviri sayfalarında TR slug'ına göre de yükle.
    if (!$load && is_page() && function_exists('pll_get_post')) {
        $tr_id = pll_get_post(get_queried_object_id(), 'tr');
        $tr    = $tr_id ? get_post($tr_id) : null;
        if ($tr && in_array($tr->post_name, $form_slugs, true)) $load = true;
    }
    // Sadece formlu sayfalarda yükle
    if (!$load) return;

    wp_register_script('kpl-forms', KAPLAN_URI . '/assets/js/forms.js', [], KAPLAN_VERSION, ['in_footer' => true, 'strategy' => 'defer']);
    wp_localize_script('kpl-forms', 'KPL_FORMS', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('kpl_form_submit'),
        'i18n'    => [
            'sending' => __('Gönderiliyor…', 'kaplan'),
            'error'   => __('Bir hata oluştu, lütfen tekrar deneyin.', 'kaplan'),
        ],
    ]);
    wp_enqueue_script('kpl-forms');
}, 110);
