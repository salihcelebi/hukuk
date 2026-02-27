<?php
/**
 * Veritabani sinifi, PDO kullanarak MySQL veritabani baglantisi saglar.
 *
 * Bu sinif, ortam degiskenlerinden baglanti parametrelerini okur ve
 * tek bir PDO nesnesi dondurur. Singleton yaklasimiyla birden fazla
 * baglanti nesnesi olusturulmasini onler.
 */
class Veritabani {
    private static ?PDO $pdo = null;

    /**
     * PDO nesnesini dondurur, yoksa olusturur.
     *
     * @return PDO
     * @throws PDOException
     */
    public static function baglanti(): PDO {
        if (self::$pdo === null) {
            $host = getenv('DB_HOST');
            $port = getenv('DB_PORT') ?: '3306';
            $db   = getenv('DB_DATABASE');
            $user = getenv('DB_USERNAME');
            $pass = getenv('DB_PASSWORD');
            $dsn  = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            self::$pdo = new PDO($dsn, $user, $pass, $options);
        }
        return self::$pdo;

        /**
     * getInstance() eski model çağrılarını desteklemek için alias.
     * Kodlama Talimatı: Singleton DB bağlantısı; getInstance alias eklendi.
     *
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        return self::baglanti();
    }
}
}
