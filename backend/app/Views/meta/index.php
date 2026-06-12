<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />

    <title><?= esc($title) ?></title>
    <meta name="description" content="<?= esc($description) ?>" />

    <!-- Canonical -->
    <link rel="canonical" href="<?= esc($url) ?>" />

    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?= esc($title) ?>" />
    <meta property="og:description" content="<?= esc($description) ?>" />
    <meta property="og:image" content="<?= esc($image) ?>" />
    <meta property="og:url" content="<?= esc($url) ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />

    <!-- Twitter Meta -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?= esc($title) ?>" />
    <meta name="twitter:description" content="<?= esc($description) ?>" />
    <meta name="twitter:image" content="<?= esc($image) ?>" />
</head>
<body></body>
</html>
