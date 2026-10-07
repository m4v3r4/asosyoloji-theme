(() => {
  const menuButton = document.querySelector('.menu-toggle');
  const navigation = document.querySelector('.site-navigation');

  if (menuButton && navigation) {
    const closeMenu = () => {
      navigation.classList.remove('is-open');
      document.body.classList.remove('has-open-menu');
      menuButton.setAttribute('aria-expanded', 'false');
    };

    menuButton.addEventListener('click', () => {
      const isOpen = navigation.classList.toggle('is-open');
      if (isOpen) {
        const headerBottom = document.querySelector('.site-header')?.getBoundingClientRect().bottom || 0;
        document.documentElement.style.setProperty('--aso-mobile-menu-top', `${Math.max(0, headerBottom)}px`);
      }
      document.body.classList.toggle('has-open-menu', isOpen);
      menuButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && navigation.classList.contains('is-open')) {
        closeMenu();
        menuButton.focus();
      }
    });

    document.addEventListener('click', (event) => {
      if (
        navigation.classList.contains('is-open') &&
        !navigation.contains(event.target) &&
        !menuButton.contains(event.target)
      ) {
        closeMenu();
      }
    });

    navigation.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', closeMenu);
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

  const motionEnabled =
    document.body.classList.contains('aso-motion-enabled') &&
    !reduceMotion;

  if (motionEnabled) {
    const revealSelectors = [
      '.home-section',
      '.article-card',
      '.feature-list__item',
      '.feature-list__lead',
      '.aso-post-widget__item',
      '.aso-announcement',
      '.aso-magazine-card',
      '.author-box',
      '.archive-tools',
      '.entry-header',
      '.entry-featured-image'
    ];

    const revealItems = Array.from(
      document.querySelectorAll(revealSelectors.join(','))
    );

    revealItems.forEach((element, index) => {
      element.classList.add('aso-reveal');
      element.style.setProperty('--aso-reveal-delay', `${Math.min(index % 6, 5) * 55}ms`);
    });

    const observer = new IntersectionObserver(
      (entries, currentObserver) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-revealed');
          currentObserver.unobserve(entry.target);
        });
      },
      {
        rootMargin: '0px 0px -8% 0px',
        threshold: 0.08
      }
    );

    revealItems.forEach((element) => observer.observe(element));

    const header = document.querySelector('.site-header');
    if (header) {
      const updateHeaderScroll = () => {
        header.classList.toggle('is-scrolled', window.scrollY > 18);
      };

      window.addEventListener('scroll', updateHeaderScroll, { passive: true });
      updateHeaderScroll();
    }
  }

  document.querySelectorAll('[data-home-latest]').forEach((section) => {
    const grid = section.querySelector('[data-home-latest-grid]');
    const button = section.querySelector('[data-home-latest-button]');
    const status = section.querySelector('[data-home-latest-status]');
    const sentinel = section.querySelector('[data-home-latest-sentinel]');
    const mode = section.dataset.loadMode || 'button';

    if (!grid || mode === 'none' || !window.asosyolojiTheme?.ajaxUrl) {
      return;
    }

    const loadCount = Math.max(3, Number.parseInt(section.dataset.loadCount || '6', 10) || 6);
    const excludedCategories = (section.dataset.excludedCategories || '')
      .split(',')
      .map((value) => Number.parseInt(value, 10))
      .filter(Boolean);
    const excludedPosts = (section.dataset.excludedPosts || '')
      .split(',')
      .map((value) => Number.parseInt(value, 10))
      .filter(Boolean);

    let offset = grid.children.length;
    let loading = false;
    let hasMore = true;

    const setStatus = (message) => {
      if (status) status.textContent = message || '';
    };

    const setButton = (disabled, label) => {
      if (!button) return;
      button.disabled = disabled;
      button.textContent = label || window.asosyolojiTheme.strings.more;
    };

    const revealAppendedCards = (cards) => {
      if (!motionEnabled) return;
      cards.forEach((card, index) => {
        card.classList.add('aso-reveal');
        card.style.setProperty('--aso-reveal-delay', `${Math.min(index, 5) * 55}ms`);
      });
      window.requestAnimationFrame(() => {
        window.requestAnimationFrame(() => {
          cards.forEach((card) => card.classList.add('is-revealed'));
        });
      });
    };

    const loadMore = async () => {
      if (loading || !hasMore) return;

      loading = true;
      setButton(true, window.asosyolojiTheme.strings.loading);
      setStatus(window.asosyolojiTheme.strings.loading);

      const body = new URLSearchParams();
      body.set('action', 'asosyoloji_load_latest');
      body.set('nonce', window.asosyolojiTheme.nonce);
      body.set('count', String(loadCount));
      body.set('offset', String(offset));
      body.set('category', section.dataset.category || '0');
      body.set('orderby', section.dataset.orderby || 'date');
      body.set('order', section.dataset.order || 'DESC');
      excludedCategories.forEach((id) => body.append('excluded_categories[]', String(id)));
      excludedPosts.forEach((id) => body.append('excluded_posts[]', String(id)));

      try {
        const response = await fetch(window.asosyolojiTheme.ajaxUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          },
          credentials: 'same-origin',
          body: body.toString()
        });

        if (!response.ok) throw new Error('Request failed');

        const payload = await response.json();
        if (!payload.success) throw new Error('Invalid response');

        const wrapper = document.createElement('div');
        wrapper.innerHTML = payload.data.html || '';
        const cards = Array.from(wrapper.children);

        cards.forEach((card) => grid.appendChild(card));
        revealAppendedCards(cards);

        offset += Number(payload.data.loaded || cards.length);
        hasMore = !!payload.data.hasMore;

        if (!hasMore) {
          setButton(true, window.asosyolojiTheme.strings.done);
          setStatus(window.asosyolojiTheme.strings.done);
          sentinel?.remove();
        } else {
          setButton(false, window.asosyolojiTheme.strings.more);
          setStatus('');
        }
      } catch (error) {
        setButton(false, window.asosyolojiTheme.strings.more);
        setStatus(window.asosyolojiTheme.strings.error);
      } finally {
        loading = false;
      }
    };

    button?.addEventListener('click', loadMore);

    if (mode === 'infinite' && sentinel && 'IntersectionObserver' in window) {
      const infiniteObserver = new IntersectionObserver(
        (entries) => {
          if (entries.some((entry) => entry.isIntersecting)) {
            loadMore();
          }
        },
        {
          rootMargin: '500px 0px',
          threshold: 0
        }
      );
      infiniteObserver.observe(sentinel);
    }
  });

  document.querySelectorAll('[data-slider]').forEach((slider) => {
    const slides = Array.from(slider.querySelectorAll('[data-slider-slide]'));
    if (slides.length < 2) {
      return;
    }

    const prev = slider.querySelector('[data-slider-prev]');
    const next = slider.querySelector('[data-slider-next]');
    const pause = slider.querySelector('[data-slider-pause]');
    const pauseLabel = slider.querySelector('[data-slider-pause-label]');
    const dots = Array.from(slider.querySelectorAll('[data-slider-dot]'));
    const autoplay = slider.dataset.autoplay === 'true' && !reduceMotion;
    const interval = Math.max(3000, Number.parseInt(slider.dataset.interval || '6000', 10) || 6000);
    const sliderMotion = motionEnabled && !reduceMotion;
    slider.style.setProperty('--aso-slider-interval', `${interval}ms`);

    let index = 0;
    let timer = null;
    let manuallyPaused = false;
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
      const previousIndex = index;
      const normalizedIndex = (newIndex + slides.length) % slides.length;
      const direction = normalizedIndex === previousIndex
        ? 'next'
        : (
          (previousIndex === slides.length - 1 && normalizedIndex === 0) ||
          (normalizedIndex > previousIndex && !(previousIndex === 0 && normalizedIndex === slides.length - 1))
            ? 'next'
            : 'prev'
        );

      index = normalizedIndex;

      if (sliderMotion) {
        slider.classList.remove('is-direction-next', 'is-direction-prev');
        slider.classList.add(`is-direction-${direction}`, 'is-changing');
        window.setTimeout(() => slider.classList.remove('is-changing'), 700);
      }

      slides.forEach((slide, slideIndex) => {
        const active = slideIndex === index;
        slide.classList.toggle('is-active', active);
        slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        updateFocusable(slide, active);
      });

      dots.forEach((dot, dotIndex) => {
        const active = dotIndex === index;
        dot.classList.toggle('is-active', active);
        dot.classList.remove('is-progressing');
        dot.setAttribute('aria-current', active ? 'true' : 'false');

        if (active && autoplay && !manuallyPaused && sliderMotion) {
          void dot.offsetWidth;
          dot.classList.add('is-progressing');
        }
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
      if (autoplay && !manuallyPaused) {
        const activeDot = dots[index];
        if (activeDot && sliderMotion) {
          activeDot.classList.remove('is-progressing');
          void activeDot.offsetWidth;
          activeDot.classList.add('is-progressing');
        }
        timer = window.setInterval(() => show(index + 1), interval);
      }
    };

    pause?.addEventListener('click', () => {
      manuallyPaused = !manuallyPaused;
      pause.setAttribute('aria-pressed', manuallyPaused ? 'true' : 'false');
      if (pauseLabel) {
        pauseLabel.textContent = manuallyPaused ? 'Oynat' : 'Durdur';
      }
      if (manuallyPaused) {
        stop();
        dots.forEach((dot) => dot.classList.remove('is-progressing'));
      } else {
        start();
      }
    });

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


  const readingContent = document.querySelector('[data-reading-content]');
  const readingArticle = document.querySelector('[data-reading-article]');
  const progressBar = document.querySelector('[data-reading-progress]');

  if (readingContent) {
    let readingScale = 1;

    const applyReadingScale = () => {
      readingContent.style.fontSize = `${readingScale}em`;
      try {
        localStorage.setItem('asosyoloji-reading-scale', String(readingScale));
      } catch (e) {}
    };

    try {
      const savedScale = Number.parseFloat(localStorage.getItem('asosyoloji-reading-scale') || '1');
      if (savedScale >= 0.85 && savedScale <= 1.3) {
        readingScale = savedScale;
        applyReadingScale();
      }
    } catch (e) {}

    document.querySelector('[data-font-increase]')?.addEventListener('click', () => {
      readingScale = Math.min(1.3, Math.round((readingScale + 0.05) * 100) / 100);
      applyReadingScale();
    });

    document.querySelector('[data-font-decrease]')?.addEventListener('click', () => {
      readingScale = Math.max(0.85, Math.round((readingScale - 0.05) * 100) / 100);
      applyReadingScale();
    });

    document.querySelector('[data-font-reset]')?.addEventListener('click', () => {
      readingScale = 1;
      applyReadingScale();
    });
  }

  if (readingArticle && progressBar) {
    const updateProgress = () => {
      const rect = readingArticle.getBoundingClientRect();
      const articleTop = window.scrollY + rect.top;
      const articleHeight = readingArticle.offsetHeight;
      const viewportBottom = window.scrollY + window.innerHeight;
      const progress = Math.max(0, Math.min(1, (viewportBottom - articleTop) / articleHeight));
      progressBar.style.transform = `scaleX(${progress})`;
    };

    window.addEventListener('scroll', updateProgress, { passive: true });
    window.addEventListener('resize', updateProgress);
    updateProgress();
  }

  document.querySelector('[data-copy-link]')?.addEventListener('click', async (event) => {
    const button = event.currentTarget;
    const url = button.dataset.url || window.location.href;
    const original = button.textContent;

    try {
      await navigator.clipboard.writeText(url);
      button.textContent = 'Kopyalandı';
      window.setTimeout(() => {
        button.textContent = original;
      }, 1600);
    } catch (e) {
      window.prompt('Bağlantıyı kopyalayın:', url);
    }
  });

  const archive = document.querySelector('[data-magazine-archive]');
  const archiveTools = document.querySelector('[data-archive-tools]');

  if (archive && archiveTools) {
    const searchInput = archiveTools.querySelector('[data-archive-search]');
    const yearsWrap = archiveTools.querySelector('[data-archive-years]');
    const status = archiveTools.querySelector('[data-archive-status]');
    const candidates = Array.from(archive.children).filter((element) => {
      return element.matches('.wp-block-file, .wp-block-image, figure, .wp-block-group, .wp-block-columns, p, div');
    });

    const items = candidates.map((element) => {
      const text = (element.innerText || element.textContent || '').trim();
      const yearMatch = text.match(/(?:19|20)\d{2}/);
      return {
        element,
        text: text.toLocaleLowerCase('tr-TR'),
        year: yearMatch ? yearMatch[0] : ''
      };
    });

    const years = Array.from(new Set(items.map((item) => item.year).filter(Boolean))).sort((a, b) => Number(b) - Number(a));
    let activeYear = '';

    if (yearsWrap && years.length) {
      const all = document.createElement('button');
      all.type = 'button';
      all.className = 'archive-tools__year is-active';
      all.textContent = 'Tümü';
      all.dataset.year = '';
      yearsWrap.appendChild(all);

      years.forEach((year) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'archive-tools__year';
        button.textContent = year;
        button.dataset.year = year;
        yearsWrap.appendChild(button);
      });

      yearsWrap.addEventListener('click', (event) => {
        const button = event.target.closest('[data-year]');
        if (!button) return;
        activeYear = button.dataset.year || '';
        yearsWrap.querySelectorAll('[data-year]').forEach((item) => item.classList.toggle('is-active', item === button));
        filterArchive();
      });
    }

    const filterArchive = () => {
      const query = (searchInput?.value || '').trim().toLocaleLowerCase('tr-TR');
      let visible = 0;

      items.forEach((item) => {
        const matchesSearch = !query || item.text.includes(query);
        const matchesYear = !activeYear || item.year === activeYear;
        const show = matchesSearch && matchesYear;
        item.element.hidden = !show;
        if (show) visible++;
      });

      if (status) {
        status.textContent = `${visible} öğe gösteriliyor`;
      }
    };

    searchInput?.addEventListener('input', filterArchive);
    filterArchive();
  }
})();
