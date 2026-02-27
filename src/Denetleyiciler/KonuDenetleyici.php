<?php
/**
 * KonuDenetleyici sinifi, konulara ait sayfalar icin metodlar icerir.
 *
 * Bu denetleyici, KonuModel uzerinden veritabanindan veri ceker ve
 * Gorunum sinifi ile HTML sayfalarini render eder.
 */
class KonuDenetleyici {
    private KonuModel $model;

    public function __construct() {
        $this->model = new KonuModel();
    }

    /**
     * Konular listesini dondurur.
     *
     * @param Request $request HTTP istegi
     * @param array $params Rota parametreleri
     * @return string HTML icerik
     */
    public function liste(Request $request, array $params = []): string {
        $konular = $this->model->tumunuGetir();
        return Gorunum::render('konular/liste', ['konular' => $konular]);
    }

    /**
     * Belirli bir konunun detayini dondurur.
     *
     * @param Request $request
     * @param array $params Rota parametreleri (slug)
     * @return string
     */
    public function detay(Request $request, array $params): string {
        $slug = $params['slug'] ?? '';
        $konu = $this->model->slugIleBul($slug);
        if (!$konu) {
            http_response_code(404);
            return Gorunum::render('404', ['mesaj' => 'Konu bulunamadi']);
        }
        return Gorunum::render('konular/detay', ['konu' => $konu]);
    }
}
