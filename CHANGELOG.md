# Changelog

All notable changes to the Asosyoloji WordPress Theme are documented here.

## 0.7.3 - 2026-10-07

### Added
- General page content width presets: Dar, Geniş and Tam.
- Homepage slider width control: Genel ayarı kullan, Dar, Geniş or Tam.

### Changed
- Slider can now inherit the general page width or override it independently.
- Standard page content width follows the selected layout preset.


## 0.7.2 - 2026-10-07

### Added
- Companion plugin manager under **Görünüm → Asosyoloji Eklentileri**.
- Asosyoloji Haftalık recommendation notice when the companion plugin is not active.
- One-click GitHub Release installation and activation for Asosyoloji Haftalık.
- Native integration with the standalone `m4v3r4/asosyoloji-weekly` repository.

### Changed
- Printed magazine PDFs are now exposed explicitly as **Asosyoloji: Basılı Sayılar (PDF)**.
- Removed the obsolete homepage post/archive callout and its Customizer section.
- Homepage section ordering no longer includes the old archive section.
- Weekly events functionality is kept outside the theme as a standalone plugin.


## 0.7.0 - 2026-10-07

### Added
- Connected SEO/GEO JSON-LD graph: Organization, WebSite, WebPage, Article, ProfilePage, Person and BreadcrumbList.
- Rich Article metadata: section, tags, word count, comments, author entity, about entities and citations.
- SEO description fallback: manual field → GEO summary → excerpt → article content.
- Author sameAs links and publisher sameAs settings.
- Publisher logo, description and publishingPrinciples support.
- Default social sharing image fallback.
- Per-post SEO/GEO editor fields for description, summary, key points, entities and citation URLs.
- Visible “Kısaca / Bu yazıda” summary panel.
- Automatic H2/H3 table of contents for long articles.
- Visible Sources / Citations section.
- PublicationIssue schema for magazine/PDF archive widgets.
- Optional IndexNow publishing notifications and key verification endpoint.
- Optional /llms.txt endpoint.
- Canonical hints for archive/author/category pages and sitemap discovery link.
- Homepage “Daha fazla yükle” mode.
- Homepage infinite-scroll mode with accessible manual fallback.
- AJAX progressive post loading with category exclusions and homepage post de-duplication.
- Enhanced slider motion: staggered editorial text entrance, image motion, directional transitions and autoplay progress indicators.
- GitHub Release based native WordPress theme updates for the public repository.

### Changed
- Turkish reading-time word counting now uses Unicode-aware tokenization.
- Slider animation behavior now follows the theme motion setting and prefers-reduced-motion.
- Homepage latest-post loading is configurable from the Customizer.
- Theme SEO output automatically yields to recognized major SEO plugins unless forced.


## 0.6.0 - 2026-10-07

### Added
- Two-level homepage category exclusions: global and section-specific.
- Native homepage slider with latest/category source, split/overlay layouts, autoplay, arrows, dots, swipe and keyboard controls.
- Sortable homepage sections in the WordPress Customizer.
- Light/dark theme with independent dark palette controls and persisted visitor preference.
- Configurable desktop/mobile logo sizing.
- Widgetized 1–4 column footer with compact GPL source notice.
- Image-first article cards with first-content-image fallback and cache.
- Reading time, progress bar, font-size controls, share tools, author box and related posts.
- Breadcrumb UI and BreadcrumbList schema.
- Article schema, Open Graph and Twitter Card metadata.
- Author social profile fields and public author links.
- Archive/PDF search and year filters.
- Asosyoloji: Yazı Listesi classic widget.
- Asosyoloji: Yazı Listesi dynamic Gutenberg block.
- [asosyoloji_posts] shortcode.
- Editorial query filters: author, sorting, date range, offset and sticky handling.
- Gutenberg block category for Asosyoloji components.
- Theme screenshot and translation POT files.

### Changed
- Header redesigned to match the original Asosyoloji structure: masthead/logo above, navigation bar below.
- Homepage and archive layouts made more image-forward and responsive.
- Dark mode contrast and component states refined.
- Theme code reorganized into smaller Customizer, block, widget and SEO modules.

### Fixed
- Duplicate homepage posts across slider/hero/content sections.
- Mobile menu dismissal and keyboard behavior.
- Mobile masthead horizontal overflow.
- Customizer control loading outside the Customizer lifecycle.
- Numerous WordPress Coding Standards issues.

## 0.5.x - 2026-10-07

Development milestone covering the first complete editorial theme implementation.
