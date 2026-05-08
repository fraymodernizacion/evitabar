<?php
declare(strict_types=1);
?><!doctype html>
<html lang="es-AR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#0d0a0b">
  <title>Carta de Evita Bar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #0d0a0b;
      --text: #f6efe4;
      --cream: #f2e5d1;
      --gold: #c7a15a;
      --shadow: 0 24px 80px rgba(0,0,0,0.42);
      --radius: 26px;
    }

    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; min-height: 100%; }
    body {
      font-family: 'Montserrat', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background:
        radial-gradient(circle at top, rgba(199,161,90,0.12), transparent 32%),
        linear-gradient(180deg, #151012 0%, #0b090a 100%);
      color: var(--text);
      -webkit-font-smoothing: antialiased;
    }

    img { display: block; max-width: 100%; }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 5;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 16px 18px;
      background: rgba(10, 8, 9, 0.9);
      border-bottom: 1px solid rgba(255,255,255,0.08);
      backdrop-filter: blur(14px);
    }

    .topbar a {
      color: var(--cream);
      text-decoration: none;
      font-size: 0.8rem;
      font-weight: 800;
      letter-spacing: 0.18em;
      text-transform: uppercase;
    }

    .topbar .title {
      font-size: 0.78rem;
      font-weight: 800;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      color: rgba(246, 239, 228, 0.8);
      text-align: center;
    }

    .hero {
      padding: 28px 18px 10px;
      text-align: center;
    }

    .hero h1 {
      margin: 0;
      font-size: clamp(1.9rem, 7vw, 3.8rem);
      line-height: 0.95;
      text-transform: uppercase;
      font-weight: 900;
      color: var(--cream);
      text-wrap: balance;
    }

    .hero p {
      margin: 12px auto 0;
      max-width: 64ch;
      line-height: 1.7;
      color: rgba(246, 239, 228, 0.8);
    }

    .reader {
      width: min(980px, calc(100% - 24px));
      margin: 18px auto 42px;
      display: grid;
      gap: 18px;
    }

    .page {
      background:
        linear-gradient(180deg, rgba(255,255,255,0.95), rgba(246,239,230,0.96));
      border-radius: var(--radius);
      overflow: hidden;
      border: 1px solid rgba(255,255,255,0.1);
      box-shadow: var(--shadow);
      padding: 12px;
    }

    .page img {
      width: 100%;
      height: auto;
      object-fit: contain;
      background: #f6efe4;
      border-radius: 18px;
    }

    .page-label {
      margin-top: 10px;
      text-align: center;
      font-size: 0.74rem;
      font-weight: 800;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: rgba(246, 239, 228, 0.6);
    }

    .note {
      width: min(780px, calc(100% - 24px));
      margin: 0 auto 40px;
      text-align: center;
      font-size: 0.9rem;
      line-height: 1.7;
      color: rgba(246, 239, 228, 0.74);
    }

    @media (min-width: 720px) {
      .reader {
        gap: 22px;
      }
      .page {
        padding: 18px;
      }
    }
  </style>
</head>
<body>
  <header class="topbar">
    <a href="/">Volver</a>
    <div class="title">Carta de Evita Bar</div>
    <span aria-hidden="true"></span>
  </header>

  <section class="hero">
    <h1>La carta</h1>
  </section>

  <main class="reader">
    <article class="page">
      <img src="/public/assets/landing/menu/page-1.png" alt="Carta de Evita Bar, página 1">
      <div class="page-label">Página 1</div>
    </article>
    <article class="page">
      <img src="/public/assets/landing/menu/page-2.png" alt="Carta de Evita Bar, página 2">
      <div class="page-label">Página 2</div>
    </article>
    <article class="page">
      <img src="/public/assets/landing/menu/page-3.png" alt="Carta de Evita Bar, página 3">
      <div class="page-label">Página 3</div>
    </article>
    <article class="page">
      <img src="/public/assets/landing/menu/page-4.png" alt="Carta de Evita Bar, página 4">
      <div class="page-label">Página 4</div>
    </article>
  </main>

</body>
</html>
