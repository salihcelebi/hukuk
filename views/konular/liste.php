<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Browse Legal Topics</title>
</head>
<body>
<h1>Browse Legal Topics</h1>
<p>This page lists all topics from the database. Click on a topic title to view its detail page.</p>
<?php
// Arayuz Talimati: Public UI Ingilizce olmalidir【434203738162501†L0-L7】.
// $konular degiskeni KonuDenetleyici tarafindan doldurulur ve her kayit icin 'baslik' ve 'slug' alanlari icerir.
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
    <p>No topics found.</p>
<?php endif; ?>
</body>
</html>
