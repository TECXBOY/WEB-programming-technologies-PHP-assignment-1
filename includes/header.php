<?php
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'Home';
$flash = flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php">
        <img src="assets/img/django-girls-logo.png" alt="Django Girls logo"
             onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'logo-fallback',textContent:'Django Girls'}))">
    </a>
    <nav>
        <?php if (is_logged_in()): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php" class="btn-small">Log out</a>
        <?php else: ?>
            <a href="login.php">Log in</a>
            <a href="register.php" class="btn-small">Sign up</a>
        <?php endif; ?>
    </nav>
</header>
<main>
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>" role="alert"><?= e($flash['text']) ?></div>
<?php endif; ?>
