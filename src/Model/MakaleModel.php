<?php
/**
 * MakaleModel sinifi veritabanindan makaleleri okumak icin kullanilir.
 * Bu model makale listesi ve tek makale sorgularini icerir.
 */
class MakaleModel {
    /** @var PDO $baglanti Veritabani baglantisi */
    private PDO $baglanti;

    /**
     * Kurucu metod, Veritabani singleton uzerinden PDO nesnesi alir.
     */
    public function __construct() {
        $this->baglanti = Veritabani::getInstance();
    }

    /**
     * Yayinda olan tum makaleleri getirir.
     * Makale listesi, basit alanlarla bir siralama yapar.
     *
     * @return array Makale dizisi (assoc array)
     */
    public function tumunuGetir(): array {
        $sql = "SELECT id, baslik, slug, ozet, yayim_tarihi, okuma_suresi
                FROM makale
                WHERE deleted_at IS NULL AND durum = 'yayinda'
                ORDER BY yayim_tarihi DESC";
        $stmt = $this->baglanti->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**
     * Slug'a gore tek bir makale getirir.
     *
     * @param string $slug Makalenin slug degeri
     * @return array|null Bulunan makale veya null
     */
    public function slugIleBul(string $slug): ?array {
        $sql = "SELECT id, baslik, slug, ozet, icerik, yayim_tarihi, okuma_suresi
                FROM makale
                WHERE slug = :slug AND deleted_at IS NULL
                LIMIT 1";
        $stmt = $this->baglanti->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        $sonuc = $stmt->fetch();
        return $sonuc ? $sonuc : null;
    }
}
