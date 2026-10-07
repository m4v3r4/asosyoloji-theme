(() => {
  const button = document.querySelector('.menu-toggle');
  const navigation = document.querySelector('.site-navigation');

  if (!button || !navigation) {
    return;
  }

  button.addEventListener('click', () => {
    const isOpen = navigation.classList.toggle('is-open');
    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  });
})();


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
