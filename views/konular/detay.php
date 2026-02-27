<!DOCTYPE html>
<html lang="en">">
<head>
    <meta charset="utf-8">
    <title><?php echo isset($konu['baslik']) ? htmlspecialchars($konu['baslik'], ENT_QUOTES, 'UTF-8') : 'Topic'; ?></title>
</head>
<body>
<h1><?php echo isset($konu['baslik']) ? htmlspecialchars($konu['baslik'], ENT_QUOTES, 'UTF-8') : 'Topic'; ?></h1>
<p>You can view details of the selected topic on this page.</p>
<?php
// Arayuz Talimati: Public UI Ingilizce olmalidir【434203738162501†L0-L7】.
// $konu degiskeni KonuDenetleyici'nin detay() metodunda gonderilir.
// Dizi icinde 'baslik', 'slug' ve 'aciklama' alanlari bulunur.
?>
<?php if (!empty($konu)): ?>
    <h2>Topic Information</h2>
    <ul>
        <li><strong>Title:</strong> <?php echo htmlspecialchars($konu['baslik'], ENT_QUOTES, 'UTF-8'); ?></li>
        <li><strong>Slug:</strong> <?php echo htmlspecialchars($konu['slug'], ENT_QUOTES, 'UTF-8'); ?></li>
        <?php if (isset($konu['aciklama']) && $konu['aciklama']): ?>
            <li><strong>Description:</strong> <?php echo htmlspecialchars($konu['aciklama'], ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endif; ?>
    </ul>
<?php else: ?>
    <p>Topic not found.</p>
<?php endif; ?>
</body>
</html>
