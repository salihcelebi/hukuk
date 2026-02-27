<?php
/**
 * Gorunum sinifi, PHP view templating icin yardimci metodlar saglar.
 *
 * Gosterim dosyalari /views dizininde bulunur.
 * Gorunum::render() fonksiyonu, verilen sablonu bir dizi degisken ile
 * birlikte calistirir ve cikan HTML'i dondurur.
 */
class Gorunum {
    /**
     * Belirtilen sablonu isletir ve sonuc olarak HTML dondurur.
     *
     * @param string $sablon Yolu 'views/' klasoru icindeki sablon dosya yolu
     *                       ('konular/liste' gibi, .php uzantisi haric).
     * @param array $veri    Sablonda kullanilacak degiskenler.
     * @return string        Render edilmis HTML icerik.
     */
    public static function render(string $sablon, array $veri = []): string {
        $dosya = __DIR__ . '/../views/' . $sablon . '.php';
        if (!file_exists($dosya)) {
            // Sablon bulunamazsa basit mesaj dondur
            return '<!-- Sablon bulunamadi: ' . htmlspecialchars($sablon) . ' -->';
        }
        // Degiskenleri yerel scope'a aktar
        extract($veri);
        ob_start();
        include $dosya;
        return ob_get_clean();
    }
}
