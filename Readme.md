
# hukuk — ABD Hukuku İçerik + Avukat Dizini Platformu (Arayüz EN, Kod Tabanı TR)

Bu depo, FindLaw benzeri bir platformu çalıştırır:
- **Public web arayüzü (kullanıcının gördüğü her şey): İngilizce**
- **Kod tabanı standartları (klasör/dosya adları, iç modüller, admin panel): Türkçe**
- İçerik odağı: **Amerikan hukuku**
- Çekirdek bileşenler: **Hukuki konular & makaleler**, **avukat/hukuk bürosu dizini**, **arama**, **formlar**, **Soru-Cevap**, **hukuki belge/şablonlar**, **SEO landing sayfaları**

---

## Dil Politikası (Zorunlu)

### 1) Public Website (EN)
Kullanıcıya görünen tüm metinler ve UI etiketleri **İngilizce** olmak zorundadır:
- Navigasyon menüsü
- Başlıklar, butonlar, form etiketleri
- Kategori adları & alt başlıklar (taksonomi)
- Kullanıcıya gösterilen yasal uyarılar
- SEO title/description alanları

### 2) İç Sistem / Kod Tabanı (TR)
Geliştiriciye dönük tüm yapı **Türkçe** olmalıdır:
- Klasör ve dosya adları (ör: `konular/`, `makaleler/`, `soru-cevap/`)
- Admin dashboard menüsü + etiketleri (Türkçe)
- İç modül adları (ör: `icerikYonetimi`, `dizinYonetimi`, `dogrulama`, `talepler`)
- Veritabanı tablo/alan adları (Türkçe isimlendirme seçildiyse)

> Önemli: Uyum için kimliklerde **ASCII-only Türkçe** kullanın (ör: `sorumluluk-reddi`).

---

## URL Ağaç Yapısı (Public Routes)

Public routing temiz UX + SEO için tasarlanmıştır.

```

/
├─ konular/
│  ├─ [konu-slug]/
│  │  ├─ (genel)        → konu özeti, alt konular, öne çıkan içerikler
│  │  ├─ sss/           → konu SSS
│  │  ├─ rehberler/     → konuya bağlı rehber/makale listesi
│  │  └─ avukatlar/     → konuya uygun avukatlar (SEO kontrollü)
│  └─ (kategori sayfaları)  → örn: aile-hukuku/, ceza-hukuku/, is-hukuku/
│
├─ makaleler/
│  ├─ [makale-slug]/
│  └─ etiket/
│     └─ [etiket-slug]/
│
├─ avukat-bul/
│  ├─ (arama)           → uzmanlık + şehir + filtreler
│  └─ (sonuçlar)        → liste (+ opsiyonel harita)
│
├─ avukatlar/
│  ├─ [sehir-slug]/
│  │  ├─ (şehir landing) → şehirde öne çıkan uzmanlıklar + avukatlar
│  │  └─ [uzmanlik-slug]/ → SEO landing (en yüksek niyet)
│  └─ [avukat-profil-slug]/ → avukat profil (tek kanonik URL)
│
├─ hukuk-burolari/
│  ├─ [sehir-slug]/
│  │  └─ [uzmanlik-slug]/
│  └─ [buro-profil-slug]/
│
├─ uzmanlik-alanlari/
│  └─ [uzmanlik-slug]/  → “What is a Divorce Lawyer?” + şehir linkleri + içerikler
│
├─ belgeler/ (legal forms)
│  ├─ kategori/
│  │  └─ [kategori-slug]/
│  └─ [belge-slug]/
│
├─ soru-cevap/
│  ├─ sor/              → soru sor formu
│  ├─ [soru-slug]/      → soru detay + cevaplar
│  └─ konu/[konu-slug]/
│
├─ yardim/
│  ├─ sikca-sorulanlar/
│  ├─ iletisim/
│  └─ kullanim-sartlari/
│
└─ yasal/
├─ gizlilik-politikasi/
├─ cerez-politikasi/
├─ kvkk/
├─ sorumluluk-reddi/  → “Not legal advice” + avukat-müvekkil ilişkisi yok
└─ reklam-politikasi/ → sıralama/endorsement açıklaması

```

---

## Taksonomi Kaynağı (Kategoriler & Alt Dallar)

ABD hukuku taksonomisi şurada tutulur:

- `kategoriler.md`

Bu dosya şunları içerir:
- 50+ ana kategori
- her birinin alt dalları
- **İngilizce isimler + kebab-case slug'lar**

### Zorunlu davranış
- Uygulama bu taksonomiyi veritabanına **import/seed** etmelidir.
- Kategori/alt dallar **ağaç yapı** (parent → children) olarak saklanır.
- Slug’lar **benzersiz** olmalıdır (en azından kendi scope’unda).

---

## SEO Kuralları (Tartışmasız)

### 1) Sadece yüksek niyetli landing sayfalarını indexle
Index:
- `/avukatlar/{sehir}/{uzmanlik}/`
- `/uzmanlik-alanlari/{uzmanlik}/`
- `/konular/{konu}/`
- seçilmiş `/makaleler/{slug}/`

### 2) Faceted arama sayfalarını index dışı tut
Arama/filtre URL’leri (query parametreleri, çok filtre) için:
- `noindex`
- veya temiz landing sayfasına canonical

---

## Admin Dashboard (Türkçe)

Admin panel **tamamen Türkçe** olmalıdır. Önerilen yapı:

- `/panel/giris`
- `/panel/kontrol-paneli`
- `/panel/icerik-yonetimi` (konular, makaleler, sss, etiketler, belgeler)
- `/panel/dizin-yonetimi` (avukatlar, hukuk-burolari, uzmanlik-alanlari, lokasyonlar)
- `/panel/dogrulama` (avukat/büro doğrulama)
- `/panel/talepler` (lead yönetimi)
- `/panel/yorumlar` (moderasyon)
- `/panel/ayarlar` (seo, yasal metinler, roller)

---

## Veri Modeli Beklentisi (Üst Seviye)

Minimum modüller:
1) **İçerik (CMS)**: konular, makaleler, SSS, etiketler, belgeler
2) **Dizin**: avukat profilleri, büro profilleri, uzmanlık alanları, lokasyonlar
3) **Arama**: anahtar kelime + facet filtreleme
4) **Talepler/İletişim**: gelen talepler ve atama
5) **Moderasyon**: yorumlar / Soru-Cevap onay akışı
6) **Doğrulama**: avukat/büro doğrulama akışı

---

## Veritabanı Seed (Zorunlu)

Build sürecinde şu adımları yapan seed adımı bulunmalıdır:
1) `kategoriler.md` dosyasını parse et
2) kategorileri ve alt dalları DB’ye oluştur/güncelle
3) idempotent çalış: seed tekrar çalışınca kopya kayıt üretmesin

Önerilen:
- `slug` ile upsert
- dosya sırasını `sira` alanına yaz
- silmeden pasif etmek için `aktif_mi` alanı kullan

---

## Dev Notları / Konvansiyonlar

- Slug formatı: kebab-case (örn: `family-law`, `dui`, `child-custody`)
- Kod organizasyonu için Türkçe klasör adları kullan (ASCII-only)
- Public UI metinleri İngilizce localization dosyalarında tut
- Admin UI metinleri Türkçe localization dosyalarında tut

---

## Yasal Uyarı (Public Website)

Public web sitesinde görünür bir yasal uyarı bulunmalıdır:
- “This site provides general information and is not legal advice.”
- “Using this site does not create an attorney-client relationship.”
- “Listings/rankings are not endorsements unless explicitly stated.”

---

## Sonraki Adımlar

1) DB seed script’inin `kategoriler.md` dosyasını import ettiğini doğrula
2) SEO landing sayfalarını otomatik üret:
   - en popüler şehirler × en popüler uzmanlıklar
3) Moderasyon kuyrukları ekle:
   - yorumlar
   - Soru-Cevap cevapları
4) Doğrulama akışlarını ekle:
   - avukatlar
   - hukuk büroları

