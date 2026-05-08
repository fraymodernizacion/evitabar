<?php
declare(strict_types=1);
?><!doctype html>
<html lang="es-AR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#120d0e">
  <meta name="description" content="Casa del Peronismo de Fray Mamerto Esquiú y Evita Bar. Inauguración 7 de mayo de 2026.">
  <title>Casa del Peronismo | Evita Bar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #0d0a0b;
      --bg-soft: #151013;
      --panel: rgba(25, 18, 20, 0.72);
      --panel-strong: rgba(14, 10, 11, 0.86);
      --text: #f6efe4;
      --muted: rgba(246, 239, 228, 0.76);
      --cream: #f2e5d1;
      --gold: #c7a15a;
      --wine: #3f1820;
      --line: rgba(246, 239, 228, 0.12);
      --shadow: 0 28px 90px rgba(0, 0, 0, 0.46);
      --radius: 28px;
    }

    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; min-height: 100%; }
    body {
      font-family: 'Montserrat', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: var(--bg);
      color: var(--text);
      -webkit-font-smoothing: antialiased;
      text-rendering: optimizeLegibility;
    }

    img { display: block; max-width: 100%; }

    .page {
      min-height: 100vh;
      background:
        radial-gradient(circle at top, rgba(199, 161, 90, 0.12), transparent 28%),
        linear-gradient(180deg, #151012 0%, #0b090a 100%);
    }

    .hero {
      position: relative;
      min-height: 100svh;
      display: grid;
      align-items: center;
      overflow: hidden;
      isolation: isolate;
      padding: clamp(24px, 4vw, 44px) 18px;
      background:
        linear-gradient(180deg, rgba(8, 6, 7, 0.42), rgba(8, 6, 7, 0.9)),
        url('/public/assets/landing/fachada.jpg') center center / cover no-repeat;
    }

    .hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background:
        radial-gradient(circle at 50% 20%, rgba(199, 161, 90, 0.18), transparent 34%),
        linear-gradient(180deg, rgba(9, 7, 8, 0.1), rgba(9, 7, 8, 0.78));
      z-index: -1;
    }

    .hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background:
        linear-gradient(120deg, rgba(255, 255, 255, 0.05), transparent 28%),
        linear-gradient(300deg, rgba(255, 255, 255, 0.03), transparent 22%);
      mix-blend-mode: screen;
      opacity: 0.36;
      pointer-events: none;
    }

    .hero-inner {
      position: relative;
      z-index: 1;
      width: min(760px, 100%);
      margin: 0 auto;
      text-align: center;
      display: grid;
      gap: 18px;
      justify-items: center;
      padding: 10px 0;
    }

    .hero-logos {
      width: min(420px, 100%);
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
      margin-bottom: 2px;
    }

    .hero-logo {
      min-height: 0;
      border-radius: 0;
      display: grid;
      place-items: center;
      padding: 0;
      background: transparent;
      border: 0;
      box-shadow: none;
      opacity: 0.98;
    }

    .hero-logo img {
      width: 100%;
      max-width: 180px;
      max-height: 40px;
      object-fit: contain;
    }

    .brand-mark {
      width: 128px;
      height: 128px;
      border-radius: 50%;
      background:
        radial-gradient(circle at 35% 30%, rgba(255,255,255,0.06), transparent 42%),
        linear-gradient(180deg, rgba(22, 16, 18, 0.98), rgba(9, 7, 8, 0.98));
      border: 1px solid rgba(199, 161, 90, 0.34);
      box-shadow: var(--shadow);
      display: grid;
      place-items: center;
      padding: 14px;
      backdrop-filter: blur(10px);
    }

    .brand-mark img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .date {
      font-size: 0.82rem;
      font-weight: 700;
      letter-spacing: 0.28em;
      text-transform: uppercase;
      color: rgba(242, 229, 209, 0.74);
    }

    h1 {
      margin: 0;
      font-size: clamp(3rem, 13vw, 6.6rem);
      font-weight: 900;
      line-height: 0.92;
      letter-spacing: 0.02em;
      text-transform: uppercase;
      color: var(--cream);
      text-wrap: balance;
      text-shadow: 0 14px 50px rgba(0, 0, 0, 0.7);
    }

    .place {
      font-size: clamp(1.02rem, 3.5vw, 1.42rem);
      font-weight: 600;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: rgba(242, 229, 209, 0.84);
      margin-top: -2px;
    }

    .statement,
    .closing {
      width: min(64ch, 100%);
      margin: 0;
      font-size: clamp(1rem, 3.6vw, 1.18rem);
      line-height: 1.75;
      text-wrap: pretty;
      color: rgba(246, 239, 228, 0.94);
    }

    .statement strong,
    .closing strong {
      color: #fff;
      font-weight: 800;
    }

    .separator {
      width: min(180px, 42vw);
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(199, 161, 90, 0.95), transparent);
      margin: 2px 0;
    }

    .emotive {
      padding: 54px 18px;
      background: linear-gradient(180deg, #0d0a0b 0%, #120d0e 100%);
      border-top: 1px solid rgba(255,255,255,0.06);
      border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .emotive-inner {
      width: min(820px, 100%);
      margin: 0 auto;
      padding: 26px 18px;
      border-radius: var(--radius);
      background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.015));
      border: 1px solid rgba(255,255,255,0.08);
      box-shadow: var(--shadow);
      text-align: center;
    }

    .emotive p {
      margin: 0;
      font-size: clamp(1.18rem, 4vw, 2rem);
      line-height: 1.55;
      color: var(--cream);
      text-wrap: balance;
    }

    .plaque {
      padding: 54px 18px 44px;
      background:
        linear-gradient(180deg, #120d0e 0%, #0d0a0b 100%);
      border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .plaque-inner {
      width: min(920px, 100%);
      margin: 0 auto;
      padding: 28px 18px;
      border-radius: var(--radius);
      background:
        radial-gradient(circle at top, rgba(199,161,90,0.11), transparent 36%),
        linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));
      border: 1px solid rgba(255,255,255,0.08);
      box-shadow: var(--shadow);
      text-align: center;
    }

    .plaque-kicker {
      margin: 0 0 10px;
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.28em;
      text-transform: uppercase;
      color: rgba(242, 229, 209, 0.68);
    }

    .plaque-logos {
      display: none;
    }

    .plaque-logo {
      min-height: 130px;
      border-radius: 22px;
      display: grid;
      place-items: center;
      padding: 18px;
      background:
        radial-gradient(circle at 50% 28%, rgba(255,255,255,0.06), transparent 42%),
        linear-gradient(180deg, rgba(18, 14, 16, 0.98), rgba(8, 6, 7, 0.98));
      border: 1px solid rgba(199,161,90,0.22);
      box-shadow: 0 14px 36px rgba(0,0,0,0.35);
    }

    .plaque-logo img {
      max-width: 100%;
      max-height: 92px;
      object-fit: contain;
    }

    .plaque-logo-end {
      width: min(320px, 100%);
      margin: 22px auto 0;
    }

    .plaque p {
      margin: 0;
    }

    .plaque-quote {
      font-size: clamp(1rem, 3.5vw, 1.42rem);
      line-height: 1.8;
      color: rgba(246, 239, 228, 0.94);
      text-wrap: pretty;
    }

    .plaque-foot {
      margin-top: 18px !important;
      font-size: 0.9rem;
      line-height: 1.7;
      color: rgba(246, 239, 228, 0.72);
      letter-spacing: 0.04em;
    }

    .menu {
      padding: 56px 18px 34px;
      background:
        linear-gradient(180deg, #120d0e 0%, #0d0a0b 100%);
      border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .menu-inner {
      width: min(1140px, 100%);
      margin: 0 auto;
    }

    .menu-kicker {
      margin: 0 0 10px;
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.28em;
      text-transform: uppercase;
      color: rgba(242, 229, 209, 0.72);
      text-align: center;
    }

    .menu h2 {
      margin: 0;
      font-size: clamp(1.7rem, 5.8vw, 3.2rem);
      line-height: 0.98;
      font-weight: 900;
      letter-spacing: 0.03em;
      text-transform: uppercase;
      text-align: center;
      color: var(--cream);
    }

    .menu-lead {
      margin: 14px auto 0;
      width: min(62ch, 100%);
      text-align: center;
      color: rgba(246, 239, 228, 0.82);
      line-height: 1.7;
      font-size: 1rem;
    }

    .menu-preview {
      width: min(780px, 100%);
      margin: 26px auto 0;
      display: block;
      position: relative;
      aspect-ratio: 210 / 297;
      border-radius: 26px;
      overflow: hidden;
      border: 1px solid rgba(255,255,255,0.12);
      box-shadow: var(--shadow);
      background: #f4efe5;
      text-decoration: none;
    }

    .menu-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      filter: saturate(0.96) contrast(1.02);
    }

    .menu-preview::after {
      content: "";
      position: absolute;
      inset: 0;
      background:
        linear-gradient(180deg, rgba(8,6,7,0.08), rgba(8,6,7,0.4));
    }

    .menu-preview-label {
      position: absolute;
      left: 16px;
      bottom: 16px;
      z-index: 2;
      padding: 10px 14px;
      border-radius: 999px;
      background: rgba(8, 6, 7, 0.74);
      color: var(--cream);
      text-transform: uppercase;
      letter-spacing: 0.16em;
      font-size: 0.76rem;
      font-weight: 800;
      border: 1px solid rgba(255,255,255,0.12);
      backdrop-filter: blur(6px);
    }

    .menu-footer-note {
      margin: 14px 0 0;
      text-align: center;
      font-size: 0.84rem;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      color: rgba(246, 239, 228, 0.56);
    }

    .gallery {
      padding: 18px 18px 42px;
      background: #0b090a;
    }

    .gallery-grid {
      width: min(1100px, 100%);
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr;
      gap: 12px;
    }

    .shot {
      position: relative;
      min-height: 210px;
      border-radius: 22px;
      overflow: hidden;
      border: 1px solid rgba(255,255,255,0.08);
      box-shadow: 0 18px 56px rgba(0,0,0,0.32);
      background: linear-gradient(140deg, rgba(255,255,255,0.09), rgba(255,255,255,0.02));
      isolation: isolate;
    }

    .shot img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      inset: 0;
      filter: saturate(0.95) contrast(1.03);
      transform: scale(1.01);
    }

    .shot::after {
      content: "";
      position: absolute;
      inset: 0;
      background:
        linear-gradient(180deg, rgba(8,6,7,0.08), rgba(8,6,7,0.36) 100%),
        radial-gradient(circle at 50% 34%, rgba(199,161,90,0.12), transparent 40%);
      z-index: 1;
      pointer-events: none;
    }

    .shot .caption {
      position: absolute;
      left: 14px;
      bottom: 14px;
      z-index: 2;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 7px 11px;
      border-radius: 999px;
      background: rgba(10, 8, 9, 0.55);
      color: rgba(255,255,255,0.96);
      border: 1px solid rgba(255,255,255,0.12);
      backdrop-filter: blur(6px);
      text-transform: uppercase;
      letter-spacing: 0.16em;
      font-size: 0.72rem;
      font-weight: 700;
    }

    .shot.logo-card {
      display: grid;
      place-items: center;
      background:
        radial-gradient(circle at 50% 24%, rgba(199,161,90,0.12), transparent 36%),
        linear-gradient(145deg, #22181a 0%, #140f10 60%, #332219 100%);
      padding: 18px;
    }

    .shot.logo-card img {
      position: relative;
      width: min(78%, 340px);
      height: auto;
      object-fit: contain;
      filter: drop-shadow(0 18px 32px rgba(0,0,0,0.28));
    }

    .footer {
      padding: 28px 18px 44px;
      background: #090707;
      text-align: center;
      border-top: 1px solid rgba(255,255,255,0.05);
    }

    .footer h2 {
      margin: 0 0 8px;
      font-size: 0.98rem;
      font-weight: 800;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: var(--cream);
    }

    .footer p {
      margin: 0;
      line-height: 1.7;
      color: rgba(246, 239, 228, 0.84);
    }

    .dream {
      margin-top: 10px !important;
      color: var(--gold) !important;
      font-style: italic;
    }

    .fade-in {
      opacity: 0;
      transform: translateY(18px);
      transition: opacity 0.9s ease, transform 0.9s ease;
    }

    .fade-in.is-visible {
      opacity: 1;
      transform: translateY(0);
    }

    @media (min-width: 720px) {
      .hero-logos {
        width: min(560px, 100%);
      }

      .hero-logo {
        padding: 0;
        opacity: 1;
      }

      .hero-logo img {
        max-width: 280px;
        max-height: 74px;
      }

      .plaque-logos {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .menu-preview {
        max-width: 520px;
      }

      .gallery-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .shot {
        min-height: 320px;
      }

      .shot.logo-card {
        min-height: 320px;
      }
    }

    @media (min-width: 1080px) {
      .gallery-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }

      .hero {
        min-height: 100vh;
      }
    }
  </style>
</head>
<body>
  <main class="page">
    <section class="hero fade-in" data-reveal>
      <div class="hero-inner">
        <div class="hero-logos" aria-label="Logos principales">
          <div class="hero-logo">
            <img src="/public/assets/landing/logo-casa-del-peronismo.png" alt="Logo Casa del Peronismo">
          </div>
          <div class="hero-logo">
            <img src="/public/assets/landing/evita-bar-logo.png" alt="Logo Evita Bar">
          </div>
        </div>
        <div class="date">7 de mayo de 2026</div>
        <h1>Casa del Peronismo</h1>
        <div class="place">Fray Mamerto Esquiú</div>
        <p class="statement">En el 107° aniversario del natalicio de Eva Perón, inauguramos por primera vez en nuestra historia un espacio propio para la militancia.</p>
        <div class="separator" aria-hidden="true"></div>
        <p class="closing">Dentro de esta casa vive <strong>Evita Bar</strong>.<br>Un homenaje permanente a la abanderada de los humildes.</p>
      </div>
    </section>

    <section class="emotive fade-in" data-reveal>
      <div class="emotive-inner">
        <p>Hay lugares que no se construyen solo con ladrillos.<br>Se construyen con historia, memoria y amor al pueblo.</p>
      </div>
    </section>

    <section class="plaque">
      <div class="plaque-inner fade-in" data-reveal>
        <p class="plaque-kicker">Texto de la placa inaugural</p>
        <p class="plaque-quote">“A 107 años del natalicio de Eva Duarte de Perón, el Consejo Directivo del Partido Justicialista de Fray Mamerto Esquiú deja inaugurada la sede partidaria construida con el valioso aporte de compañeros y compañeras, funcionarias y funcionarios de la Municipalidad de Fray Mamerto Esquiú, que durante el período 2020–2026 hicieron su contribución económica para la construcción de este espacio fundamental para la doctrina peronista, la construcción colectiva y la memoria activa.”</p>
        <p class="plaque-foot">Lucía Corpacci, presidenta del Partido Justicialista de Catamarca y Consejo Directivo · Guillermo Ferreyra, presidente del Consejo Departamental FME y Consejo Directivo · Fray Mamerto Esquiú, 7 de mayo de 2026</p>
        <div class="plaque-logo plaque-logo-end" aria-label="Logo Partido Justicialista Fray Mamerto Esquiú">
          <img src="/public/assets/landing/logo-pj-fray.png" alt="Logo Partido Justicialista Fray Mamerto Esquiú">
        </div>
      </div>
    </section>

    <section class="gallery">
      <div class="gallery-grid">
        <!-- Reemplazar las imágenes cuando haya nuevas fotos oficiales -->
        <figure class="shot fade-in" data-reveal>
          <img src="/public/assets/landing/fachada.jpg" alt="Fachada de la Casa del Peronismo y Evita Bar">
        </figure>
        <figure class="shot fade-in" data-reveal>
          <img src="/public/assets/landing/interior-3.jpg" alt="Interior de Evita Bar">
        </figure>
        <figure class="shot fade-in" data-reveal>
          <img src="/public/assets/landing/interior-4.jpg" alt="Interior de Evita Bar con mural de Eva Perón">
        </figure>
        <figure class="shot logo-card fade-in" data-reveal>
          <img src="/public/assets/landing/evita-bar-logo.png" alt="Logo de Evita Bar">
        </figure>
      </div>
    </section>

    <section class="menu">
      <div class="menu-inner">
        <p class="menu-kicker fade-in" data-reveal>La carta del bar</p>
        <h2 class="fade-in" data-reveal>Sabores de casa, con identidad propia</h2>
        <p class="menu-lead fade-in" data-reveal>Una primera mirada a la carta de Evita Bar.</p>
        <a class="menu-preview fade-in" data-reveal href="/carta.php" aria-label="Abrir la carta completa de Evita Bar">
          <img src="/public/assets/landing/menu/page-1.png" alt="Vista previa de la carta de Evita Bar">
          <span class="menu-preview-label">Abrir carta completa</span>
        </a>
      </div>
    </section>

    <footer class="footer fade-in" data-reveal>
      <h2>Casa del Peronismo + Evita Bar</h2>
      <p>Fray Mamerto Esquiú — Catamarca</p>
      <p class="dream">Un sueño hecho realidad.</p>
    </footer>
  </main>

  <script>
    const observer = new IntersectionObserver((entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      }
    }, { threshold: 0.15 });

    document.querySelectorAll('[data-reveal]').forEach((el) => observer.observe(el));
  </script>
</body>
</html>
