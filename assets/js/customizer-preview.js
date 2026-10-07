(() => {
  const root = document.documentElement;

  const bindVar = (setting, cssVar, transform = (value) => value) => {
    if (!window.wp || !wp.customize) return;
    wp.customize(setting, (value) => {
      value.bind((next) => {
        root.style.setProperty(cssVar, transform(next));
      });
    });
  };

  bindVar('aso_primary_color', '--aso-primary');
  bindVar('aso_text_color', '--aso-text');
  bindVar('aso_muted_color', '--aso-muted');
  bindVar('aso_border_color', '--aso-border');
  bindVar('aso_background_color', '--aso-bg');
  bindVar('aso_surface_color', '--aso-surface');

  bindVar('aso_dark_background', '--aso-dark-bg');
  bindVar('aso_dark_surface', '--aso-dark-surface');
  bindVar('aso_dark_text', '--aso-dark-text');
  bindVar('aso_dark_muted', '--aso-dark-muted');
  bindVar('aso_dark_border', '--aso-dark-border');
  bindVar('aso_dark_link', '--aso-dark-link');
  bindVar('aso_dark_footer', '--aso-dark-footer');

  bindVar('aso_logo_width', '--aso-logo-width', (value) => `${value}px`);
  bindVar('aso_logo_width_mobile', '--aso-logo-width-mobile', (value) => `${value}px`);
  bindVar('aso_container_width', '--aso-container', (value) => `${value}px`);
  bindVar('aso_article_width', '--aso-article', (value) => `${value}px`);
  bindVar('aso_body_size', '--aso-body-size', (value) => `${value}px`);
})();
