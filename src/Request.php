<?php
/**
 * Request sinifi HTTP istegi hakkinda bilgi tutar.
 *
 * Bu sinif, HTTP metodunu, path (slash olmayan), query parametrelerini ve 
 * body'yi (POST) saklar. Basit bir wrapper olarak kullanilir.
 */
class Request {
    public string $method;
    public string $path;
    public array $query;
    public array $body;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        // Query ve path'i ayir
        $pos = strpos($uri, '?');
        if ($pos !== false) {
            $this->path = trim(substr($uri, 0, $pos), '/');
            parse_str(substr($uri, $pos + 1), $this->query);
        } else {
            $this->path = trim($uri, '/');
            $this->query = [];
        }
        // JSON POST destegi
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            $this->body = is_array($data) ? $data : [];
        } else {
            $this->body = $_POST;
        }
    }
}
