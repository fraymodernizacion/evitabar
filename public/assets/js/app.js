if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    const version = document.documentElement.dataset.appVersion || '';
    const swUrl = version ? `/public/sw.js?v=${encodeURIComponent(version)}` : '/public/sw.js';
    navigator.serviceWorker.register(swUrl).catch(() => {});
  });
}

window.addEventListener('DOMContentLoaded', () => {
  const phoneInput = document.querySelector('input[name="phone"]');
  const dniInput = document.querySelector('input[name="dni"]');
  const toggleButtons = document.querySelectorAll('[data-password-toggle]');
  const carousel = document.querySelector('[data-benefit-carousel]');

  if (phoneInput) {
    const sanitizePhone = () => {
      phoneInput.value = phoneInput.value.replace(/[^0-9+\s()-]/g, '');
    };

    phoneInput.addEventListener('input', sanitizePhone);
    phoneInput.addEventListener('paste', () => {
      window.setTimeout(sanitizePhone, 0);
    });
  }

  if (dniInput) {
    const sanitizeDni = () => {
      dniInput.value = dniInput.value.replace(/[^0-9]/g, '');
    };

    dniInput.addEventListener('input', sanitizeDni);
    dniInput.addEventListener('paste', () => {
      window.setTimeout(sanitizeDni, 0);
    });
  }

  toggleButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const field = button.closest('.password-field');
      const input = field ? field.querySelector('[data-password-input]') : null;
      if (!input) {
        return;
      }

      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      button.setAttribute('aria-label', isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña');
      button.textContent = isHidden ? '🙈' : '👁';
    });
  });

  if (carousel) {
    const track = carousel.querySelector('[data-benefit-track]');
    const tabs = Array.from(carousel.querySelectorAll('[data-benefit-tab]'));
    const slides = Array.from(carousel.querySelectorAll('[data-benefit-slide]'));
    const dots = Array.from(carousel.querySelectorAll('[data-benefit-dot]'));

    const setActive = (level) => {
      tabs.forEach((tab) => {
        const isActive = Number(tab.dataset.benefitTab) === level;
        tab.classList.toggle('is-active', isActive);
        tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });

      dots.forEach((dot) => {
        const isActive = Number(dot.dataset.benefitDot) === level;
        dot.classList.toggle('dot-active', isActive);
      });
    };

    const scrollToLevel = (level) => {
      const slide = slides.find((item) => Number(item.dataset.benefitSlide) === level);
      if (!slide || !track) {
        return;
      }

      track.scrollTo({
        left: slide.offsetLeft,
        behavior: 'smooth',
      });
      setActive(level);
    };

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        scrollToLevel(Number(tab.dataset.benefitTab));
      });
    });

    let scrollTimer = null;
    track?.addEventListener('scroll', () => {
      window.clearTimeout(scrollTimer);
      scrollTimer = window.setTimeout(() => {
        const trackRect = track.getBoundingClientRect();
        const centered = slides
          .map((slide) => ({
            level: Number(slide.dataset.benefitSlide),
            distance: Math.abs(slide.getBoundingClientRect().left - trackRect.left),
          }))
          .sort((a, b) => a.distance - b.distance)[0];

        if (centered) {
          setActive(centered.level);
        }
      }, 80);
    });

    setActive(1);
  }
});
