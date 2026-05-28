<?php
/**
 * Deploy script — cPanel Git Version Control '.cpanel.yml' tarafından çağrılır.
 *
 * Davranış: rsync -a --delete eşdeğeri (orphan temizliği), checksum/mtime ile
 * değişmemiş dosyaları atlar, sonunda opcache'i reset eder. Native PHP — rsync,
 * cp ve exec'e bağımsız çalışır. cPanel taskrunner sadece /usr/bin/php'ye muhtaç.
 *
 * Usage:
 *   php bin/deploy.php <src-rel-or-abs> <dst-abs>
 *
 * Örnek (.cpanel.yml içinde):
 *   - /usr/bin/php bin/deploy.php wp-content/themes/kaplan $HOME/public_html/wp-content/themes/kaplan
 *
 * Exit kodu:
 *   0  Başarılı (errors=0)
 *   1  Çalıştı ama bir veya daha fazla dosyada hata var
 *   2  Argüman hatası
 *   3  Kaynak dizin yok
 *   4  Hedef dizin oluşturulamadı
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "ERROR: CLI only\n");
    exit(2);
}
if ($argc < 3) {
    fwrite(STDERR, "Usage: php bin/deploy.php <src> <dst>\n");
    exit(2);
}

$src = rtrim($argv[1], '/');
$dst = rtrim($argv[2], '/');

// Göreceli src'yi CWD'ye göre çöz (cPanel cwd = repo kökü).
if ($src === '' || $src[0] !== '/') {
    $src = getcwd() . '/' . $src;
}

if (!is_dir($src)) {
    fwrite(STDERR, "ERROR: SRC missing: $src\n");
    exit(3);
}
if (!is_dir($dst) && !@mkdir($dst, 0755, true)) {
    fwrite(STDERR, "ERROR: DST mkdir failed: $dst\n");
    exit(4);
}

echo "DEPLOY  src=$src\n        dst=$dst\n\n";
$t0 = microtime(true);
$copied = $skipped = $errors = $deleted = 0;
$srcFiles = [];

// Pass 1 — Source'u dest'e senkronize et (sadece değişenler).
$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);
foreach ($it as $s) {
    $rel = substr($s->getPathname(), strlen($src) + 1);
    $srcFiles[$rel] = true;
    $d = $dst . '/' . $rel;
    if ($s->isDir()) {
        if (!is_dir($d) && !@mkdir($d, 0755, true)) {
            fwrite(STDERR, "MKDIR ERR  $rel/\n");
            $errors++;
        }
    } else {
        $need = !file_exists($d)
             || filesize($d) !== $s->getSize()
             || filemtime($d) !== $s->getMTime();
        if ($need) {
            if (@copy($s->getPathname(), $d)) {
                @touch($d, $s->getMTime());
                $copied++;
                if ($copied <= 100) echo "COPY  $rel\n";
            } else {
                fwrite(STDERR, "COPY ERR  $rel\n");
                $errors++;
            }
        } else {
            $skipped++;
        }
    }
}

// Pass 2 — Dest'te source'ta olmayanları sil (rsync --delete eşdeğeri).
$it2 = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dst, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::CHILD_FIRST
);
foreach ($it2 as $d) {
    $rel = substr($d->getPathname(), strlen($dst) + 1);
    if (!isset($srcFiles[$rel])) {
        $ok = $d->isDir() ? @rmdir($d->getPathname()) : @unlink($d->getPathname());
        if ($ok) {
            $deleted++;
            echo "DEL   $rel" . ($d->isDir() ? '/' : '') . "\n";
        } else {
            fwrite(STDERR, "DEL ERR  $rel\n");
            $errors++;
        }
    }
}

$elapsed = round(microtime(true) - $t0, 2);
echo "\n--- COPIED: $copied  SKIPPED: $skipped  DELETED: $deleted  ERRORS: $errors  ({$elapsed}s)\n";

// Opcache reset — deploy sonrası eski bytecode'u temizle.
if (function_exists('opcache_reset')) {
    $r = @opcache_reset();
    echo "opcache_reset: " . ($r ? 'OK' : 'failed/disabled') . "\n";
}

exit($errors > 0 ? 1 : 0);
