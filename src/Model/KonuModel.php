<?php
/**
 * KonuModel sinifi veritabani baglantisi araciligiyla konulari almak icin kullanilir.
 * Bu sinif FindLaw benzeri sistemde konu hiyerarsisini ve detaylarini sorgular.
 */
class KonuModel {
    /** @var PDO $baglanti Veritabani baglantisi */
    private PDO $baglanti;

    /**
     * Kurucu metodunda Veritabani singleton uzerinden baglanti alinir.
     */
    public function __construct() {
        // Veritabani sinifi PDO nesnesi dondurur
        $this->baglanti = Veritabani::getInstance();
    }

    /**
     * Tum yayinlanmis konulari getirir.
     *
     * @return array Gelen konularin listesi (her biri assoc array)
     */
    public function tumunuGetir(): array {
        $sorgu = "SELECT id, parent_id, baslik, slug, aciklama, sira
                  FROM konu
                  WHERE deleted_at IS NULL AND yayinda_mi = 1
                  ORDER BY sira ASC";
        $stmt = $this->baglanti->query($sorgu);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**
     * Verilen slug'a gore tek bir konuyu bulur.
     *
     * @param string $slug Aranan konunun slug'i
     * @return array|null Bulunan konu kaydi veya null
     */
    public function slugIleBul(string $slug): ?array {
        $stmt = $this->baglanti->prepare("SELECT id, parent_id, baslik, slug, aciklama, sira
                                         FROM konu
                                         WHERE slug = :slug AND deleted_at IS NULL
                                         LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $sonuc = $stmt->fetch();
        return $sonuc ? $sonuc : null;
    }
}
