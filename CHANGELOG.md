# Changelog

Semua perubahan penting pada theme ini dicatat di file ini.

## [2.0.0] - 2026-02-27

### Breaking Changes
- Menghapus shortcode `[resize-thumbnail]`.
- Menghapus ketergantungan child theme terhadap Kirki untuk pengaturan customizer.
- Menghapus logic hit counter lokal di child theme (tracking diserahkan ke plugin `velocity-addons`).

### Added
- Menambahkan helper thumbnail terpusat:
  - `velocity_mobil3_no_image_url()`
  - `velocity_mobil3_post_thumb_url()`
  - `velocity_mobil3_render_post_thumb()`
- Thumbnail sekarang memakai Bootstrap 5 ratio (`ratio-4x3`), fallback ke `img/no-image.webp`, dan selalu bisa diberi link ke post.
- Menambahkan struktur metabox berbasis schema array di `inc/produk.php` agar lebih scalable dan mudah maintenance.
- Menambahkan dukungan renderer/sanitizer field standar: `text`, `textarea`, `number`, `email`, `url`, `checkbox`, `radio`, `select`, `date`, `file`.

### Changed
- Customizer child theme dimigrasikan ke native WordPress API untuk field `welcome_text`.
- Sumber warna tema mengikuti parent theme (`primary_color`) tanpa override field warna/background di child.
- Tampilan dan output thumbnail produk di template child diganti ke helper baru.
- Perbaikan alignment ikon submenu pada offcanvas menu mobile.
- Metadata theme diperbarui ke versi `2.0.0`.

### Fixed
- Warning `Undefined variable $s` pada search input di `inc/part-header.php`.
- Perbaikan class Bootstrap lama:
  - `sr-only` -> `visually-hidden`
  - `input-group-append` dihapus (markup BS5)
  - `pr-md-0` -> `pe-md-0`
  - typo `table-reponsive` -> `table-responsive`
- Menghapus penggunaan FontAwesome pada bullet list dan menggantinya dengan Bootstrap SVG-based icon (CSS mask/data URI).

### Notes
- Meta key lama tetap dipertahankan (tanpa prefix/suffix): `harga`, `warna`, `mesin`, `transmisi`.
- Parent `single.php` tidak diubah; caption thumbnail parent tetap mengikuti implementasi parent theme.

## [1.0.0]
- Initial release.
