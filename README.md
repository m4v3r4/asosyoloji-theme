# Asosyoloji WordPress Theme

Asosyoloji için özel geliştirilen WordPress dergi teması.

## Hedefler

- Mevcut Asosyoloji içerik yapısını ve basılı PDF arşivini korumak
- Modern, editoryal ve okunabilir bir tasarım oluşturmak
- Tema ayarlarının mümkün olduğunca WordPress yönetim arayüzünden yapılabilmesini sağlamak
- Renk, logo, tipografi ve ana sayfa düzeni gibi seçenekleri kod değiştirmeden yönetmek
- Hafif, bağımsız ve page-builder gerektirmeyen bir yapı kullanmak

## Geliştirme yaklaşımı

Tema klasik WordPress PHP template yapısını kullanır. Görsel özelleştirmeler WordPress Customizer ve `theme.json` üzerinden yönetilir.

Başlangıç yapısı:

- `style.css` — tema tanımı ve temel stiller
- `functions.php` — tema kurulumu ve dosya yükleme
- `inc/customizer.php` — yönetim paneli tema seçenekleri
- `inc/dynamic-css.php` — Customizer değerlerinden üretilen CSS
- `header.php`, `footer.php`
- `index.php`, `front-page.php`, `single.php`, `page.php`
- `archive.php`, `category.php`, `author.php`, `search.php`, `404.php`
- `template-parts/` — tekrar kullanılabilir içerik parçaları
- `assets/css/`, `assets/js/`

## Durum

Tema aktif geliştirme aşamasında **0.6 beta** seviyesindedir. Ana sayfa, slider, kategori akışları, yazı sayfası, yazar sayfaları, arşiv/PDF araçları, widget/blok sistemi, light/dark tema ve WordPress Customizer seçenekleri çalışır durumdadır. 1.0 öncesinde canlı içerik QA ve son erişilebilirlik kontrolleri yürütülmektedir.


## Footer Builder

Tema dört ayrı footer widget alanı sağlar:

- Footer Sütun 1
- Footer Sütun 2
- Footer Sütun 3
- Footer Sütun 4

Sütun sayısı **Görünüm → Özelleştir → Asosyoloji Tema Ayarları → Footer Builder** bölümünden 1–4 arasında seçilebilir. Her sütunun içeriği WordPress'in **Görünüm → Bileşenler** arayüzünden blok/widget eklenerek düzenlenebilir.

Footer altında kompakt **GPL-3.0-or-later · Tema kaynak kodu** bilgisi gösterilebilir. Bu satır Customizer üzerinden açılıp kapatılabilir; kaynak kodu adresi değiştirilebilir.

## Lisans

Tema **GPL-3.0-or-later** lisansı ile özgür yazılım olarak paylaşılır. Ayrıntılar için `LICENSE.md` dosyasına bakın.


## Geliştirme ve kalite kontrol

PHP/JS syntax kontrolleri her push ve pull request'te GitHub Actions ile çalışır.

Yerel geliştirme araçları:

```bash
composer install
composer lint:php
composer phpcs
```

Çeviri şablonu üretmek için WP-CLI ile:

```bash
bash bin/make-pot.sh
```

Kurulabilir WordPress tema ZIP'i üretmek için:

```bash
bash bin/package-theme.sh
```

Çıktı:

```text
build/asosyoloji-theme.zip
```

GitHub Actions artifact depolama alanı gerekmeden CI, oluşturulan ZIP'in bütünlüğünü de doğrular.


## Ana sayfa kategori hariç tutma

Ana sayfadaki yazı akışlarında kategori dahil etmenin yanında kategori hariç tutma da desteklenir.

**Görünüm → Özelleştir → Asosyoloji Tema Ayarları → Ana Sayfa: Genel** bölümünde seçilen kategoriler slider, hero, son yazılar ve kategori bölümlerinde genel olarak gösterilmez. Duyurular gibi akış dışında tutulmak istenen kategoriler için bu ayar önerilir.

Bölüm bazında ayrıca:
- Ana Sayfa: Slider
- Ana Sayfa: Yazı Akışı
- Ana Sayfa: Kategori Bölümü

içinde ek hariç kategori seçimleri yapılabilir.

## Asosyoloji Yazı Listesi

Tema, **Görünüm → Bileşenler** ekranında kullanılabilen **Asosyoloji: Yazı Listesi** widget'ını sağlar.

Desteklenen görünümler:
- Görselli liste
- Kompakt liste
- Kart grid
- Bir büyük + liste

Widget içinde kategori, hariç kategoriler, yazı sayısı, görsel, özet ve meta bilgileri ayarlanabilir.

Sayfalarda Gutenberg editöründen **Asosyoloji: Yazı Listesi** bloğu eklenebilir. Aynı ayarlar blok sağ panelinden yönetilir.

Shortcode kullanmak isteyenler için:

```text
[asosyoloji_posts title="Son Yazılar" category="yazilar" exclude="duyurular" count="6" layout="grid"]
```

`category` ve `exclude` alanlarında kategori slug veya ID kullanılabilir. `layout` değeri `list`, `compact`, `grid` veya `feature` olabilir.


### Gelişmiş yazı listesi filtreleri

**Asosyoloji: Yazı Listesi** widget ve Gutenberg bloğu şu ek filtreleri destekler:

- Yazar seçimi
- Sıralama: yayın tarihi, güncellenme, başlık, yorum sayısı, rastgele
- Artan / azalan sıralama
- Başlangıç ve bitiş tarihi
- Offset: ilk N yazıyı atlama
- Sabitlenmiş (sticky) yazıları dahil etme

Shortcode örneği:

```text
[asosyoloji_posts title="Arşiv Seçkisi" category="yazilar" exclude="duyurular" author="12" count="8" layout="grid" orderby="date" order="DESC" after="2026-01-01" before="2026-12-31" offset="0" sticky="0"]
```


## Changelog

Sürüm değişiklikleri için `CHANGELOG.md` dosyasına bakın.


## Asosyoloji Duyurular

Tema, normal yazı kartlarından ayrı bir görünüme sahip **Asosyoloji: Duyurular** widget ve Gutenberg bloğu içerir.

Varsayılan olarak `duyurular` kategorisini kullanır. İstenirse başka bir kategori seçilebilir.

Gösterim seçenekleri:
- Tarih rozeti
- Duyuru etiketi
- Başlık
- Kısa açıklama
- Duyuru detayları bağlantısı
- Normal / kompakt görünüm

Shortcode:

```text
[asosyoloji_duyurular title="Duyurular" category="duyurular" count="5" excerpt="1" date="1" button="1" compact="0"]
```


## Asosyoloji Dergi Arşivi

**Asosyoloji: Dergi Arşivi** widget'ı basılı dergi/PDF sayılarını manuel olarak listeler.

Her sayı için:
- Sayı / başlık
- PDF dosyası
- Kapak görseli

tanımlanır.

PDF ve kapak görselleri WordPress Medya Kütüphanesi üzerinden seçilebilir. Widget iki görünüm sunar:

- Kapak grid
- Liste

Kapak veya başlığa tıklandığında PDF yeni sekmede açılır. Bu bileşen mevcut arşiv/PDF içeriklerini değiştirmez; yalnızca istenen sayıları farklı alanlarda tekrar sunmak için kullanılır.
