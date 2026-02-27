<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Makale Listesi</title>
</head>
<body>
    <h1>Makale Listesi</h1>
    <p>Bu sayfa, tüm makaleleri listeler. Makale başlığına tıkladığınızda makale detay sayfasına gidersiniz.</p>
    <?php
    // $makaleler değişkeni MakaleDenetleyici'nin liste() metodunda doldurulur.
    // Her makale için 'baslik', 'slug' ve isteğe bağlı olarak 'ozet' alanları bulunur.
    ?>
    <?php if (!empty($makaleler)): ?>
        <ul>
        <?php foreach ($makaleler as $makale): ?>
            <li>
                <a href="/makaleler/<?php echo htmlspecialchars($makale['slug'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($makale['baslik'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
                <?php if (isset($makale['ozet']) && $makale['ozet'] !== ''): ?>
                    <p><?php echo htmlspecialchars($makale['ozet'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Henüz makale bulunamadı.</p>
    <?php endif; ?>
</body>
</html>
