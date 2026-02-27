<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Makale Detayı - <?php echo isset($makale['baslik']) ? htmlspecialchars($makale['baslik'], ENT_QUOTES, 'UTF-8') : 'Makale'; ?></title>
</head>
<body>
    <h1><?php echo isset($makale['baslik']) ? htmlspecialchars($makale['baslik'], ENT_QUOTES, 'UTF-8') : 'Makale'; ?></h1>
    <?php
    // $makale değişkeni MakaleDenetleyici'nin detay() metodunda gönderilir.
    // 'baslik', 'slug', 'ozet' ve 'icerik' alanlarını içerir.
    ?>
    <?php if (!empty($makale)): ?>
        <?php if (isset($makale['ozet']) && $makale['ozet'] !== ''): ?>
            <p><strong>Özet:</strong> <?php echo htmlspecialchars($makale['ozet'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (isset($makale['icerik']) && $makale['icerik'] !== ''): ?>
            <div>
                <?php echo nl2br(htmlspecialchars($makale['icerik'], ENT_QUOTES, 'UTF-8')); ?>
            </div>
        <?php endif; ?>
        <p><em>Slug: <?php echo htmlspecialchars($makale['slug'], ENT_QUOTES, 'UTF-8'); ?></em></p>
    <?php else: ?>
        <p>Makale bulunamadı.</p>
    <?php endif; ?>
</body>
</html>
