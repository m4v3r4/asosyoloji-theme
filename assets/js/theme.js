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
