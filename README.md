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

Tema aktif geliştirme aşamasında **0.7 beta** seviyesindedir. Ana sayfa, slider, kategori akışları, yazı sayfası, yazar sayfaları, arşiv/PDF araçları, widget/blok sistemi, light/dark tema ve WordPress Customizer seçenekleri çalışır durumdadır. 1.0 öncesinde canlı içerik QA ve son erişilebilirlik kontrolleri yürütülmektedir.


## Footer Builder

Tema dört ayrı footer widget alanı sağlar:

- Footer Sütun 1
- Footer Sütun 2
- Footer Sütun 3
- Footer Sütun 4

Sütun sayısı **Görünüm → Özelleştir → Asosyoloji Tema Ayarları → Footer Builder** bölümünden 1–4 arasında seçilebilir. Her sütunun içeriği WordPress'in **Görünüm → Bileşenler** arayüzünden blok/widget eklenerek düzenlenebilir.

Footer altında kompakt **GPL-3.0-or-later · Tema kaynak kodu** bilgisi gösterilebilir. Bu satır Customizer üzerinden açılıp kapatılabilir; kaynak kodu adresi değiştirilebilir.

## Lisans

Tema **GPL-3.0-or-later** lisansı ile özgür yazılım olarak paylaşılır. Tam GNU GPL v3 metni kökteki `LICENSE` dosyasındadır.

## GitHub sürümleri ve WordPress güncellemesi

Tema GitHub Release sürümlerini WordPress'in yerleşik tema güncelleme sistemi üzerinden kontrol eder.

Yeni sürüm yayınlamak için:

1. `style.css` içindeki `Version` ve `ASOSYOLOJI_VERSION` aynı sürüm olmalıdır.
2. Aynı sürümle bir tag oluşturulur: örneğin `v0.7.1`.
3. GitHub'daki tek release workflow'u kurulabilir `asosyoloji-theme.zip` paketini üretip Release'e ekler.
4. WordPress yeni Release'i gördüğünde **Görünüm → Temalar** ekranında normal güncelleme bildirimi gösterir.

Repo public olduğunda token veya ek WordPress yapılandırması gerekmez.


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


## Basılı Sayılar (PDF)

**Asosyoloji: Basılı Sayılar (PDF)** widget'ı yalnızca basılı dergi sayılarını temsil eden PDF dosyalarını manuel olarak listeler. WordPress yazı arşivi veya yazı kategorileriyle bağlantısı yoktur.

Her sayı için:
- Sayı / başlık
- PDF dosyası
- Kapak görseli

tanımlanır.

PDF ve kapak görselleri WordPress Medya Kütüphanesi üzerinden seçilebilir. Widget iki görünüm sunar:

- Kapak grid
- Liste

Kapak veya başlığa tıklandığında PDF yeni sekmede açılır. Her sayı bağımsız olarak **başlık + PDF dosyası + kapak görseli** ile tanımlanır. Kapak veya başlığa tıklandığında ilgili PDF yeni sekmede açılır.


## Animasyonlar

**Görünüm → Özelleştir → Asosyoloji Tema Ayarları → Aydınlık / Karanlık Tema** bölümünden arayüz animasyonları açılıp kapatılabilir.

Animasyon yoğunluğu:
- Sade
- Normal

Animasyon sistemi:
- Bölüm ve kart scroll reveal
- Hafif stagger gecikmeleri
- Kart hover yükselmesi
- Görsel hover zoom
- Sticky header scroll gölgesi
- Buton ve bağlantı geçişleri

Ziyaretçinin `prefers-reduced-motion` sistem tercihi her zaman önceliklidir; bu durumda hareketler otomatik olarak devre dışı bırakılır.


## SEO / GEO 0.7

**Görünüm → Özelleştir → Asosyoloji Tema Ayarları → SEO / GEO** bölümünde:

- Tema SEO çıktısı: Otomatik / Her zaman tema / Kapalı
- Yayıncı adı ve açıklaması
- Yayıncı sosyal / profil URL'leri
- Yayın ilkeleri URL'si
- Varsayılan sosyal paylaşım görseli
- Otomatik İçindekiler
- GEO özet / “Bu yazıda” kutusu
- Kaynaklar bölümü
- IndexNow
- llms.txt

ayarları bulunur.

Tema schema graph'ı şu entity'leri birbirine bağlar:

- Organization
- WebSite
- WebPage
- Article
- ProfilePage
- Person
- BreadcrumbList
- PublicationIssue

Yazı editöründeki **Asosyoloji SEO / GEO** kutusunda:
- SEO açıklaması
- Kısa özet
- “Bu yazıda” maddeleri
- Ana konular / entity'ler
- Kaynak URL'leri

tanımlanabilir.

Tema SEO modu varsayılan olarak **Otomatik** çalışır. Yoast SEO, Rank Math, SEOPress veya AIOSEO gibi bilinen SEO eklentileri tespit edilirse tema kendi meta/schema çıktısını tekrar basmaz.

### Ana sayfa progressive loading

**Ana Sayfa: Yazı Akışı** bölümünde:
- Kapalı
- Daha fazla yükle butonu
- Aşağı indikçe otomatik yükle

modlarından biri seçilebilir. Her yüklemede getirilecek yazı sayısı da panelden ayarlanır.

Kategori hariç tutma ve slider/hero tekrar engelleme mantığı AJAX ile gelen yazılarda da korunur.


## Önerilen Eklenti

Tema, bağımsız **Asosyoloji Haftalık** eklentisini önerilen companion plugin olarak tanır.

WordPress yönetiminde:

**Görünüm → Asosyoloji Eklentileri**

ekranından eklentinin durumu görülebilir.

- Kurulu değilse GitHub Release paketinden tek tıkla kurulur ve etkinleştirilir.
- Kurulu fakat pasifse etkinleştirme düğmesi gösterilir.
- Etkinse etkinlik yönetim ekranına bağlantı gösterilir.
- Eklenti aktif olduğunda öneri bildirimi kaybolur.

Eklenti deposu:

`https://github.com/m4v3r4/asosyoloji-weekly`

Eklenti kendi güncellemelerini de GitHub Release sürümlerinden WordPress'in normal **Eklentiler → Güncellemeler** sistemi üzerinden alır.
