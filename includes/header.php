<?php

declare(strict_types=1);

$appConfig = require __DIR__ . '/../config/app.php';
$appName = setting('program_name', $appConfig['app_name']) ?? $appConfig['app_name'];
$pageTitle = $pageTitle ?? $appName;
$bodyClass = $bodyClass ?? '';
$flashes = get_flashes();
?>
<!doctype html>
<html lang="es-AR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f1b2d">
    <title><?= e($pageTitle) ?></title>
    <link rel="manifest" href="/public/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/public/assets/icons/favicon.svg">
    <link rel="apple-touch-icon" href="/public/assets/icons/icon.svg">
    <link rel="stylesheet" href="/public/assets/css/styles.css">
</head>
<body class="<?= e($bodyClass) ?>">
<div class="app-shell">
    <header class="topbar">
        <div class="brand-lockup">
            <div class="logo-frame" aria-hidden="true">
                <img src="/public/assets/images/logo-evita-bar.png" alt="">
            </div>
            <div>
                <p class="brand-kicker">Evita Bar</p>
                <h1 class="brand-title"><?= e($appName) ?></h1>
            </div>
        </div>
    </header>

    <?php if (!empty($flashes)): ?>
        <section class="flash-stack">
            <?php foreach ($flashes as $flash): ?>
                <article class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <main class="main-content">
