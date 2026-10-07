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
