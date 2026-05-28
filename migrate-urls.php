<?php
/**
 * Kaplan — Serileştirme-güvenli URL migration aracı.
 *
 * Lokal → production taşımada DB'deki absolute URL'leri (menü, slide/footer
 * linkleri, görsel yolları, siteurl/home, Polylang/theme_mod serialized
 * verileri) GÜVENLE değiştirir. Düz SQL REPLACE serialized veriyi bozar;
 * bu script unserialize → recurse → reserialize yaparak bozmaz.
 *
 * KULLANIM (production'da, DB import edildikten SONRA):
 *   1. Bu dosyayı production kökine (public_html) yükle.
 *   2. Tarayıcıda aç: https://www.kaplanegitim.com/migrate-urls.php
 *      → DRY-RUN (varsayılan): ne değişeceğini RAPORLAR, hiçbir şey yazmaz.
 *   3. Rapor doğruysa: aşağıda DRY_RUN = false yap, tekrar çalıştır → UYGULAR.
 *   4. İŞ BİTİNCE BU DOSYAYI SİL.  + Ayarlar → Kalıcı Bağlantılar → Kaydet.
 *
 * GÜVENLİK: admin girişi (veya CLI) gerektirir. Önce DB yedeği al!
 */

define('WP_USE_THEMES', false);
require __DIR__ . '/wp-load.php';

if (php_sapi_name() !== 'cli' && !current_user_can('manage_options')) {
    wp_die('Yetki yok — admin olarak giriş yapın.');
}
header('Content-Type: text/plain; charset=utf-8');

/* ============ AYARLAR ============ */
$DRY_RUN = true;   // true = sadece raporla; false = gerçekten değiştir
$REPLACEMENTS = [
    // Sıra önemli: önce tam (şemalı) URL, sonra şemasız.
    'http://localhost/kaplan'  => 'https://www.kaplanegitim.com',
    'https://localhost/kaplan' => 'https://www.kaplanegitim.com',
    'localhost/kaplan'         => 'www.kaplanegitim.com',
];
// guid ASLA değiştirilmemeli (WP feed kimliği) — atla.
$SKIP_COLUMNS = ['guid'];
/* ================================= */

/**
 * Serileştirme-güvenli recursive replace (interconnect/it algoritması).
 */
function kpl_sr($from, $to, $data, $serialised = false) {
    try {
        if (is_string($data) && $data !== '' && ($un = @unserialize($data)) !== false) {
            $data = kpl_sr($from, $to, $un, true);
        } elseif (is_array($data)) {
            $tmp = [];
            foreach ($data as $k => $v) $tmp[$k] = kpl_sr($from, $to, $v, false);
            $data = $tmp;
        } elseif (is_object($data)) {
            $tmp = clone $data;
            foreach (get_object_vars($data) as $k => $v) $tmp->$k = kpl_sr($from, $to, $v, false);
            $data = $tmp;
        } elseif (is_string($data)) {
            $data = str_replace($from, $to, $data);
        }
        if ($serialised) return serialize($data);
    } catch (Exception $e) {}
    return $data;
}

global $wpdb;
$tables = $wpdb->get_col('SHOW TABLES');
$total_changed = 0;

echo ($DRY_RUN ? '### DRY-RUN (yazma YOK — sadece rapor) ###' : '### UYGULA MODU — DB DEĞİŞTİRİLİYOR ###') . "\n";
echo 'Değişim: ' . implode(' · ', array_map(fn($k, $v) => "$k → $v", array_keys($REPLACEMENTS), $REPLACEMENTS)) . "\n\n";

foreach ($tables as $table) {
    // Primary key kolon adı bul (SHOW KEYS'te Column_name = 5. kolon → offset 4)
    $pk = $wpdb->get_var("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'", 4);
    if (!$pk) continue; // PK yoksa atla

    // Metin kolonlarını al
    $cols = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
    $text_cols = [];
    foreach ($cols as $c) {
        if (in_array($c->Field, $SKIP_COLUMNS, true)) continue;
        if (preg_match('/(char|text|blob|json)/i', $c->Type)) $text_cols[] = $c->Field;
    }
    if (!$text_cols) continue;

    $changed = 0;
    $select_cols = '`' . $pk . '`,`' . implode('`,`', $text_cols) . '`';
    $rows = $wpdb->get_results("SELECT $select_cols FROM `$table`", ARRAY_A);

    foreach ($rows as $row) {
        $updates = [];
        foreach ($text_cols as $col) {
            $orig = $row[$col];
            if ($orig === null || $orig === '') continue;
            $hit = false;
            foreach ($REPLACEMENTS as $f => $t) { if (strpos($orig, $f) !== false) { $hit = true; break; } }
            if (!$hit) continue;
            $new = $orig;
            foreach ($REPLACEMENTS as $f => $t) $new = kpl_sr($f, $t, $new);
            if ($new !== $orig) $updates[$col] = $new;
        }
        if ($updates) {
            $changed++;
            if (!$DRY_RUN) $wpdb->update($table, $updates, [$pk => $row[$pk]]);
        }
    }

    if ($changed) { echo sprintf("  %-28s %d satır %s\n", $table, $changed, $DRY_RUN ? 'değişecek' : 'güncellendi'); $total_changed += $changed; }
}

echo "\n--- TOPLAM: $total_changed satır " . ($DRY_RUN ? 'değişecek' : 'güncellendi') . " ---\n";
if ($DRY_RUN) echo "Uygulamak için: \$DRY_RUN = false yapıp tekrar çalıştırın.\n";
else echo "BİTTİ. Bu dosyayı SİLİN + Kalıcı Bağlantılar → Kaydet.\n";
