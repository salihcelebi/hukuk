<?php
// Kodlama Talimati: Front controller ve otomatik sinif yukleme
// src ve alt klasorlerdeki siniflari otomatik yukler. Bu sayede elle require yapilmaz.
// Turkish comments for developers; public UI remains English.
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../src/' . $class . '.php',
        __DIR__ . '/../src/Denetleyiciler/' . $class . '.php',
        __DIR__ . '/../src/Model/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Veritabani saglik kontrolu fonksiyonu.
// Kodlama Talimati: Veritabani katmani kullanilir. Arayuz Talimati: hata detaylari kullaniciya gosterilmez.
function db_status(): bool {
    try {
        // Veritabani::baglanti() tekil bir PDO nesnesi dondurur
        $pdo = Veritabani::baglanti();
        return true;
    } catch (Throwable $e) {
        // Hata loglanabilir; kullaniciya aciklanmaz
        return false;
    }
}

$request = new Request();
$router  = new Router();

// Denetleyiciler (Controllers)
$konuCtrl   = new KonuDenetleyici();
$makaleCtrl = new MakaleDenetleyici();

// Rotalar tanimlama
// Ana sayfa ve konular listesi
$router->get('', function ($req) use ($konuCtrl) {
    return $konuCtrl->liste($req);
});
$router->get('konular', function ($req) use ($konuCtrl) {
    return $konuCtrl->liste($req);
});
// Konu detay sayfasi, slug dinamik parametre
$router->get('konular/(?P<slug>[^/]+)', function ($req, $params) use ($konuCtrl) {
    return $konuCtrl->detay($req, $params);
});
// Makale listesi
$router->get('makaleler', function ($req) use ($makaleCtrl) {
    return $makaleCtrl->liste($req);
});
// Makale detay sayfasi
$router->get('makaleler/(?P<slug>[^/]+)', function ($req, $params) use ($makaleCtrl) {
    return $makaleCtrl->detay($req, $params);
});
// Saglik durumu /health ucu
$router->get('health', function () {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'ok',
        'db'     => db_status() ? 'ok' : 'fail',
    ]);
    return null;
});

// Yonu (dispatch) istegi isleyip yanit olusturur
$content = $router->dispatch($request);
if ($content === null) {
    // Eslesmeyen rota icin 404 sayfasi. Arayuz Talimati: Ingilizce 404 sayfasi, teknik detay yok.
    http_response_code(404);
    echo Gorunum::render('404');
} else {
    // Normal sayfalarda Response sinifi araciligiyla icerik gonderilir.
    Response::send($content);
}
