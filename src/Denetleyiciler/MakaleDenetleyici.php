<?php
/**
 * MakaleDenetleyici sinifi makale sayfalarini yonetir.
 * Listeleme ve detay goruntuleme islemlerini bu kontrolcu saglar.
 */
class MakaleDenetleyici {
    /** @var MakaleModel $model Makale verilerini saglayan model */
    private MakaleModel $model;

    /**
     * Kurucu metodunda MakaleModel nesnesi olusturulur.
     */
    public function __construct() {
        $this->model = new MakaleModel();
    }

    /**
     * Makale listesini dondurur.
     *
     * @param Request $request HTTP istegi
     * @param array $params Rota parametreleri (kullanilmiyor)
     * @return string Render edilmis HTML icerik
     */
    public function liste(Request $request, array $params = []): string {
        $makaleler = $this->model->tumunuGetir();
        // Gorunum sinifi ile makale listesi sablonu render edilir
        return Gorunum::render('makaleler/liste', ['makaleler' => $makaleler]);
    }

    /**
     * Belirli bir makalenin detayini dondurur.
     *
     * @param Request $request HTTP istegi
     * @param array $params Rota parametreleri (slug anahtari beklenir)
     * @return string
     */
    public function detay(Request $request, array $params): string {
        $slug = $params['slug'] ?? '';
        $makale = $this->model->slugIleBul($slug);
        if (!$makale) {
            // Makale bulunamadiginda 404 durum kodu ve hata mesaji
            http_response_code(404);
            return Gorunum::render('404', ['mesaj' => 'Makale bulunamadi']);
        }
        return Gorunum::render('makaleler/detay', ['makale' => $makale]);
    }
}
