
AJX, AŞAĞIDAKİ TALİMATLARI EKSİKSİZ UYGULA. BU REVİZYON, “30 MADDELİK HATA KAÇIRMAMA POLİTİKASI”NA TAM UYUMLUDUR.
KURAL: Public UI %100 İngilizce. Kod tabanı + admin panel + dosya/klasör adları Türkçe (ASCII-only).

──────────────────────────────────────────────────────────────────────────────
0) AJX ÇALIŞMA PLANI (xx kuralı: önce plan)
──────────────────────────────────────────────────────────────────────────────
AJX, şu sırayla ilerle ve her adım bitince küçük commit at:

1) Çekirdek Boot + Ortam Politikası
   - APP_ENV zorunlu (dev|staging|prod), safe-fail
   - Feature flags (DEV_OVERLAY, DEBUG_SQL, DEBUG_HTTP)
   - Config validasyonu + bağımlılık kontrolü (extensions)

2) Global Hata Yakalama Katmanı
   - error_reporting(E_ALL)
   - set_error_handler → ErrorException
   - set_exception_handler → tüm Throwable + previous zinciri
   - register_shutdown_function → fatal/parse/compile yakala
   - Output buffering + double-fault guard
   - Correlation ID üret + X-Correlation-Id header

3) Log + Karantina + Dedupe
   - JSON line log standardı
   - errors_quarantine tablosu (asla kayıp yok)
   - Dedup (aynı hata 2 sn içinde birleştir)
   - Admin incident banner (fatal artışı)

4) “No naked call” Wrapper Mimarisi
   - DB/HTTP/FS/Cache/Queue/Mail/Include/Require çağrıları wrapper ile
   - safe_call(module, action, fn, context) standardı
   - context merge + rethrow zinciri (RuntimeException(..., 0, $e))

5) Router + Request/Response + Middleware
   - Public routes + Panel routes
   - Middleware: auth, csrf, rateLimit, seo, cache
   - 404/405/500 standart response

6) Modül Modül Uygulama (sırasıyla)
   - /healthz (internal) + /health (prod’da auth)
   - İçerik: konular + makaleler + sss + etiket
   - Dizin: /avukat-bul + /avukatlar/{sehir}/{uzmanlik} + profil
   - Lead form + spam koruması + PRG
   - Yorum + report + moderasyon kuyrukları
   - Belgeler + Soru-Cevap
   - SEO: canonical/meta/json-ld/sitemap/robots

7) Render + Aiven entegrasyon doğrulaması
   - Nginx envsubst, supervisord sırası
   - DB bağlantısı + migration/seed idempotent
   - Logların Render’da görünürlüğü

──────────────────────────────────────────────────────────────────────────────
1) ORTAM / POLİTİKA (30 MADDEYİ KOD STANDARTI YAP)
──────────────────────────────────────────────────────────────────────────────
AJX, aşağıdaki 30 maddeyi “çekirdek zorunlu standart” olarak kodla:

A) Modlar, Ortamlar, Politika (1–6)
1) APP_ENV zorunlu: dev|staging|prod. Varsayılan dev OLAMAZ.
   - .env veya ENV yoksa uygulama “safe fail” ile ayağa kalkmasın.
2) dev: hatalar arayüzde görünür + karantinaya yazılır.
   prod: arayüzde görünmez, sadece admin panelinde görünür.
3) Her istek için correlation_id üret; response header’a ekle: X-Correlation-Id
4) Hata sınıflandırması zorunlu:
   - source = code|dependency|integration|config|security
   - severity = info|warn|error|fatal
5) Prod privacy: stack trace/SQL/endpoint asla gösterme; sadece CID göster.
6) Feature-flag’ler sadece dev/staging’de aktif:
   - DEV_OVERLAY, DEBUG_SQL, DEBUG_HTTP prod’da otomatik kapalı.

B) PHP Error Yakalama Katmanı (7–12)
7) error_reporting(E_ALL) her ortamda açık.
   - display_errors dev=1, prod=0
8) set_error_handler ile NOTICE/WARNING/DEPRECATED/STRICT dahil hepsini ErrorException’a çevir.
9) set_exception_handler: tüm Throwable yakalanır; getPrevious zinciri korunur.
10) register_shutdown_function: fatal/parse/compile hatalarını error_get_last ile yakala.
11) Output buffering: overlay basmadan önce buffer temizlemek için ob_start kullan.
12) Double-fault guard: handler içi handler patlarsa minimal error_log + CID yaz.

C) Katmanlı Try-Catch Disiplini (13–18)
13) No naked call: DB/HTTP/FS/Cache/Queue/Mail/Include/Require wrapper ile sarılacak.
14) Wrapper standardı:
   - safe_call(module, action, fn, context)
   - her catch “context merge” ile üst katmana aktarır.
15) İç içe sarmalama:
   - (a) pre-check try-catch
   - (b) call try-catch
   - (c) wrap/rethrow try-catch
16) İstisna zinciri zorunlu:
   - throw new RuntimeException("...", 0, $e)
17) Context minimal ama yeterli:
   - module, action, dependency_name, url/sql/path, user_id, payload_hash
18) Yakalanan hata normalize edilip tek formatta karantinaya yazılacak (JSON schema).

D) Bağımlılıklar ve Konfig (19–22)
19) Boot sırasında kontrol:
   - composer autoload var mı?
   - required PHP extensions var mı (pdo, mbstring, curl, json vb.)
20) .env/config validasyonu:
   - zorunlu anahtarlar yoksa “config error” olarak karantinaya yaz + dev overlay
21) Versiyon uyumu:
   - PHP sürümü, extension sürümü, vendor paket sürümleri dev’de panelde görünür
22) Health endpoints:
   - /healthz (internal, auth’suz ama sadece internal amaçlı)
   - /health (prod’da auth ile)
   - DB/Cache/Queue ping

E) Gözlemlenebilirlik (23–26)
23) Karantina deposu:
   - DB errors_quarantine tablosu + JSON context + trace. Kayıp yok.
24) Log formatı:
   - JSON line log (CID, env, severity, source, message, file, line, trace_short)
25) Dedupe:
   - aynı hatayı 2 sn içinde birleştir (spam engelle)
26) Alert hook:
   - staging/prod’da fatal artarsa admin panelde “incident banner” göster

F) Güvenlik ve UI Ayrımı (27–30)
27) Dev overlay’de bile XSS koruması: her değer escape, ham HTML yok.
28) Prod’da stack trace asla: sadece “Bir hata oluştu. CID: ...” + 500.
29) Admin panel erişimi:
   - role-based auth + opsiyonel IP allowlist + opsiyonel 2FA
30) Admin panel audit:
   - kim hangi hatayı görüntüledi → admin_audit_logs kaydı

──────────────────────────────────────────────────────────────────────────────
2) MİMARİ REVİZYONU (ÖNCEKİ TALİMATLARIMIN TAM GÜNCEL HALİ)
──────────────────────────────────────────────────────────────────────────────
AJX, mimariyi şu katmanlarla kur ve HER DIŞ ÇAĞRIYI wrapper’a taşı:

- public/index.php (Front Controller)
- src/Cekirdek/
  - Ortam.php (APP_ENV + flags + safe fail)
  - Yapilandirma.php (config + env validasyon)
  - Istek.php / Yanit.php
  - Yonlendirici.php (Router)
  - Middleware/
  - Hata/
    - HataYakalama.php (handlers)
    - HataNormalize.php (source/severity + context)
    - Karantina.php (errors_quarantine yazıcı)
  - Log/
    - JsonLogger.php (JSON line log)
  - Guvenlik/
    - Csrf.php, RateLimit.php, Yetki.php
  - Yardimcilar/
    - Hash.php (payload_hash), Slug.php, Escape.php

- src/Denetleyiciler/ (TR isimler)
- src/Servisler/
- src/Depolar/
- views/ (public EN metinler)
- panel/ (admin TR metinler + yetkilendirme)

KABUL KRİTERİ (kalite):
- “No naked call” ihlali 0 olacak.
- Prod’da kullanıcıya SQL/trace asla görünmeyecek.
- Her hata: JSON log + errors_quarantine kaydı + CID.
- Admin panelde hata listesi/inceleme + audit log.

──────────────────────────────────────────────────────────────────────────────
3) FRONT CONTROLLER (public/index.php) — HATA KAÇIRMAYAN BOOT
──────────────────────────────────────────────────────────────────────────────
AJX, index.php akışını aşağıdaki sırayla yaz:

1) ob_start() (madde 11)
2) correlation_id üret:
   - RFC4122 UUID veya güvenli random.
   - Request’e koy, Response header’a koy.
3) Ortam/flags yükle:
   - APP_ENV zorunlu; yoksa safe fail (madde 1)
   - prod’da debug flag’leri otomatik kapat.
4) error_reporting(E_ALL)
   - display_errors dev=1, prod=0
5) HataYakalama’yı kur:
   - set_error_handler → ErrorException
   - set_exception_handler → Throwable
   - register_shutdown_function → fatal
   - double-fault guard (madde 12)
6) Bağımlılık kontrolü:
   - vendor/autoload.php var mı? yoksa dependency error → karantina + safe fail
   - extensions listesi kontrol
7) Config validasyonu:
   - DB_HOST/DB_NAME/DB_USER/DB_PASS vb. zorunlular
   - eksikse config error → karantina
8) Router dispatch:
   - middleware pipeline
   - controller action
9) Response üret:
   - header + status + body
   - X-Correlation-Id her zaman eklenecek.
10) Dev overlay:
   - sadece dev/staging ve DEV_OVERLAY=1 ise
   - buffer’da oluşan çıktıyı sanitize edip overlay bas.
11) Prod hata response:
   - “Bir hata oluştu. CID: {cid}”
   - 500

──────────────────────────────────────────────────────────────────────────────
4) WRAPPER STANDARDI (NO NAKED CALL) — safe_call ZORUNLU
──────────────────────────────────────────────────────────────────────────────
AJX, aşağıdaki wrapper’ları yaz ve her dış çağrıyı bunlarla yap:

- safe_db(module, action, sql, params, context)
- safe_http(module, action, method, url, opts, context)
- safe_fs(module, action, path, fn, context)
- safe_cache(module, action, key, fn, context)
- safe_mail(module, action, payload, context)
- safe_include(module, action, file, context)

Ve hepsi içerde şunu kullanacak:
- safe_call(module, action, fn, context)

safe_call davranışı:
1) context’i normalize et + payload_hash üret.
2) try:
   - pre-check
   - call
3) catch(Throwable $e):
   - normalize error: source/severity + context + trace_short
   - JSON log yaz
   - errors_quarantine kaydet (madde 23)
   - rethrow: throw new RuntimeException("ModuleAction failed", 0, $e)

Dedupe:
- Aynı hata imzası (message+file+line+topFrame) 2 sn içinde birleşsin (madde 25)

──────────────────────────────────────────────────────────────────────────────
5) KARANTİNA TABLOLARI (AIVEN MYSQL) + MİGRASYON
──────────────────────────────────────────────────────────────────────────────
AJX, veritabanına aşağıdaki tabloları ekle (migration ile):

1) errors_quarantine
- id (PK)
- correlation_id (varchar)
- app_env (dev|staging|prod)
- source (code|dependency|integration|config|security)
- severity (info|warn|error|fatal)
- module (varchar)
- action (varchar)
- message (text)
- file (varchar)
- line (int)
- trace_json (longtext)       // full trace (prod’da sadece panelde)
- context_json (longtext)     // normalized context
- signature_hash (char(64))   // dedupe için
- dedupe_count (int)
- first_seen_at (datetime)
- last_seen_at (datetime)
- created_at, updated_at

2) admin_audit_logs
- id
- admin_user_id
- action (ör: "error_view", "error_export", "error_resolve")
- target_type ("errors_quarantine")
- target_id
- correlation_id
- ip_address
- user_agent
- created_at, updated_at

KABUL:
- Hata olunca kesinlikle errors_quarantine’a kayıt düşecek.
- Prod’da kullanıcı sadece CID görür; detay panelde.

──────────────────────────────────────────────────────────────────────────────
6) HEALTH ENDPOINTS (Render uyumlu)
──────────────────────────────────────────────────────────────────────────────
AJX, iki endpoint yaz:

- GET /healthz (internal)
  - auth yok, ama minimal bilgi:
  - {status:"ok", time, build}
  - DB ping başarısızsa status:"fail"

- GET /health (prod’da auth zorunlu)
  - db, cache, queue ping sonuçları
  - dependency versions (madde 21) sadece dev/staging’de

KABUL:
- /healthz Render healthcheck için stabil.
- /health prod’da açık değil; auth şart.

──────────────────────────────────────────────────────────────────────────────
7) ROUTE/MODÜL TALİMATLARI — HER MODÜLDE HATA POLİTİKASI UYGULA
──────────────────────────────────────────────────────────────────────────────
AJX, aşağıdaki route’ları implement ederken her controller action’da şu standardı uygula:
- correlation_id context’e ekle
- safe_call kullan
- hata olursa: prod’da 500 + CID; dev’de overlay + CID

MODÜLLER (özet kalite revizyonu):
A) /konular
- KonuDenetleyici: liste, detay, sss, rehberler, avukatlar (SEO kontrollü)
- DB çağrıları: safe_db
- sss sayfası: FAQPage JSON-LD (EN)
- /avukatlar alt sayfası: noindex + canonical/redirect

B) /makaleler
- MakaleDenetleyici: detay, etiket
- TOC üretimi server-side:
  - HTML parse güvenli (script/style temizle)
- “Legally Reviewed / Fact-Checked / Last reviewed” alanları zorunlu kontrol (editor policy)

C) /avukat-bul ve /avukatlar/*
- AvukatAramaDenetleyici:
  - /avukat-bul parametreli sayfalar noindex
  - valid sehir+uzmanlik varsa 302 ile canonical landing
- /avukatlar/{sehir}/{uzmanlik} landing:
  - facet filtreler whitelist
  - SQL ORDER BY whitelist
  - rate limit: abuse’a karşı
  - hata olursa: prod generic + CID

D) Profil sayfaları
- AvukatProfilDenetleyici, BuroProfilDenetleyici
- lead form POST:
  - CSRF + rate limit + honeypot + PRG
  - safe_db transaction + lead_events
  - mail queue varsa safe_mail

E) Yorumlar/Report
- Moderasyon akışı + 14 gün hold
- report abuse: safe_db + rate limit
- “Verified client” sadece panel yetkisi ile

F) Belgeler + Soru-Cevap
- UGC moderasyonlu
- public sadece approved
- her sayfa disclaimer (EN)

──────────────────────────────────────────────────────────────────────────────
8) ADMIN PANEL (TR) — HATA PANELİ + AUDIT + INCIDENT BANNER
──────────────────────────────────────────────────────────────────────────────
AJX, panelde şu ekranları ekle:

- /panel/hatilar (hatalar)
  - filtre: env, severity, source, module
  - liste: message, file:line, first_seen, last_seen, dedupe_count, correlation_id
  - detay: context_json + trace_json (sadece admin)
  - aksiyonlar: “resolved” etiketi (opsiyon), export (opsiyon)
  - her görüntüleme admin_audit_logs kaydı (madde 30)

- Incident banner:
  - staging/prod’da son 15 dk fatal sayısı eşik aşarsa banner göster (madde 26)

Güvenlik:
- RBAC + opsiyonel IP allowlist + opsiyonel 2FA (madde 29)

──────────────────────────────────────────────────────────────────────────────
9) RENDER İÇİN DEPLOY TALİMATI (Nginx + Supervisord) — HATA KAÇIRMASIN
──────────────────────────────────────────────────────────────────────────────
AJX, Render container için:

1) Dockerfile:
- gettext-base (envsubst) yüklü
- php-fpm + nginx + supervisor
- prod’da display_errors kapalı

2) Nginx:
- dinamik PORT: envsubst ile template → gerçek conf üret
- envsubst Nginx başlamadan önce kesin çalışacak

3) Supervisord:
- php-fpm önce, nginx sonra
- stdout/stderr logları container loglarına aksın
- restart policy

4) Sağlık:
- Render healthcheck: /healthz
- Build sırasında config eksikse safe fail (exit 1) ve karantinaya yaz (mümkünse dosya log)

──────────────────────────────────────────────────────────────────────────────
10) TEST / KALİTE GATE (EN UFAK HATA KAÇMASIN)
──────────────────────────────────────────────────────────────────────────────
AJX, aşağıdaki kalite kapılarını koy:

- Unit test:
  - Router param parse
  - HataYakalama (error_handler → ErrorException)
  - safe_call normalizasyon + dedupe
- Integration test:
  - /healthz (db ok/fail)
  - örnek konu/makale render
- Static analysis:
  - phpstan seviyesi yükselt
- Format:
  - PSR-12 (php-cs-fixer)
- Güvenlik test:
  - CSRF yoksa POST reddet
  - rate limit tetikle
  - overlay escape testi (XSS)

KABUL:
- Bu gate’lerden biri kırılırsa deploy etmeye çalışma.

──────────────────────────────────────────────────────────────────────────────
11) SON KONTROL CHECKLIST (MUTLAKA)
──────────────────────────────────────────────────────────────────────────────
AJX, deploy öncesi şu checklist’i tek tek doğrula:

- APP_ENV yoksa safe fail çalışıyor mu?
- prod’da stack trace asla görünmüyor mu?
- Her response’da X-Correlation-Id var mı?
- errors_quarantine kayıt kaçırmadan doluyor mu?
- Dedupe 2 sn içinde birleştiriyor mu?
- /healthz Render’da 200 dönüyor mu?
- /health prod’da auth istiyor mu?
- Panelde hata görüntüleme audit log düşüyor mu?
- “No naked call” ihlali kalmadı mı?
- Public UI metinleri %100 İngilizce mi, panel %100 Türkçe mi?

BİTTİ.
```

```text id="revize-02"
AJX, NOT: Bu revizyonu uygularken “kodu daha kaliteli yaz” hedefi için şu iki ek kuralı da getir:

1) Her modülde “Sözleşme (contract) dokümanı” üret:
   - Girdi doğrulama kuralları
   - Yetki gereksinimi
   - Hata kodları + kullanıcı mesajı (EN) + panel mesajı (TR)
   - SEO davranışı (index/noindex/canonical)

2) Her kritik akışın “başarı + başarısızlık” senaryosunu yaz:
   - Lead gönder: DB ok, DB fail, mail fail, rate limit fail
   - Makale göster: published, draft, yok
   - Landing filtre: whitelist dışı parametre
   - Review: email verify, hold, abuse report

Bu iki kuralı her commit’te README/CHANGELOG’a kısa not düş.
```
