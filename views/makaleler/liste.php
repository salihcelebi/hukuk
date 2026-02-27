<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Browse Articles</title>
</head>
<body>
<h1>Browse Articles</h1>
<p>This page lists all articles. Click an article title to view details.</p>
<?php
// $makaleler degiskeni MakaleDenetleyici->liste() metodundan gelir.
// Her makale icin 'baslik', 'slug' ve istege bagli olarak 'ozet' alanlari bulunur.
// Arayuz Talimati: Public UI Ingilizce olmalidir [434203738162501]
?>
<?php if (!empty($makaleler)): ?>
    <ul>
    <?php foreach ($makaleler as $makale): ?>
        <li>
            <a href="/makaleler/<?php echo htmlspecialchars($makale['slug'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($makale['baslik'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
            <?php if (isset($makale['ozet']) && $makale['ozet'] != ''): ?>
                <p><?php echo htmlspecialchars($makale['ozet'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No articles found.</p>
<?php endif; ?>
</body>
</html>
