# Hack Artığı Spam URL Temizliği (Search Console + .htaccess)

Eski site hacklendiğinde Google, malware'in ürettiği yüzlerce spam sayfayı
(casino/bahis/pharma vb.) indexledi. Temiz kurulumdan sonra bu sayfalar
sunucuda YOK → hepsi **404** dönüyor. Google bunları zamanla düşürür; aşağıdaki
adımlar süreci günlere indirir.

---

## 1. `.htaccess` — spam kalıplarını 410 Gone yap (toplu çözüm)

404 yerine **410 Gone** ("kalıcı olarak silindi") Google'a en net sinyaldir ve
recrawl'da hızlı düşürür. Aşağıdaki blok, URL'de bahis/pharma/sahte-ürün
kelimesi geçen TÜM istekleri yakalar. Türkçe eğitim/danışmanlık sitesinde bu
kelimeler meşru URL'lerde geçmez → güvenli.

**Nereye:** Production `public_html/.htaccess` dosyasında, `# BEGIN WordPress`
satırının HEMEN ÜSTÜNE ekle (WP bloğunun içine DEĞİL — WP onu yeniden yazabilir).

```apache
# === BEGIN Spam URL temizliği (hack artığı) — 410 Gone ===
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_URI} (casino|gambling|betting|\bbet\b|bahis|slot|poker|roulette|blackjack|baccarat|bingo|jackpot|wager|sportsbook) [NC,OR]
RewriteCond %{REQUEST_URI} (viagra|cialis|pharmacy|pharma|tadalafil|sildenafil|\bpills?\b|prescription) [NC,OR]
RewriteCond %{REQUEST_URI} (replica|rolex|louis-vuitton|escort|porn|xxx|adult-dating) [NC,OR]
RewriteCond %{REQUEST_URI} (essay-writing|write-my-essay|payday-loan|crypto-casino) [NC]
RewriteRule .* - [G,L]
</IfModule>
# === END Spam URL temizliği ===
```

> `[G]` = 410 Gone döndürür. `[L]` = kuralı sonlandırır.
> Ekledikten sonra test:
> `curl -s -o /dev/null -w "%{http_code}\n" "https://www.kaplanegitim.com/an-overview-of-live-dealer-games-at-online-casinos-canada/"`
> → **410** dönmeli.

---

## 2. Search Console → Removals (Kaldırmalar) — anında gizleme

410 "kalıcı sil" sinyali verirken, Removals aramadan **hemen** gizler (6 ay).
İkisi birlikte: 6 ay dolmadan Google kalıcı siler.

1. Search Console → sol menü **"Kaldırmalar" (Removals)**
2. **Yeni İstek → "Bu URL'yi içeren tüm adresler" (prefix)** sekmesi
3. Spam'de ortak bir kelime varsa önek olarak gir (ör. tek tek yerine kalıp).
   Prefix tek bir path öneki kabul eder; ortak öneki yoksa en görünür
   URL'leri **"Yalnızca bu URL"** ile tek tek gir.

---

## 3. Kalan spam'i bul: `site:` operatörü

Google'da şunları aratıp index'te ne kaldığını gör:
```
site:kaplanegitim.com casino
site:kaplanegitim.com -site:kaplanegitim.com/en   (TR tarafı)
site:kaplanegitim.com
```
Çıkan çöp URL'leri Removals'a gir. Birkaç hafta sonra tekrar arat — 410 + recrawl
ile sayı düşmeli.

---

## 4. (Opsiyonel) Kullanıcı sitemap'ini kapat

`wp-sitemap-users-1.xml` yazar kullanıcı adlarını dışa açar; kurumsal sitede
gereksiz. Tema `inc/seo.php` içinden `users` (ve istenirse `category`) provider'ı
kaldırılabilir — istenirse eklenir.

---

## Beklenen sonuç
- Spam URL'ler 410 döner, Removals ile aramadan kalkar.
- 2–6 hafta içinde `site:` taramasında çöp sonuçlar kaybolur.
- Yeni temanın gerçek sayfaları (12 alt-sitemap) indexlenmeye devam eder.
