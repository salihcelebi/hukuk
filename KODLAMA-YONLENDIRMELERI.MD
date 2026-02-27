# FindLaw benzeri ABD hukuku sitesi için PHP geliştirmeye nereden başlanır ve her bölüm nasıl kodlanır

## FindLaw’da tekrar eden sayfa kalıpları ve sizin URL ağacına karşılığı

FindLaw’da içerik (Learn About the Law) ve dizin (Lawyer Directory) sayfalarının çoğunda aynı “çekirdek CTA” kalıbı tekrar eder: kullanıcıdan **“Legal Issue”** ve **“City, ZIP Code or County”** alıp avukat aramaya yönlendiren form ve “More Options” menüsü (Name Search, Browse Legal Issues, Browse Law Firms, Support). Bu arama kutusu yalnızca dizinde değil, konu/merkez sayfalarda da görünür. citeturn10view2turn1view2turn24view1turn1view0

İçerik tarafında makaleler; **yazar + “Legally reviewed by” + “Last reviewed”**, ayrıca **“Legally Reviewed”** ve **“Fact-Checked”** bloklarıyla güven işaretleri taşır. “Fact-Checked” bölümünde, güncelleme tarihinin neyi ifade ettiği açıklanır ve çoğunlukla “contact an attorney in your area” çağrısı bulunur. citeturn24view3turn10view0turn10view1

Dizin tarafında şehir ve uzmanlık (practice area) kombinasyonlu landing sayfaları, sonuç listesinin üstünde **facet filtreler** taşır: yıldız puanı eşikleri, “virtual appointments”, “free consultation”, ödeme/credit card, “Super Lawyers”, mesafe ve dil gibi. Bu filtrelerin yapısı sizin `/avukat-bul/` ve `/avukatlar/{sehir}/{uzmanlik}` sayfalarınızın “çekirdek işlev seti”ni belirler. citeturn21view0

Avukat ve büro profil sayfaları; “Areas of Practice / Attorney Information (veya Firm Information) / Reviews / Photos / Contact” gibi bölümlere ayrılır. İletişim bölümünde lead formu (adı, soyadı, e‑posta, telefon, ihtiyaç metni vb.) ve form gönderimi için ayrı bir yasal uyarı bulunur; ayrıca bazı profillerde **state advertising restrictions** notu ve dizin uyarısı yer alır. citeturn25view0turn4view1turn27view0

```text
AJX, önce “ortak sayfa kalıbı” katmanını kur:
- Global üst arama bileşeni: legal issue + location + (opsiyon) topic search.
- Ortak layout: header/footer + SEO meta + breadcrumb + newsletter CTA alanları.
- Ortak “Find a Lawyer” CTA bloğu: içerik sayfalarında tekrar kullanılabilir olsun.
- Ortak profile “Jump to sections” (anchor menü) bileşeni: avukat/büro profillerinde ortak kullan.
- Ortak “sponsored” etiketleme mantığı: liste kartlarında ve üst sonuçlarda göster.

AJX, FindLaw’daki iki ana modülü ayır:
1) Icerik Modulu (konular/makaleler/sss/etiketler)
2) Dizin Modulu (avukatlar/burolar/uzmanliklar/lokasyon/yorum/lead)
Bu ayrım hem kod klasörlerinde hem DB sorgu katmanında gözüksün.
```

## Öncelikli PHP kodlama başlangıç sırası

Siz Render + Nginx + PHP‑FPM ile “front controller” tabanlı bir kurulumdasınız; bu yüzden önce **routing + controller iskeleti + view katmanı + DB erişimi** oturtulmalı. Sonra SEO açısından en çok trafik alacak “landing” tipleri (konu sayfaları ve şehir×uzmanlık dizin sayfaları) geliştirilmeli; en son kullanıcı üretimi içerikler (yorum, soru‑cevap) ve yönetim paneli zenginleştirilmeli.

Ayrıca, FindLaw’ın editorial policy metni “içerikler legal advice değildir, yayın öncesi editorial review yapılır” gibi güven kurallarını merkezi politika olarak ele alır. Siz de admin panelde (Türkçe) bu iş akışını “taslak → editör → yayın” şeklinde kodlamaya erken başlamalısınız (özellikle makaleler ve konu sayfaları). citeturn11view0turn12view1

```text
AJX, geliştirmeye şu sırayla başla (MVP → SEO trafikli → etkileşimli):

A) Cekirdek Altyapi (1–2 gün)
- public/index.php → tek giriş noktası.
- Router (dinamik segment destekli) + Request/Response sınıfları.
- Config/ENV okuyucu + PDO DB bağlantı havuzu.
- Basit View renderer (layout + partial + page template).
- Hata yönetimi: 404/500 sayfaları + loglama.

B) Icerik (Konular + Makaleler) (3–5 gün)
- /konular (liste) + /konular/{slug} (genel) + /makaleler/{slug}.
- SEO meta, canonical, breadcrumb, sitemap altyapısı.
- “Legally Reviewed” ve “Fact-Checked” alanları DB’de ve şablonda.

C) Dizin Arama (Avukat Bul) + SEO Landing (5–10 gün)
- /avukat-bul → form + sonuç (query parametreli, noindex).
- /avukatlar/{sehir}/{uzmanlik} → indexlenecek landing (temiz URL).
- Filtreler: puan, free consultation, virtual, credit card, payment plan, dil, mesafe.

D) Profil Sayfaları + Lead Form (5–8 gün)
- /avukatlar/{profil-slug} (kanonik) + /hukuk-burolari/{buro-slug}.
- Contact form → leads tablosu + e-posta gönderimi + spam koruması.

E) Yorum/İnceleme Sistemi (3–7 gün)
- Review yazma → e-posta doğrulama → 14 gün bekletme → yayın.
- Abuse report akışı + admin moderasyon kuyruğu.

F) Belgeler + Soru-Cevap (opsiyonel, MVP sonrası)
- /belgeler + /soru-cevap modülleri.
- Her ikisinde de “not legal advice” uyarıları ve moderasyon.
```

## Konular ve makaleler nasıl kodlanmalı

FindLaw içerik sayfalarında iki güçlü desen var: (1) konu landing sayfalarında kısa tanım + “Find a Qualified Attorney Near You” formu; (2) makalelerde TOC benzeri alt başlık listeleri ve “Legally Reviewed / Fact‑Checked” blokları. Örneğin “Criminal Law Basics” sayfası; breadcrumb, özet, byline, “Legally Reviewed”, “Fact‑Checked” ve ardından bölüm bölüm link listeleri içerir. citeturn24view3turn24view1turn24view0

Bu, sizin `/konular/{konu-slug}` “genel” sayfanızda: **konu tanımı + alt konular + öne çıkan içerikler + avukat CTA**, `/makaleler/{makale-slug}` sayfanızda ise: **byline + reviewer + last reviewed + TOC + içerik + ilgili içerikler + disclaimer** göstermeniz gerektiği anlamına gelir. FindLaw’ın “Fact‑Checked” açıklaması, “last updated” bilgisinin nasıl yorumlanacağını netleştirir; benzeri bir blok sizde de olmalı. citeturn10view0turn24view3

```text
AJX, “Icerik Modulu”nu şu şekilde kodla:

1) Veri modeli (minimum)
- konular: id, parent_id, title_en, slug, description_en, body_en, seo_meta, is_published, sort_order
- makaleler: id, topic_id, title_en, slug, excerpt_en, content_en, status, published_at
- makale_inceleme_meta: author_name_en, author_slug, reviewer_name_en, reviewer_slug, last_reviewed_at, fact_checked_at
- sss: topic_id, question_en, answer_en, is_published, sort_order
- etiketler: name_en, slug
- makale_etiket pivot

2) Routing & Controller (Türkçe dosya isimleri)
- Denetleyici: KonuDenetleyici
  - liste(): /konular
  - detay(): /konular/{konu-slug}
  - sss(): /konular/{konu-slug}/sss
  - rehberler(): /konular/{konu-slug}/rehberler
  - konuAvukatlari(): /konular/{konu-slug}/avukatlar (SEO kontrollü)
- Denetleyici: MakaleDenetleyici
  - detay(): /makaleler/{makale-slug}
  - etiket(): /makaleler/etiket/{etiket-slug}

3) View şablonları (public UI İngilizce)
- views/konular/liste.php:
  - “Browse Legal Topics” (EN) + arama widget’ı + kategori kartları.
- views/konular/detay.php:
  - H1: Topic title
  - kısa özet, alt konu kartları
  - “Find a Qualified Attorney Near You” CTA bloğu
  - “Featured Guides” (öne çıkan makaleler)
  - “Related Topics” ve “Popular Articles”
- views/konular/sss.php:
  - FAQPage schema altlığı (json-ld üretimini sakla)
- views/konular/rehberler.php:
  - makale listesi (sayfalama), filtre: etiket, alt konu
- views/makaleler/detay.php:
  - breadcrumb
  - byline: “By {author} | Legally reviewed by {reviewer} | Last reviewed {date}”
  - “Legally Reviewed” paneli + “Fact-Checked” paneli
  - TOC: content içinden h2/h3 çıkar (server-side parse) → sayfa içi anchor
  - içerik (LONGTEXT)
  - “Related Articles”
  - en altta “Not legal advice” uyarısı (yasal/sorumluluk-reddi linkli)

4) SEO kuralları
- /konular/{slug} ve /makaleler/{slug} index
- /konular/{slug}/avukatlar → varsayılan noindex + canonical
  - canonical hedefi: /avukatlar/{sehir}/{uzmanlik} landing (uygunsa)
- her sayfada canonical_url hesapla:
  - DB’de canonical_url varsa onu kullan
  - yoksa request URL’i canonical kabul et

5) Performans
- konu ve makale sayfalarını cachele:
  - 5–30 dk (yayın sıklığına göre)
- “latest” listelerini ayrı cache anahtarıyla tut

AJX, içerik yönetimini (admin TR) en baştan ekle:
- makale: taslak/yayin/arsiv
- reviewer/last_reviewed alanları zorunlu validasyonlu
- yayınlanmadan önce “Legally reviewed by” boş olmasın kuralı (opsiyonel ama önerilir)
```

## Avukat dizini, arama, filtreler ve profil sayfaları nasıl kodlanmalı

FindLaw dizininde üç kritik sayfa tipi var: (1) şehir landing (ör. “Browse Lake Oswego, Oregon Lawyers”) – sık görüntülenen legal issue’lar ve A‑Z; (2) şehir×uzmanlık sonuç sayfası – facet filtreler ve liste kartları; (3) avukat/büro profil sayfası – bölümlere ayrılmış detay ve iletişim formu. citeturn20view3turn21view0turn25view0turn4view1

Şehir×uzmanlık sayfasında filtre seti oldukça belirgin: yıldız puanı eşikleri + “virtual appointments/free consultation” + ödeme/credit card + “Super Lawyers” + mesafe + dil. Bu filtreleri DB’de “sorgulanabilir alanlar” olarak modelleyip indekslemek gerekir. citeturn21view0

Profil sayfalarında “Years of experience … based on state bar association data” açıklaması yer alır; ayrıca “Updated” tarihi, “Areas of Practice” listesi, “Awards and Honors”, “Education”, “Bar Admissions”, “Other Affiliations” gibi alanlar bulunur. citeturn7view1turn7view3turn25view0

```text
AJX, “Dizin Modulu”nu şu mimariyle kur:

1) URL stratejisi (sizin ağaca uyumlu, ama US lokasyon problemi çözülü)
- ABD’de aynı isimli şehirler var (Auburn AL / Auburn WA).
- sehir-slug formatı: "{city}-{state}" yani "lake-oswego-or", "auburn-al".
- uzmanlik-slug: kebab-case practice area (divorce, estate-planning, family-law).
- SEO landing: /avukatlar/{sehir-slug}/{uzmanlik-slug}/
- şehir landing: /avukatlar/{sehir-slug}/
- profil canonical: /avukatlar/{avukat-profil-slug}/ ve /hukuk-burolari/{buro-profil-slug}/

2) Arama sayfası
- /avukat-bul
  - GET: form göster (legal issue + location + filtreler)
  - GET + query: sonuçları aynı sayfada göster (noindex)
  - form submit → 302 redirect ile canonicalize:
    - eğer (sehir + uzmanlik) varsa → /avukatlar/{sehir}/{uzmanlik}/
    - yoksa → /avukat-bul?...
- Otomatik tamamlama (AJAX):
  - /api/otomatik/uzmanlik?q=
  - /api/otomatik/sehir?q=
  - response: [{label, slug, type}]

3) Sonuç/landing sayfası (en kritik)
- Denetleyici: AvukatAramaDenetleyici
  - sehirLanding(sehirSlug): /avukatlar/{sehir}/
  - sehirUzmanlikLanding(sehirSlug, uzmanlikSlug): /avukatlar/{sehir}/{uzmanlik}/
- Liste kartı alanları:
  - ad (firma veya avukat)
  - “Serving {city}” metni ve adres
  - rozetler: SuperLawyers sayısı, rating sayısı, years experience
  - CTA: Visit Website, Email Us, Call Today, Profile
  - “Sponsored” etiketi (is_sponsored=true)
- Filtreler (DB alanlarıyla birebir):
  - min_rating: 1..5 (and up)
  - has_virtual_appointments (bool)
  - has_free_consultation (bool)
  - has_payment_plans (bool)
  - accepts_credit_cards (bool)
  - is_super_lawyers_rated (bool)
  - distance_miles: 10/30/60/all (geo şart)
  - language: enum/list
- Sıralama:
  - sponsored önce (opsiyon)
  - sonra relevance (sehir+uzmanlik eşleşmesi)
  - sonra rating desc, review_count desc, years_experience desc

4) Geo / Mesafe filtresi
- city tablosunda lat/lng tut.
- avukat/firm lat/lng yoksa:
  - şehir merkezini kullan (fallback).
- Haversine ile mesafe hesapla ve filter uygula.
- performans için:
  - “distance filter” yalnızca sehir lat/lng varsa aktif olsun.
  - büyük data için arama indeksine (OpenSearch) geçiş planı çıkar.

5) Profil sayfası – Avukat
- Denetleyici: AvukatProfilDenetleyici
  - detay(profilSlug): /avukatlar/{profil-slug}
- Header alanları:
  - ad, firma linki (varsa)
  - rating + review count
  - address + call + email + website
  - “Updated: {date}”
  - “Years of experience” + açıklama metni
- Bölümler:
  - Areas of Practice (çoklu)
  - Attorney Information: Overview (bio), Awards, Education, Bar Admissions, Affiliations
  - Reviews (liste + özet)
  - Photos (media)
  - Contact (lead form)
- Contact form (POST):
  - First name, Last name, Email, Phone, ZIP, Message, Preferred contact method
  - CSRF token + rate limiting + honeypot
  - “Success” ekranı (PRG pattern: POST → 303 → GET)
  - leads tablosuna kayıt + e-posta gönder

6) Profil sayfası – Hukuk Bürosu
- Denetleyici: BuroProfilDenetleyici
  - detay(buroSlug): /hukuk-burolari/{buro-profil-slug}
- Bölümler:
  - Areas Of Practice, Firm Information (overview), Reviews, Attorneys, Photos, Contact
- Attorneys sekmesi:
  - firm_lawyers pivot ile listele
  - her avukata link: /avukatlar/{profil-slug}

7) State/City dizin sayfaları (opsiyonel ama FindLaw benzeri)
- /hukuk-burolari/{sehir}/ ve /hukuk-burolari/{sehir}/{uzmanlik}/
- /avukatlar/{state}/all-cities tarzı (istersen):
  - /avukatlar/{state}/tum-sehirler/ (alpha filtreli)

AJX, UI metinlerini İngilizce tut; ama kod/dosya adları Türkçe kalsın.
```

## İnceleme sistemi, doğrulama, kötüye kullanım bildirimi ve lead akışı

FindLaw inceleme sistemi “yıldız ortalaması”nın nasıl hesaplandığını açıklar; isteğe bağlı alt puanlar (Value, Quality of Service, Professional Competence) ana ortalamayı etkilemez. Ayrıca “Verified Client” gibi bir bayrak tanımlar ve review başına limit kuralı (bir kullanıcı bir avukat için bir review) getirir. citeturn8view0

Review sürecinde e‑posta doğrulaması, “attorney review period” ve 14 günlük bekletme mantığı bulunur; avukatlar yalnızca uygunsuz/spam gibi durumlarda engelleme/işaretleme sürecine dahil olur. Profil sayfasında ayrıca “report” mekanizması ve raporlama için e‑posta doğrulaması gibi alanlar görünür. citeturn8view0turn7view5

İletişim/lead tarafında FindLaw profillerinde ayrıntılı bir form (adı, soyadı, e‑posta, telefon, ihtiyaç metni, iletişim tercihi) bulunur; form altına Terms/Privacy kabul uyarısı eklenir. Sizde bu formun çıktısı “leads” tablosuna, atama ve CRM entegrasyonuna uygun şekilde kaydedilmelidir. citeturn25view0

```text
AJX, review + lead modülünü FindLaw benzeri güven/uyum kurallarıyla kur:

1) Reviews tablosu & durumlar
- reviews:
  - entity_type: 'lawyer'|'firm'
  - entity_id
  - reviewer_email (zorunlu, doğrulama için)
  - reviewer_name (opsiyon)
  - rating_overall (1..5)
  - rating_value (opsiyon)
  - rating_quality (opsiyon)
  - rating_competence (opsiyon)
  - title, body
  - status: pending|on_hold|approved|rejected
  - verified_client: bool
  - helpful_count: int
  - hold_until: datetime (14 gün)
- Kural: 1 e-posta = 1 entity için 1 review (unique constraint).

2) E-posta doğrulama (review)
- review oluşturulunca:
  - status = on_hold
  - verify_token üret, email gönder
- verify linkine tıklanınca:
  - email_verified_at yaz
  - hold_until = now + 14 days
  - (14 gün bitince) otomatik publish kuyruğu veya cron ile status=approved
- Avukat/büro panelinden:
  - “verified client” işaretleyebilsin
  - “report/flag as inappropriate” gönderebilsin

3) Abuse report
- review_reports:
  - review_id, reporter_email, reason, notes, status
- UI:
  - report formu e-posta zorunlu
  - reason seti: spam, offensive, personal info, promotes other business, etc.

4) Puan hesaplama
- overall_avg = AVG(rating_overall) sadece approved olanlardan
- optional rating’ler ayrı ortalama; overall ile karıştırma.

5) Lead (Contact) form
- leads:
  - source_page, practice_area_id, topic_id, lawyer_id/firm_id
  - name, email, phone, message, preferred_contact_method
  - status: new|in_progress|closed|spam
  - assigned_to_user_id
  - ip_address, user_agent
- Anti-spam:
  - rate limit (IP başına)
  - honeypot alan
  - basit içerik spam skoru (link sayısı vs)
- PRG:
  - POST /.../iletisim-gonder → 303 → GET ?sent=1

6) Admin panel (TR) ekranları
- /panel/yorumlar:
  - bekleyen/on-hold/onaylı/reddedilen listeleri
  - raporlananlar kuyruğu
- /panel/talepler:
  - lead listesi, atama, notlar, lead_events
```

## Belgeler, soru-cevap, yardım ve yasal/uyumluluk sayfaları nasıl kodlanmalı

FindLaw’ın “Legal Forms & Services” merkezinde iki ana hat var: (1) kategori/ürün (estate planning, business formation) ve (2) “Resources” içerikleri. Sayfa; “Need help? Contact a qualified attorney” CTA’sını açık biçimde gösterir. citeturn28view0turn28view1

FindLaw daha önce “Answers community” gibi soru‑cevap topluluğunu bloglarında anlatır; tüketiciler ve avukatların birbirine yön gösterdiği bir yapıdan söz eder (boards alanı bu ortamda 403 ile erişilemediği için birebir arayüzünü doğrulayamadık). citeturn15view0turn16view0

Yasal/uyumluluk tarafında FindLaw; “not legal advice”, “no attorney‑client relationship”, “we are not a law firm” gibi çekirdek uyarıları disclaimer sayfasında açıkça yazar. Ayrıca dizin listelemelerinde “state advertising restrictions” uyarısına ayrı bir sayfa ayırır ve eyalet bazlı reklam kurallarını listeler. citeturn12view1turn27view0

```text
AJX, “Belgeler + Soru-Cevap + Yasal” modüllerini şu şekilde kur:

BELGELER (/belgeler)
1) Routing
- /belgeler
- /belgeler/kategori/{kategori-slug}
- /belgeler/{belge-slug}

2) Şablon
- kategori sayfası: “Resources” gibi alt konu blokları + öne çıkan belgeler
- belge sayfası:
  - açıklama, adım adım kullanım, indir/görüntüle
  - “Need help?” CTA → /avukat-bul ve ilgili uzmanlık landing linkleri

3) DB
- belgeler: title_en, slug, summary_en, content_en, file_url, category_id, jurisdiction_state (opsiyon)
- belge_kategori: name_en, slug, parent_id (opsiyon)

SORU-CEVAP (/soru-cevap)
1) Routing
- /soru-cevap/sor (GET form, POST create)
- /soru-cevap/{soru-slug} (detay + cevaplar)
- /soru-cevap/konu/{konu-slug}

2) Moderasyon
- sorular: pending|approved|rejected
- cevaplar: pending|approved|rejected (avukat rolü veya editor onayı)
- her sayfada “Not legal advice” banner

3) Anti-spam
- rate limit + honeypot + e-posta doğrulama (opsiyon)

YARDIM (/yardim)
- /yardim/sikca-sorulanlar
- /yardim/iletisim
- /yardim/kullanim-sartlari
- içerikleri DB’den yönetilebilir yap (admin TR)

YASAL (/yasal)
- /yasal/gizlilik-politikasi
- /yasal/cerez-politikasi
- /yasal/sorumluluk-reddi  (not legal advice / no attorney-client)
- /yasal/reklam-politikasi  (sponsored, sıralama kuralları)
- /yasal/state-advertising-restrictions (US dizin uyumu için)

ZORUNLU UYGULAMA KURALI
- Public UI: İngilizce
- Admin + dosya/klasör isimleri: Türkçe (ASCII-only)

AJX, her public sayfanın footer’ına şu linkleri koy:
- Terms, Privacy, Disclaimer, Cookies
ve profil/contact formlarında bu linklere referans ver.

AJX, SEO için sitemap üret:
- /konular/* (published)
- /makaleler/* (published)
- /avukatlar/{sehir}/{uzmanlik} (whitelist’ten)
- /avukatlar/{profil} ve /hukuk-burolari/{profil}
```

