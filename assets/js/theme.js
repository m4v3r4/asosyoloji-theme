(() => {
  const menuButton = document.querySelector('.menu-toggle');
  const navigation = document.querySelector('.site-navigation');

  if (menuButton && navigation) {
    menuButton.addEventListener('click', () => {
      const isOpen = navigation.classList.toggle('is-open');
      menuButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  const themeToggle = document.querySelector('.theme-toggle');

  const setTheme = (theme) => {
    document.documentElement.dataset.theme = theme;
    document.documentElement.style.colorScheme = theme;

    try {
      localStorage.setItem('asosyoloji-theme', theme);
    } catch (e) {}

    if (themeToggle) {
      themeToggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
      themeToggle.dataset.theme = theme;
    }
  };

  if (themeToggle) {
    const current = document.documentElement.dataset.theme || 'light';
    themeToggle.setAttribute('aria-pressed', current === 'dark' ? 'true' : 'false');
    themeToggle.dataset.theme = current;

    themeToggle.addEventListener('click', () => {
      const active = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
      setTheme(active === 'dark' ? 'light' : 'dark');
    });
  }

  const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.querySelectorAll('[data-slider]').forEach((slider) => {
    const slides = Array.from(slider.querySelectorAll('[data-slider-slide]'));
    if (slides.length < 2) {
      return;
    }

    const prev = slider.querySelector('[data-slider-prev]');
    const next = slider.querySelector('[data-slider-next]');
    const dots = Array.from(slider.querySelectorAll('[data-slider-dot]'));
    const autoplay = slider.dataset.autoplay === 'true' && !reduceMotion;
    const interval = Math.max(3000, Number.parseInt(slider.dataset.interval || '6000', 10) || 6000);

    let index = 0;
    let timer = null;
    let touchStartX = 0;
    let touchDeltaX = 0;

    const updateFocusable = (slide, active) => {
      slide.querySelectorAll('a, button, input, select, textarea, [tabindex]').forEach((element) => {
        if (active) {
          if (element.dataset.sliderTabindex !== undefined) {
            const saved = element.dataset.sliderTabindex;
            if (saved === '') {
              element.removeAttribute('tabindex');
            } else {
              element.setAttribute('tabindex', saved);
            }
            delete element.dataset.sliderTabindex;
          }
        } else {
          if (element.dataset.sliderTabindex === undefined) {
            element.dataset.sliderTabindex = element.getAttribute('tabindex') || '';
          }
          element.setAttribute('tabindex', '-1');
        }
      });
    };

    const show = (newIndex, focus = false) => {
      index = (newIndex + slides.length) % slides.length;

      slides.forEach((slide, slideIndex) => {
        const active = slideIndex === index;
        slide.classList.toggle('is-active', active);
        slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        updateFocusable(slide, active);
      });

      dots.forEach((dot, dotIndex) => {
        const active = dotIndex === index;
        dot.classList.toggle('is-active', active);
        dot.setAttribute('aria-current', active ? 'true' : 'false');
      });

      if (focus) {
        const activeLink = slides[index].querySelector('.aso-slider__title a');
        if (activeLink) {
          activeLink.focus({ preventScroll: true });
        }
      }
    };

    const stop = () => {
      if (timer) {
        window.clearInterval(timer);
        timer = null;
      }
    };

    const start = () => {
      stop();
      if (autoplay) {
        timer = window.setInterval(() => show(index + 1), interval);
      }
    };

    prev?.addEventListener('click', () => {
      show(index - 1);
      start();
    });

    next?.addEventListener('click', () => {
      show(index + 1);
      start();
    });

    dots.forEach((dot) => {
      dot.addEventListener('click', () => {
        const target = Number.parseInt(dot.dataset.sliderDot || '0', 10);
        show(target);
        start();
      });
    });

    slider.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowLeft') {
        event.preventDefault();
        show(index - 1, true);
        start();
      }

      if (event.key === 'ArrowRight') {
        event.preventDefault();
        show(index + 1, true);
        start();
      }
    });

    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);
    slider.addEventListener('focusin', stop);
    slider.addEventListener('focusout', (event) => {
      if (!slider.contains(event.relatedTarget)) {
        start();
      }
    });

    slider.addEventListener('touchstart', (event) => {
      touchStartX = event.touches[0]?.clientX || 0;
      touchDeltaX = 0;
      stop();
    }, { passive: true });

    slider.addEventListener('touchmove', (event) => {
      const currentX = event.touches[0]?.clientX || 0;
      touchDeltaX = currentX - touchStartX;
    }, { passive: true });

    slider.addEventListener('touchend', () => {
      if (Math.abs(touchDeltaX) > 45) {
        show(touchDeltaX < 0 ? index + 1 : index - 1);
      }
      start();
    }, { passive: true });

    show(0);
    start();
  });
})();
