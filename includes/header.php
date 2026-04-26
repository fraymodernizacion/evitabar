<?php

declare(strict_types=1);

$appConfig = require __DIR__ . '/../config/app.php';

if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

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
    <meta name="app-version" content="<?= e(app_version()) ?>">
    <link rel="manifest" href="<?= e(asset_url('/public/manifest.json')) ?>">
    <link rel="icon" type="image/svg+xml" href="<?= e(asset_url('/public/assets/icons/favicon.svg')) ?>">
    <link rel="apple-touch-icon" href="<?= e(asset_url('/public/assets/icons/icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/public/assets/css/styles.css')) ?>">
</head>
<body class="<?= e($bodyClass) ?>" data-app-version="<?= e(app_version()) ?>">
<div class="app-shell">
    <header class="topbar">
        <div class="brand-lockup">
            <div class="logo-frame" aria-hidden="true">
                <img src="<?= e(asset_url('/public/assets/images/logo-evita-bar.png')) ?>" alt="">
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
