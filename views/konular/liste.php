<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Konu Listesi</title>
</head>
<body>
    <h1>Konu Listesi</h1>
    <p>Bu sayfa, veritabanından gelen tüm konuları listeler. Her bir konu başlığına tıklandığınızda konu detay sayfasına gidersiniz.</p>
    <?php
    // $konular değişkeni KonuDenetleyici tarafından doldurulur ve
    // her bir konu için 'baslik' ve 'slug' alanlarını içerir.
    ?>
    <?php if (!empty($konular)): ?>
        <ul>
        <?php foreach ($konular as $konu): ?>
            <li>
                <a href="/konular/<?php echo htmlspecialchars($konu['slug'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($konu['baslik'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Henüz konu bulunamadı.</p>
    <?php endif; ?>
</body>
</html>
