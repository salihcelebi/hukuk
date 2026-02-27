<?php
/**
 * Response sinifi HTTP yaniti gondermeyi kolaylastirir.
 *
 * Metin veya JSON yanitlari dondurmek icin statik metodlar sunar.
 */
class Response {
    /**
     * Icerik, durum kodu ve header'lar ile yanit gonderir.
     *
     * @param string $content Gonderilecek icerik
     * @param int $status    HTTP durum kodu
     * @param array $headers Ek header'lar
     */
    public static function send(string $content, int $status = 200, array $headers = []): void {
        http_response_code($status);
        foreach ($headers as $key => $value) {
            header("$key: $value");
        }
        echo $content;
    }

    /**
     * Dizi veya nesneyi JSON olarak dondurur.
     *
     * @param mixed $data
     * @param int $status
     */
    public static function json($data, int $status = 200): void {
        header('Content-Type: application/json');
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        self::send($json, $status);
    }
}
