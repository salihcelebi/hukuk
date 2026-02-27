<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Konu Detayı - <?php echo isset($konu['baslik']) ? htmlspecialchars($konu['baslik'], ENT_QUOTES, 'UTF-8') : 'Konu'; ?></title>
</head>
<body>
    <h1><?php echo isset($konu['baslik']) ? htmlspecialchars($konu['baslik'], ENT_QUOTES, 'UTF-8') : 'Konu'; ?></h1>
    <p>Bu sayfada seçtiğiniz konunun detaylarını görebilirsiniz.</p>
    <?php
    // $konu değişkeni, KonuDenetleyici'nin detay() metodunda gönderilir
    // ve seçilen konunun baslik, slug ve aciklama alanlarını içerir.
    ?>
    <?php if (!empty($konu)): ?>
        <h2>Konu Bilgileri</h2>
        <ul>
            <li><strong>Başlık:</strong> <?php echo htmlspecialchars($konu['baslik'], ENT_QUOTES, 'UTF-8'); ?></li>
            <li><strong>Slug:</strong> <?php echo htmlspecialchars($konu['slug'], ENT_QUOTES, 'UTF-8'); ?></li>
            <?php if (isset($konu['aciklama']) && $konu['aciklama'] !== ''): ?>
                <li><strong>Açıklama:</strong> <?php echo htmlspecialchars($konu['aciklama'], ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endif; ?>
        </ul>
    <?php else: ?>
        <p>Konu bulunamadı.</p>
    <?php endif; ?>
</body>
</html>
