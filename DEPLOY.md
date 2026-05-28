# Kaplan — Production Deployment Runbook

> ## ✅ DEPLOY TAMAMLANDI — Mayıs 2026
>
> Site **canlıda yayında**: `https://www.kaplanegitim.com/` · PHP 8.1 · WordPress 7.0 · özel kaplan teması.
> Migrasyon **All-in-One WP Migration (108 MB .wpress)** ile yapıldı; URL/path otomatik dönüştürüldü; tüm sayfa, form ve çift dil çalışıyor.
>
> Bu dosya artık **arşiv runbook** — gelecekteki benzer migrasyonlar için referans. Aşağıdaki adımlar uygulandı; üstü çizili olmasa da hepsi tamam. Güncel "ne kaldı" listesi `RAPOR.md` §7'de + `SEO-SPAM-CLEANUP.md` (Google'da hâlâ duran hack-artığı URL'ler için 410 + Search Console Removals).
>
> Lansman sonrası senkron kuralı: **kod → File Manager dosya yükle; içerik → canlıdan AIOWPM import.** İkisini karıştırma.

Lokal: `http://localhost/kaplan/`  →  Production: `https://www.kaplanegitim.com/`
Sunucu: cPanel + **LiteSpeed** (LSWS) + **Cloudflare** önde · PHP 8.1 (yükseltildi)

---

## 0. Lansman öncesi son kontrol (lokalde — TAMAMLANDI)
- [x] `blog_public = 1` (arama motoru görünürlüğü açık)
- [x] Dev sayfalar taslağa alındı: `bilesen-showcase`, `ornek-sayfa`
- [x] Tema sürümü senkron (style.css = KAPLAN_VERSION = 0.3.8)
- [x] Tema dosyalarında hardcoded `localhost` yok (CTA band CSS düzeltildi)
- [x] `DISALLOW_FILE_EDIT` açık (lokal wp-config — production wp-config'e de eklenecek)
- [ ] **Gizlilik Politikası / KVKK** sayfası (`gizlilik-ilkesi`, şu an draft) — gerçek metinle doldur & yayınla (form veri topluyor; yasal gereklilik)
- [ ] Kullanılmayan eklenti: `wpforms-lite` aktif görünüyor — custom AJAX form kullanıyoruz, gerekmiyorsa **devre dışı bırak**

---

## 1. Dosyaları yükle (FTP / cPanel File Manager)
- `wp-content/themes/kaplan/`  → production `wp-content/themes/kaplan/`
- `wp-content/uploads/`        → production (Media Library görselleri: client logoları, case/slide görselleri)
- `wp-content/plugins/polylang/` (+ kuracağın LiteSpeed Cache, WP Mail SMTP)
- **wp-config.php'yi YÜKLEME** — production'ın kendi wp-config'i (kendi DB bilgisi) kullanılır.

## 2. Veritabanı taşı
1. Lokal export:  cPanel/phpMyAdmin yerine lokalde **phpMyAdmin → kaplan_local → Export (SQL)**
2. Production'da boş bir DB + kullanıcı oluştur (cPanel → MySQL Databases)
3. Import et (phpMyAdmin → Import)

## 3. URL search-replace (KRİTİK — serileştirme-güvenli olmalı)
DB'de ~27 absolute `localhost/kaplan` URL var (menü, slide/footer linkleri, görsel yolları).
**Düz SQL REPLACE KULLANMA** (serialized veriyi bozar). Şu yöntemlerden biri:

**A) WP-CLI (önerilen, cPanel Terminal'de):**
```bash
wp search-replace 'http://localhost/kaplan' 'https://www.kaplanegitim.com' --all-tables --precise
wp search-replace 'localhost/kaplan' 'www.kaplanegitim.com' --all-tables --precise
wp cache flush
```

**B) Eklenti:** "Better Search Replace" kur → `http://localhost/kaplan` → `https://www.kaplanegitim.com` (tüm tablolar, "Run as dry run" ile önce dene).

**C) Dahili script `migrate-urls.php`** (WP-CLI yoksa — kök dizinde hazır):
- Production köküne yükle → tarayıcıda aç (admin girişli): `https://www.kaplanegitim.com/migrate-urls.php`
- Varsayılan **DRY-RUN**: ne değişeceğini raporlar (lokalde test: 28 satır — options 4 + postmeta 21 + posts 2 + users 1; **guid hariç tutulur**, WP kuralı).
- Rapor doğruysa dosyada `$DRY_RUN = false` yap → tekrar çalıştır (uygular).
- **İş bitince `migrate-urls.php`'yi SİL.**
- Serileştirme-güvenli (Polylang/theme_mod serialized verisini bozmaz).

> Not: lokal **alt-dizin** (`/kaplan`), production **kök** (`/`). Yukarıdaki replace bunu da çözer.

## 4. Production wp-config.php ayarları
```php
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);
define('DISALLOW_FILE_EDIT', true);
define('WP_AUTO_UPDATE_CORE', 'minor');
// Cloudflare arkasında doğru protokol/IP:
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']==='https') $_SERVER['HTTPS']='on';
// Yeni güvenlik salt'ları üret: https://api.wordpress.org/secret-key/1.1/salt/
```
- `table_prefix` lokaldekiyle aynı olmalı (`wp_`) — yoksa import uyumsuz olur.

## 5. Permalink & rewrite
- WP Admin → Ayarlar → **Kalıcı Bağlantılar → Kaydet** (rewrite kurallarını yenile; `.htaccess` üretilir, sitemap/robots düzelir).
- Polylang dil URL yapısı korunur (TR kök, EN `/en/`).

## 6. Eklenti kurulum & ayar (production)
- **LiteSpeed Cache:** kur → Cache ON → CDN → Cloudflare API (purge senkronu) → Image Opt (WebP) ON → CSS/JS Minify ON (Combine kapalı başla). Sonra "Empty All Caches".
- **WP Mail SMTP:** Other SMTP → Host `localhost` · 587 TLS (veya 465 SSL) · user `bilgi@kaplanegitim.com` · Force From ON → **Email Test** gönder.
- **Polylang:** dil ayarları DB ile geldi; Diller sekmesinden TR/EN ve menü konumlarını doğrula.

## 7. DNS & SSL (Cloudflare)
- Domain Cloudflare'de → SSL/TLS **Full (strict)** · Always Use HTTPS ON.
- SPF + DKIM kayıtları **Cloudflare DNS'e** eklenmeli (mail deliverability — cPanel → Email Deliverability'deki değerleri kopyala).

## 8. Lansman sonrası kontrol listesi
- [ ] Anasayfa, 8 iç sayfa, EN sayfalar (`/en/...`), CPT detayları (`/egitim/...`, `/proje/...`, `/ekip/...`) → 200
- [ ] Hero slider + RPA görseli + 2 gradient placeholder slide
- [ ] Dil değiştirici (bayrak) TR↔EN doğru
- [ ] Form gönder → admin'de "Form Gönderimleri"ne düşüyor + mail geliyor
- [ ] Footer/topbar telefon, e-posta, sosyal linkler (Customizer)
- [ ] **GA4:** Özelleştir → Analitik → `G-...` ID gir (canlıdaki gerçek ID)
- [ ] Görsel/CSS/JS yükleniyor (404 yok), Media Library görselleri görünüyor
- [ ] `https://www.kaplanegitim.com/wp-sitemap.xml` çalışıyor → Google Search Console'a gönder
- [ ] Mixed-content (http) uyarısı yok (tarayıcı konsolu)
- [ ] Cache testi: çıkış yapıp sayfa hızı (LSCache hit)

## 9. Geri dönüş (rollback)
- Import öncesi production DB + dosya yedeği al (cPanel → Backup). Sorun olursa yedekten dön.
