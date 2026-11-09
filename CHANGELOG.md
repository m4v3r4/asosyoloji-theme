# Changelog

All notable changes to the Asosyoloji WordPress Theme are documented here.

## 1.0.0 - 2026-11-09

İlk kararlı sürüm. Depo geçmişi bu sürümle temiz bir başlangıca alındı; önceki sürümlerin notları aşağıda korunmuştur.

### Added
- Mobil uygulama entegrasyonu: iOS Safari için Apple Smart App Banner, Android ve diğer iOS tarayıcıları için kapatılabilir “Uygulamada aç” şeridi (30 gün hatırlanır), alt alanda App Store ve Google Play düğmeleri. Kimlikler Customizer → **Mobil Uygulama** bölümünden yönetilir (varsayılan: App Store `6823511120`, Play `com.asosyoloji.app`).
- “Yazıyı dinle” (Local TTS) oynatıcısı uygulamadaki oynatıcı kartı görünümünde: dolu bordo oynat düğmesi, kalın yuvarlak zaman çizgisi, temanın açık/koyu renkleri.

### Changed
- Fontlar WOFF2 biçiminde (toplam ~%24 daha küçük); ön yükleme WOFF2'ye göre güncellendi.
- Slider'ın ilk görseli `loading="eager"` ve `fetchpriority="high"` ile yüklenir; diğer slaytlar tembel yüklenir.

## 0.9.1 - 2026-11-09

### Added
- Görseli olmayan yazılarda kartlarda, slider'da ve listelerde site logosu gösterilir (karanlık temada açık renkli logo).
- Alt alanda site adı yerine logo görseli.
- Animasyonlar: sayfa başlıklarının girişte yumuşakça belirmesi, ekranın altındaki kartların kaydırınca sırayla görünmesi, kartlarda hafif yükselme ve görsel yakınlaşması, slider'da yavaş yakınlaşan fotoğraf ve sırayla gelen metin, menü çekmecesi ve arama paneli geçişleri. İçerik hiçbir zaman gizli kalmaz; hareketi azalt tercihi ve Customizer ayarı geçerlidir.

### Changed
- Kategori listeleme sayfaları (Güncel, Söyleşi, Çeviri, Misafir vb.) artık arşiv sayfalarıyla aynı geniş düzeni ve aynı yazı kartını kullanır; `[asosyoloji_posts layout="grid"]` ve Yazı Listesi bloğunun grid görünümü standart karta geçti.
- Sayfa içine yerleştirilen listeler makale tipografisinden (bordo bağlantı rengi, başlık boşlukları) etkilenmez.

### Removed
- “ASOSYOLOJİ” yazılı SVG yedek görseli.

## 0.9.0 - 2026-11-09

### Added
- Asosyoloji mobil uygulamasının renk şeması genel şema olarak benimsendi: bordo `#731C3F`, kağıt zemin `#F6F2EF`, beyaz kartlar, mürekkep metin `#1E1A1C`; karanlık temada `#141113` zemin, `#1D191B` kartlar ve gül `#E58CAE` vurgu. Menü çubuğu ve alt alan marka bordosunu kullanır.
- Güncellemede eski renk, yazı tipi, slider düzeni ve logo genişliği ayarları bir kez sıfırlanır; eski değerler `asosyoloji_scheme_backup` seçeneğinde yedeklenir.
- Asosyoloji mobil uygulamasıyla ortak tasarım dili: başlıklarda **IBM Plex Serif**, gövde ve arayüzde **Roboto**. Fontlar temaya gömülüdür (Latin + Türkçe alt kümesi, WOFF, OFL lisanslı); dış servise istek yapılmaz.
- Uygulamadaki gibi kağıt tonlu zemin üzerinde beyaz kartlar, 20 px köşe yuvarlaklığı, hap biçimli düğme/etiket/arama alanları ve uzayan aktif slider noktası.
- Ana sayfada ve kategori sayfalarında uygulamadaki gibi yatay kategori çipleri (Customizer'dan kapatılabilir).
- Temayla gelen Asosyoloji logosu (açık ve karanlık sürüm) ve Customizer'da ayrı **Karanlık tema logosu** alanı.
- Varsayılan slider düzeni uygulamanın kahraman kartı gibi “Görsel üzerinde metin”.

### Changed
- Tasarım sadeleştirildi.
- `style.css` sürüm sürüm üst üste eklenen yama katmanlarından arındırılıp baştan, tek düzende yazıldı (118 KB → ~50 KB).
- Gölge, yuvarlatma, rozet, kaydırma ile beliren animasyonlar ve tekrarlanan üst etiketler (kicker) kaldırıldı.
- Yazı sayfası tek sütuna geçti: kategori, başlık, spot, künye, geniş görsel ve tek satırlık okuma/paylaşım araçları.
- Yazı kartlarında kategori görsel üstü rozet yerine başlık üstünde metin olarak gösterilir; ayrı “Devamını oku” bağlantısı kaldırıldı.
- Header ikonları (tema, arama, menü) tek tip SVG ikonlara dönüştürüldü; mobilde boş mor menü şeridi artık görünmez.
- Üst sosyal medya şeridi ve menü ayırıcıları varsayılan olarak kapalı.
- Etkinlik sayfasında aynı bilgilerin ikinci kez gösterildiği yan kutu kaldırıldı.
- Eklenti yönetimi tek ve genel bir modülde birleştirildi; öneri bildirimi yalnızca Pano, Temalar ve Eklentiler ekranlarında tek bildirim olarak gösterilir.

### Fixed
- Kaydırma animasyonu nedeniyle kartların görünür alana girene kadar boş kalması (tam sayfa ekran görüntüleri, yazdırma ve bazı tarayıcılar) giderildi.
- Ana sayfada iki `h1` bulunması düzeltildi (öne çıkan başlık artık `h2`).
- Sayfalı arşivlerde canonical adresin her zaman ilk sayfayı göstermesi düzeltildi.
- Schema, yazar sayfası ve breadcrumb verisinde “admin” kullanıcı adının görünmesi engellendi.
- Kendi `id` değeri olan başlıklarda İçindekiler bağlantısının bozulması düzeltildi.
- Kart görsellerinde test ortamı için yazılmış, her istekte canlı siteye uzak istek atan geçici kod kaldırıldı.
- Silinmiş öne çıkan görsellerde boş kart görseli yerine yedek görsele düşülür; görselsiz içerik her istekte yeniden taranmaz.
- “Etkinlikleri Yönet” bağlantısı eski `asosyoloji_event` yerine `em_event` içerik tipine gider.
- GitHub erişilemediğinde veya sınır aşıldığında her yönetim sayfasında 8 saniyelik istek atılması önlendi (hata 15 dk önbelleklenir).
- Eklenti kurulumu sonrası sonuç mesajı artık gösterilir.
- Form gönderiminden sonra sayfa doğrudan bildirim alanına kayar.
- `[asosyoloji_posts image="0"]` parametresi çalışır.
- Customizer'daki sürükle-bırak bölüm sırası kontrolünün stilsiz görünmesi düzeltildi.
- Hiçbir etkisi kalmamış Customizer ayarları kaldırıldı: menü bar rengi, özel menü rengi, karanlık footer rengi, animasyon yoğunluğu.

## 0.8.1 - 2026-10-14

### Fixed
- Mobil menü çekmecesinin header yüksekliğine sıkışarak bağlantıları gizlemesi düzeltildi.
- Menü açıkken ekran yüksekliği ve yön değişimlerinde çekmece konumu yeniden hesaplanır.

### Changed
- Header içindeki “Ara” metni, erişilebilir adı korunan mercek ikonuyla değiştirildi.
- Mobil header kontrollerinin dokunma alanları en az 44 × 44 piksel olarak sabitlendi.

## 0.8.0 - 2026-10-14

### Added
- Contact Form 7 kısa kodu işlenemediğinde devreye giren yerleşik iletişim formu eklendi.
- Altbilgiye onay kutulu bülten talep formu; gizlilik politikası ve yayın künyesi bağlantıları eklendi.
- Yapılandırılabilir form bildirim adresi ve altbilgi alanları Özelleştiriciye eklendi.
- WordPress site simgesi tanımlı değilse kullanılan Asosyoloji SVG faviconu eklendi.

### Changed
- Duyuru özetlerindeki uzun ve boşluksuz bağlantılar mobil ekranda güvenli biçimde satıra bölünür.
- Slider görsel bağlantıları erişilebilir ad kazandı; slider noktalarının dokunma alanı en az 44 × 44 piksele çıkarıldı.
- İletişim kısa kodu SEO açıklamasında ham metin olarak görünmez.

## 0.7.14 - 2026-10-12

### Added
- Asosyoloji Basılı Yayın Arşivi, tema eklentileri ekranına GitHub Release üzerinden tek tık kurulum ve etkinleştirme desteğiyle eklendi.
- Eklentinin kurulu, etkin ve GitHub sürümü durumları tema yönetim ekranında gösterilir.

## 0.7.13 - 2026-10-12

### Changed
- Sliderdaki Durdur/Oynat kontrolü kaldırılarak yön okları ve slayt noktaları sadeleştirildi.
- “Devamını oku” bağlantısı masaüstü, mobil ve görsel üstü slider düzenlerinde daha belirgin bir butona dönüştürüldü.

## 0.7.12 - 2026-10-12

### Changed
- Slider başlıkları içerik uzunluğuna ve ekran genişliğine göre otomatik ölçeklenir; özetler sınırlanır ve kontrol alanı için güvenli boşluk ayrılır.
- Yazı kartı ve widget görsellerinin flex düzeninde sıfır yüksekliğe sıkışması engellendi; ana sayfa yazı widgetında görseller zorunlu hale getirildi.
- Footer sosyal medya alanı ile telif, lisans ve kaynak bağlantısı metinleri Özelleştirici üzerinden yönetilebilir hale getirildi.

## 0.7.11 - 2026-10-07

### Changed
- Ana menü, alt menüler ve footer arka planları marka moru `#540048` altında birleştirildi.
- Menü ve footer metinleri açık ve karanlık temada beyaz, ayraçlar ise erişilebilir yarı saydam tonda sabitlendi.
- Özelleştiriciye masaüstü ve mobil için ayrı slider yüksekliği kontrolleri eklendi; mobil değer `0` olduğunda yükseklik içeriğe göre otomatik kalır.

## 0.7.10 - 2026-10-07

### Changed
- Yazı akışı kartları öne çıkarılmış görseli kullanır; test ortamındaki bozuk medya bağlantılarında kaynak sitenin gerçek kapak görseli güvenli biçimde bulunup önbelleğe alınır.
- Eski `http://asosyoloji.com` içerik görselleri güvenli HTTPS adresine uyarlanır ve eski negatif görsel önbelleği yenilenir.
- Yazı detayında başlık küçültülüp görsel alanı büyütülerek masaüstü ve mobil oranları dengelenmiştir.
- Karanlık temada logonun kaybolmaması için beyaz zemin ve ince çerçeve eklenmiştir.
- Ana sayfa slider yüksekliği, başlık boyutu ve içerik boşlukları azaltılmıştır.

## 0.7.9 - 2026-10-07

### Changed
- Ana sayfaya semantik H1 ve boş site açıklamaları için SEO varsayılanı eklendi.
- Test/staging alan adları otomatik olarak `noindex, nofollow` işaretlenir.
- Slider için kalıcı durdur/oynat kontrolü ve görselsiz içerik yer tutucusu eklendi.
- Eksik kart görselleri için tutarlı editoryal SVG kullanımı sağlandı.
- Tekil etkinlik şablonuna şehir, organizatör, ücretsiz katılım ve takvim bağlantıları eklendi.
- Mobil menü kaydırmayı kilitleyen çekmece görünümüne geçirildi.
- Boş footer sütunları, geçersiz sosyal profil bağlantıları ve eski llms.txt sayfaları temizlendi.
- Temel güvenlik başlıkları ve GitHub Actions PHP/JavaScript kalite kontrolü eklendi.

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
