# Kaplan — Kurulum & Yeniden Yapılandırma

Bu dosya, repo'dan başlayarak çalışan bir Kaplan kurulumu (lokal veya prod) elde etmek için gereken adımları listeler. Repo **sadece**:

- `wp-content/themes/kaplan/` (custom tema)
- Kök dokümanlar, `.htaccess`, `migrate-urls.php`, `screenshot.png`, `.gitignore`

içerir. WordPress core, default tema'lar, 3rd-party plugin'ler, `wp-content/uploads/`, `wp-config.php` ve veritabanı repo'da **tutulmaz** — aşağıdaki adımlarla harici kaynaklardan getirilir.

> Bu dosya bir **dokümandır, backup değildir.** Plugin **verisi / ayarları / uploads / DB** için All-in-One WP Migration export'u veya hosting backup'ı kullanın.

---

## 1. Gereksinimler

| Bileşen | Versiyon |
|---|---|
| WordPress | **7.0** (`wp-includes/version.php`'deki `$wp_version`) |
| PHP | **7.4+** (lokal dev'de PHP 8.0 ile test edildi) |
| MySQL / MariaDB | **5.5.5+** |
| Web server | Apache (WAMP / cPanel) — `.htaccess` ile pretty permalinks |

---

## 2. Kurulum adımları (sıfırdan)

### 2.1 Repo'yu klonla

```bash
git clone git@github.com:kaplanegitim/kaplan.git
cd kaplan
```

### 2.2 WordPress core'unu indir

```bash
# WP-CLI ile (önerilen)
wp core download --version=7.0 --skip-content

# veya manuel: https://wordpress.org/download/releases/  → 7.0 zip → kök dizine aç
```

`--skip-content` flag'i `wp-content/` klasörünü atlar — bizimkini ezmesin diye önemli.

### 2.3 `wp-config.php` oluştur

```bash
cp wp-config-sample.php wp-config.php   # core indirildikten sonra
```

Sonra şu alanları doldur (örnek altta — §6):

- `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_HOST`
- `AUTH_KEY`/`SECURE_AUTH_KEY`/... salt'ları: https://api.wordpress.org/secret-key/1.1/salt/ üzerinden taze üret
- `WP_DEBUG`, `WP_DEBUG_LOG`, `DISALLOW_FILE_EDIT` vb. proje flag'leri

### 2.4 Veritabanını içe aktar

DB dump'ı **repo'da değil** — hosting backup'ından, All-in-One WP Migration export'undan veya en son `mysqldump`'tan al.

```bash
mysql -u root -p kaplan_local < kaplan-backup.sql

# Lokal → prod URL migration için (varsa):
php migrate-urls.php   # script'i okuyup parametrelerini ayarlayın
# veya:
wp search-replace 'https://eski-domain.com' 'https://yeni-domain.com' --all-tables
```

### 2.5 Uploads'ları getir

`wp-content/uploads/` repo'da yok. Prod'dan kopyala:

```bash
rsync -avz user@prod:/path/to/wp-content/uploads/ wp-content/uploads/
```

### 2.6 Plugin'leri kur

Aşağıdaki §3'teki tabloya bakarak hepsini kur. Toplu komut:

```bash
wp plugin install \
  akismet \
  litespeed-cache \
  polylang \
  wp-mail-smtp \
  wpforms-lite \
  --activate

# All-in-One WP Migration With Import (GitHub fork — wordpress.org'da değil):
cd wp-content/plugins
git clone https://github.com/wp-plugins/all-in-one-wp-migration.git
# veya orijinal "With Import" fork'undan zip indir → çıkar
# Lisans/yasal not: ücretsiz import limit'ini açan community fork
```

### 2.7 Tema'yı aktive et

```bash
wp theme activate kaplan
```

### 2.8 Permalink'leri flush et

```bash
wp rewrite flush
```

---

## 3. Plugin listesi (mevcut prod state)

| Slug | Plugin | Versiyon | Kaynak |
|---|---|---|---|
| `akismet` | Akismet Anti-spam: Spam Protection | 5.7 | https://wordpress.org/plugins/akismet/ |
| `All-In-One-WP-Migration-With-Import-master` | All-in-One WP Migration With Import (ServMask + community fork) | 6.77 | GitHub fork — wordpress.org sürümü import limit'li |
| `litespeed-cache` | LiteSpeed Cache | 7.8.1 | https://wordpress.org/plugins/litespeed-cache/ |
| `polylang` | Polylang (multilang) | 3.8.4 | https://wordpress.org/plugins/polylang/ |
| `wp-mail-smtp` | WP Mail SMTP | 4.8.0 | https://wordpress.org/plugins/wp-mail-smtp/ |
| `wpforms-lite` | WPForms Lite | 1.10.0.5 | https://wordpress.org/plugins/wpforms-lite/ |
| `hello.php` | Hello Dolly | 1.7.2 | WP core ile gelir (gerekirse: `wp plugin delete hello`) |

> **Listeyi güncel tutmak için:** plugin ekleyip/çıkardığınızda bu tabloyu da güncelleyin. Otomatize etmek isterseniz `wp plugin list --format=csv > plugins.lock.csv` komutunu bir script'e koyup commit'leyebilirsiniz.

---

## 4. Tema'lar

| Slug | Tip | Versiyon | Repo'da? |
|---|---|---|---|
| `kaplan` | Custom | — (style.css'e bakın) | ✅ Evet |
| `twentytwentyfive` | WP default | 1.5 | ❌ Hayır — `wp theme install twentytwentyfive` |
| `twentytwentyfour` | WP default | 1.5 | ❌ Hayır |
| `twentytwentythree` | WP default | 1.6 | ❌ Hayır |
| `twentytwentytwo` | WP default | 2.1 | ❌ Hayır |

Default tema'lar genellikle gerekmez; fallback için bir tane bulundurmak yeterli.

---

## 5. mu-plugins

`wp-content/mu-plugins/` repo'da değil. Sadece `automation-by-installatron.php` vardı, Installatron (hosting control panel) tarafından otomatik üretilen bir hook — manuel kurulum gereken bir şey değil. Installatron kullanıyorsanız zaten dosya panel tarafından oluşturulur; kullanmıyorsanız atlayın.

---

## 6. `wp-config.php` örnek template

```php
<?php
// ** Database settings ** //
define( 'DB_NAME',     'kaplan_local' );   // prod'da farklı
define( 'DB_USER',     'root' );           // prod'da farklı
define( 'DB_PASSWORD', '' );               // prod'da MUTLAKA güçlü
define( 'DB_HOST',     'localhost' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

// ** Salts — taze üret: https://api.wordpress.org/secret-key/1.1/salt/ ** //
define('AUTH_KEY',         '...');
define('SECURE_AUTH_KEY',  '...');
define('LOGGED_IN_KEY',    '...');
define('NONCE_KEY',        '...');
define('AUTH_SALT',        '...');
define('SECURE_AUTH_SALT', '...');
define('LOGGED_IN_SALT',   '...');
define('NONCE_SALT',       '...');

$table_prefix = 'wp_';

// ** Dev flag'leri (prod'da kapatın) ** //
define( 'WP_DEBUG',         true );
define( 'WP_DEBUG_LOG',     true );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

define( 'WP_LOCAL_DEV',     true );        // sadece lokal
define( 'DISALLOW_FILE_EDIT', true );      // admin'den dosya düzenlemeyi kapat

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
```

---

## 7. Backup stratejisi (öneri)

- **Kod:** bu repo (push & pull).
- **Veritabanı:** günlük `mysqldump` veya hosting otomatik backup. Aylık bir tanesini offsite tutun.
- **Uploads:** `rsync` veya hosting backup; her major content değişikliğinden sonra.
- **Bütünsel snapshot:** All-in-One WP Migration export'u (`wp-content/ai1wm-backups/`'a yazar; oradan indirip dış depolama). Repo bunları ignore'lar.

---

## 8. Yararlı komutlar

```bash
# Plugin listesini güncelle ve commit'le (manuel refresh)
wp plugin list --status=active --format=table

# Tüm tema'ları list'le
wp theme list

# Çalışan WP versiyonunu doğrula
wp core version

# Site URL'lerini topluca değiştir (DB taşıma sonrası)
wp search-replace 'https://eski.com' 'https://yeni.com' --all-tables --dry-run
# --dry-run kaldırılıp gerçek çalıştır
```

---

_Son güncelleme: bu dosya manuel tutulur. Plugin/tema değiştiğinde §3-4'ü güncelleyin._
