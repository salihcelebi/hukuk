<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo isset($makale['baslik']) ? htmlspecialchars($makale['baslik'], ENT_QUOTES, 'UTF-8') : 'Article'; ?></title>
</head>
<body>
<h1>
    <?php echo isset($makale['baslik']) ? htmlspecialchars($makale['baslik'], ENT_QUOTES, 'UTF-8') : 'Article'; ?>
</h1>
<?php
// $makale degiskeni MakaleDenetleyici'nin detay() metodundan gelir.
// 'baslik', 'slug', 'ozet' ve 'icerik' alanlarini icerir.
// Arayuz Talimati: Public UI Ingilizce olmalidir [434203738162501]
?>
<?php if (!empty($makale)): ?>
    <?php if (isset($makale['ozet']) && $makale['ozet'] !== ''): ?>
        <p><strong>Summary:</strong> <?php echo htmlspecialchars($makale['ozet'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
    <?php if (isset($makale['icerik']) && $makale['icerik'] !== ''): ?>
        <div>
            <?php echo nl2br(htmlspecialchars($makale['icerik'], ENT_QUOTES, 'UTF-8')); ?>
        </div>
    <?php endif; ?>
    <p><em>Slug: <?php echo htmlspecialchars($makale['slug'], ENT_QUOTES, 'UTF-8'); ?></em></p>
<?php else: ?>
    <p>No article found.</p>
<?php endif; ?>
</body>
<</html>
