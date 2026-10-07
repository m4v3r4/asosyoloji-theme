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

İlk tema iskeleti hazırlanıyor. Tasarım ve WordPress panel seçenekleri aşamalı olarak geliştirilecek.


## Footer Builder

Tema dört ayrı footer widget alanı sağlar:

- Footer Sütun 1
- Footer Sütun 2
- Footer Sütun 3
- Footer Sütun 4

Sütun sayısı **Görünüm → Özelleştir → Asosyoloji Tema Ayarları → Footer Builder** bölümünden 1–4 arasında seçilebilir. Her sütunun içeriği WordPress'in **Görünüm → Bileşenler** arayüzünden blok/widget eklenerek düzenlenebilir.

Footer altında özgür yazılım lisans açıklaması ve kaynak kodu bağlantısı gösterilebilir. Bu alan da Customizer üzerinden açılıp kapatılabilir ve düzenlenebilir.

## Lisans

Tema **GPL-3.0-or-later** lisansı ile özgür yazılım olarak paylaşılır. Ayrıntılar için `LICENSE.md` dosyasına bakın.
