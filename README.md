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
- `assets/js/`, `assets/images/`

## Durum

Tema **1.0** sürümündedir. 0.9 ile tasarım sadeleştirildi, stil dosyası baştan yazıldı ve Asosyoloji mobil uygulamasının renk, yazı tipi ve arayüz şeması tüm temanın genel şeması oldu. 1.0 öncesinde canlı içerik QA ve son erişilebilirlik kontrolleri yürütülmektedir.


## Ortak şema: uygulama ve web

Renkler: bordo `#731C3F` (marka, menü çubuğu, alt alan), kağıt zemin `#F6F2EF`, beyaz kartlar, metin `#1E1A1C`, ikincil metin `#5F5559`, çizgi `#E5DCD7`. Karanlık tema: zemin `#141113`, kart `#1D191B`, metin `#F2ECEE`, vurgu `#E58CAE`. Bu değerler Customizer'daki **Renkler** ve **Aydınlık / Karanlık Tema** bölümlerinde varsayılandır. Asosyoloji Haftalık ve Basılı Yayın Arşivi eklentileri aynı değişkenleri kullanır.

### Tipografi

Tema, Asosyoloji mobil uygulamasıyla aynı yazı tiplerini kullanır: başlıklarda **IBM Plex Serif**, gövde metni ve arayüzde **Roboto**. Fontlar `assets/fonts/` altında temayla birlikte gelir (SIL Open Font License); Google Fonts veya başka bir dış servise bağlanılmaz.

Kartlar, köşe yuvarlaklıkları, hap biçimli düğmeler ve kategori çipleri uygulamanın arayüz diliyle eşleşir. Logo için Customizer'da ayrı bir **Karanlık tema logosu** alanı bulunur; hiç logo seçilmemişse temayla gelen Asosyoloji logosu kullanılır.

## Mobil uygulama

**Görünüm → Özelleştir → Asosyoloji Tema Ayarları → Mobil Uygulama** bölümünde App Store kimliği ve Google Play paket adı tanımlanır. Bu değerlerle:

- iOS Safari'de Apple'ın yerleşik akıllı uygulama afişi gösterilir,
- Android'de ve iOS'taki diğer tarayıcılarda kapatılabilen “Uygulamada aç” şeridi çıkar (kapatılırsa 30 gün gösterilmez),
- alt alanda App Store ve Google Play düğmeleri yer alır.

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

Tema, **Görünüm → Asosyoloji Eklentileri** ekranından Asosyoloji Haftalık ve Asosyoloji Basılı Yayın Arşivi eklentilerini GitHub Release paketleriyle kurup etkinleştirebilir. Eklentiler kurulduktan sonra yeni sürümler WordPress'in yerleşik eklenti güncelleme ekranında görünür.

## İletişim ve altbilgi

`[asosyoloji_iletisim]` kısa kodu yerleşik iletişim formunu gösterir. Eski bir Contact Form 7 kısa kodu sayfada ham metin olarak kalırsa tema bunu otomatik olarak aynı forma dönüştürür. Formlar nonce, tuzak alan ve IP özeti üzerinden hız sınırı kullanır; mesajlar Özelleştiricide seçilen bildirim adresine iletilir.

Altbilgideki bülten alanı ziyaretçinin e-posta adresini site veritabanında saklamaz; yöneticinin seçilen adresine abonelik talebi gönderir. Sosyal hesaplar, bülten metni, gizlilik ve yayın künyesi bağlantıları **Görünüm → Özelleştir → Asosyoloji Tema Ayarları → Footer Builder** bölümünden yönetilir.


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

Animasyonlar bilinçli olarak az tutulur: slider geçişi ve küçük renk/opaklık geçişleri. Kaydırma ile beliren içerik animasyonu 0.9 ile kaldırılmıştır.

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


## Önerilen Eklentiler

Tema iki bağımsız eklentiyle birlikte çalışacak şekilde tasarlanmıştır:

- **Asosyoloji Haftalık** — `https://github.com/m4v3r4/asosyoloji-weekly`
- **Asosyoloji Basılı Yayın Arşivi** — `https://github.com/m4v3r4/asosyoloji-print-archive`

**Görünüm → Asosyoloji Eklentileri** ekranında her eklentinin durumu ve GitHub sürümü görülür; kurulu değilse GitHub Release paketinden tek tıkla kurulup etkinleştirilir. Eklentiler kendi güncellemelerini WordPress'in normal **Eklentiler → Güncellemeler** ekranından alır.
