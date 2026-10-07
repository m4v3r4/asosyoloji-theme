# Changelog

All notable changes to the Asosyoloji WordPress Theme are documented here.

## 0.7.8 - 2026-10-07

### Changed
- Post list widgets now show images in compact mode instead of hiding them.
- Widget images use consistent 4:3 media boxes across list, compact, grid and feature layouts.
- Image-less widget posts use the existing site-logo/theme fallback artwork without breaking card proportions.
- Grid widget cards use consistent title/excerpt rhythm and safer long-text wrapping.
- Single post pages now use a dedicated editorial hero combining category, title, metadata, deck and featured image.
- Single post reading layout, headings, wide/full media and mobile presentation were refined to match the Asosyoloji visual system.


## 0.7.7 - 2026-10-07

### Added
- Enhanced printed issues PDF widget with Media Library PDF selection.
- PDF attachment IDs are stored so WordPress-generated first-page previews can be used automatically as issue covers.
- Issue titles are auto-filled from the selected PDF attachment and remain editable.
- Manual cover selection remains available as a fallback when PDF preview generation is unavailable.

### Changed
- Printed issues widget defaults to a list layout with cover, issue title and **Oku** action.
- PDF cover cards now use a consistent 3:4 cover ratio in list and grid views.


## 0.7.6 - 2026-10-07

### Added
- Dedicated `single-em_event.php` event detail template.
- Editorial event hero with event type, date range, time, venue and ticket information.
- Event share actions and calendar-add integration when Asosyoloji Haftalık is active.
- Responsive event content layout with sticky event information panel on desktop.

### Changed
- Event pages now use a purpose-built Asosyoloji detail layout instead of the generic single-post presentation.


## 0.7.5 - 2026-10-07

### Added
- Automatic card image fallback: featured image, first content image, site logo, then built-in Asosyoloji fallback artwork.
- Built-in fallback card artwork for posts without any usable image.

### Changed
- Article cards now use standardized visual height, title depth and excerpt depth for cleaner grids.
- Fallback/logo artwork uses contain mode rather than being cropped.
- Dark theme defaults were refined toward the original Asosyoloji site's near-black, warm neutral visual language.


## 0.7.4 - 2026-10-07

### Added
- Configurable main navigation bar with accent, light, dark and custom background modes.
- Main menu alignment options: left, center, right and spread.
- Main menu width, density, separators, capitalization and active-item style controls.
- Optional social media bar with Instagram, X, Facebook, YouTube, LinkedIn, Mastodon and Telegram links.
- Lightweight inline SVG social icons without an external icon library.

### Changed
- Main menu bar now uses the theme accent color by default.
- Desktop menu toggle is hidden; the collapsible menu button is mobile-only.
- Article cards now use a consistent vertical layout: image, title, excerpt and metadata.
- Article category is displayed as an overlay in the upper-right corner of the image.
- Removed breadcrumb trails from primary page, post, archive, category and author templates.
- Hardened article cards against long-word, URL and excerpt overflow.


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
