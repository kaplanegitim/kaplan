# Kaplan Eğitim — Web Sitesi Yenileme Projesi
## Yönetici Özeti Raporu

**Tarih:** Mayıs 2026 · **Durum:** Canlıya alındı (production'da çalışıyor)
**Site:** kaplanegitim.com

---

## 1. Olay — Malware / Güvenlik İhlali (Projenin Çıkış Noktası)
Proje, eski sitenin **zararlı yazılım (malware) ile enfekte olması** sonucu başladı. Eski WordPress kurulumu yıllardır güncellenmediği için (WP 4.9.13 + eski eklentiler) güvenlik açığı vermiş ve site ele geçirilmişti.

**Müdahale:**
- Site tarandı ve enfeksiyon tespit edildi (mevcut kurulum güvenilmez kabul edildi)
- Enfekte kurulumu "temizlemeye" çalışmak yerine **temiz, sıfırdan yeniden inşa** kararı alındı (en güvenli yol — gizli arka kapı/yeniden bulaşma riskini sıfırlar)
- İçerik, **temiz yedek + Wayback Machine arşiv kayıtları + sayfa export'ları** üzerinden kurtarıldı (orijinal görseller dahil, bulunabilenler)

## 2. Amaç
Güvenlik ihlali yaşamış, bakımsız ve güncellenemeyen eski sitenin; **temiz altyapıda**, modern, hızlı, çift dilli (TR/EN) ve yönetimi kolay özel bir tema ile **sıfırdan yeniden inşa edilmesi** ve güvenli şekilde canlıya alınması.

## 3. Başlangıç Durumu (Eski Site)
| Öge | Durum |
|---|---|
| Güvenlik | **Malware enfeksiyonu** — site ele geçirilmiş |
| WordPress | **4.9.13** (2019 sürümü, 5 ana sürüm geride, güncellenemiyordu) → açığın kök sebebi |
| PHP | 7.4 (destek sonu / EOL) |
| Tema | Insomnia + **WPBakery** (Visual Composer) page builder — ağır, bağımlı |
| İçerik | WPBakery shortcode'larına gömülü, bakımı zor; kısmen kurtarma gerektirdi |
| Çoklu dil | Eksik (sadece 4 EN sayfa) |
| SSL | Origin'de geçerli sertifika yok |

## 4. Yapılanlar — Yeni "Kaplan" Teması
Tamamen **özel kodlanmış**, page-builder bağımlılığı olmayan WordPress teması:

**Tasarım & Altyapı**
- Tek kaynaklı design token sistemi (renk/font/spacing), Inter + Manrope tipografi, modern cyan palet
- Klasik PHP şablon + theme.json hibrit yapı (Gutenberg uyumlu)
- Responsive, hafif, hızlı; minimal başlık tasarımı + ince köşe gradient'leri

**İçerik Yönetimi (Custom Post Types)**
- **Eğitimler** (18 program + paketler) · **Projeler/Vakalar** (8) · **Ekip** (8 kişi) · **Hero Slider** · **Müşteri Logoları** (13)
- Her biri admin panelinden yönetilebilir; detay sayfaları (eğitim/proje/ekip)

**Formlar**
- Özel AJAX form sistemi (Eğitim Talep + İletişim) → gönderimler panelde "Form Gönderimleri"nde saklanıyor + e-posta bildirimi
- Spam koruması (honeypot + nonce)

**Çift Dil (TR/EN) — Polylang**
- Arayüzün tamamı çevrildi (377 metin), 8 sayfa + 37 CPT öğesi EN'e çevrildi
- Dil-duyarlı linkler, bayraklı dil değiştirici (TR ↔ EN)

**Görsel İçerik**
- Anasayfa slider, portfolyo, ekip, müşteri logoları marquee
- İnfografik & Sunum proje galerileri (modal/lightbox ile büyütme)
- Gerçek proje görselleri yerleştirildi; bulunamayanlar için marka gradient **placeholder** sistemi (görsel eklenince otomatik geçer)

**SEO & Analitik (eklentisiz, tema içinde)**
- Meta description, Open Graph, Twitter Card, JSON-LD kurumsal schema
- Google Analytics 4 (Customizer'dan ID ile), preconnect, görsel lazy-load
- XML sitemap (WordPress core)

**Yönetim Kolaylığı (Customizer)**
- Telefon, e-posta, sosyal medya, GA4 ID panelden düzenlenebilir

**Yasal**
- KVKK / Gizlilik Politikası sayfası (TR + EN taslak, footer linki)

## 5. Canlıya Alma (Migrasyon)
- Geliştirme lokal ortamda (WAMP, WP 6.9.4) yapıldı
- Eski WP 4.9.13 kaldırıldı → production'a **güncel WordPress + PHP 8.1** taze kuruldu
- All-in-One WP Migration ile aktarım (108 MB paket) — URL'ler otomatik dönüştürüldü
- Eski tema + WPBakery + ilgili eklentiler temizlendi
- Permalink + doğrulama → **tüm sayfalar, formlar, çift dil çalışıyor** ✓

## 6. Teknik Kazanımlar
| | Eski | Yeni |
|---|---|---|
| WordPress | 4.9.13 (2019) | Güncel 6.x |
| PHP | 7.4 (EOL) | 8.1 |
| Tema | WPBakery bağımlı, ağır | Hafif özel tema, builder yok |
| Çift dil | Kısmi (4 EN sayfa) | Tam (UI + sayfa + içerik) |
| SEO | Eklenti yok | Tema içi tam meta + schema |
| Güvenlik | 5 sürüm açık, EOL PHP | Güncel çekirdek + PHP |

## 7. Kalan İşler (Operasyonel — sahibi/yönetici tarafında)
- [ ] **SSL:** AutoSSL (Let's Encrypt) + Cloudflare "Full (strict)"
- [ ] **LiteSpeed Cache** kurulumu (performans — sunucu LiteSpeed)
- [ ] **WP Mail SMTP** e-posta testi (form bildirimlerinin gitmesi için)
- [ ] **GA4** gerçek ölçüm kimliği girişi
- [ ] **KVKK metni:** şirket ünvanı/VKN doldurma + hukukçu onayı + yayın
- [ ] Sosyal medya linkleri (Instagram) ve eksik proje görsellerinin tamamlanması

---
*Yeni site canlıda yayında. Yukarıdaki operasyonel maddeler tamamlandığında geçiş %100 kapanmış olur.*
