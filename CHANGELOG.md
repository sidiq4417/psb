# Changelog

Semua perubahan penting pada proyek ini dicatat di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/id-ID/1.1.0/), dan proyek ini mematuhi [Semantic Versioning](https://semver.org/lang/id/).

## [Unreleased]

## [v1.0.3] - 2026-08-15

### Ditambahkan
- Fitur *Shortcut Edit Data Calon Siswa* di halaman admin: perbaikan data calon siswa langsung dari daftar, lengkap dengan cascade select provinsi/kabupaten/kecamatan.
- Kolom baru pada tabel `users`: `address_country_id`, `address_province_id`, `address_district_id`, `address_subdistrict_id`, `street_address`.
- Kolom baru pada tabel `application_settings`: `logo`, `favicon`.
- Route & method baru `application-settings/download` untuk mengunduh dokumen pengaturan aplikasi.

### Diperbaiki
- Tabel `users`: konsolidasi kolom nama ibu — `mother_name_parent` diubah menjadi `mother_name` dan duplikat `mother_name` dihapus; `database_mysql_schema.sql` dan migration file kini konsisten (sebelumnya migration berisi kolom `mother_name` ganda yang menyebabkan error "Duplicate column name" saat `php spark migrate`).
- Pemindahan folder upload dari `writable/uploads/` ke `public/uploads/` di seluruh controller & view agar berkas tidak hilang saat update aplikasi.
- Penampilan tahun akademik yang benar di dashboard admin dan laporan statistik (berdasarkan nama tahun akademik, bukan tanggal mulai).
- Dashboard admin kini menampilkan daftar tahun akademik dinamis dari database, bukan rentang tahun hardcoded.
- `SubjectGradeRequirementModel`: perbaikan `orderBy` yang tidak valid.
- Form siswa (alamat, kontak, wali, orang tua, sekolah): perbaikan pengambilan input (`getPost` → `getVar`) dan penyegaran data setelah simpan.
- Pengiriman email pendaftaran (SMTP): `wordWrap` dan `SMTPCrypto` (TLS) untuk keandalan.
- Label tampilan versi di footer: menghilangkan huruf "v" ganda ("SPMB Version v...").
- Label opsi "Jalur Default" pada pengaturan jalur pendaftaran.
- Perbaikan build rilis: dokumen uji di `public/uploads/` tidak lagi ikut ter-distribusi.
- `deploy-production.sh`: baseURL development (mis. `.local`) tidak lagi ikut tersalin ke `.env` paket rilis; diganti placeholder yang harus diisi pengguna.
- `tests/unit/MigrationSmokeTest.php`: dihapus dependensi ke model `QBase` yang tidak ada; suite kini berjalan terhadap MySQL dan otomatis di-skip pada driver lain.

### Keamanan
- File `LICENSE` kini disertakan dalam paket distribusi.

### Lainnya
- `.github/workflows/ci.yml`: pipeline CI (PHP 8.5 + MySQL 8.0) yang menjalankan `composer test`.
- `composer test` kini memakai `--no-coverage` agar tidak bergantung pada driver coverage lokal.
- `PANDUAN_UPGRADE.md` ditulis ulang untuk upgrade v1.0.2 → v1.0.3 (termasuk migrasi database tabel `users`).

### Dokumentasi
- `README.md` ditulis ulang sesuai implementasi aktual (MySQL relasional + CodeIgniter 4).

## [v1.0.2] - 2026-08-13

### Diperbaiki
- Perbaikan berbagai bugs pada model dan kontroler.
- Perbaikan keamanan CSP (Content Security Policy) dan whitelist aset CDN.
- Perbaikan pengecekan email pada `UserModel`.
- Perbaikan akses method pada `RegistrationDocumentModel` dan `RegistrationSubjectGradeModel`.

### Lainnya
- Penambahan dokumentasi pada berbagai model.
- Pembaruan dependencies (`composer.lock`).

## [v1.0.1] - 2026-08-10

### Diperbaiki
- Perbaikan build CSS Tailwind pada lingkungan produksi.

## [v1.0.0] - 2026-08-06

### Ditambahkan
- Migrasi penuh dari **PostgreSQL (JSONB)** ke **MySQL relasional**: 27 tabel relasional baru (`database_mysql_schema.sql`).
- Refactor seluruh lapisan model/controller mengikuti skema relasional.
- Fitur *Perbaikan Data Calon Siswa* (form/simpan data siswa beserta cascade select provinsi/kabupaten/kecamatan).
- Pipeline build rilis `deploy-production.sh` (paket distribusi bersih + `database_spmb.sql` pra-seed).
- Panduan instalasi, penggunaan, dan upgrade (Markdown + PDF).

[Unreleased]: https://github.com/rector/spmb/compare/v1.0.3...HEAD
[v1.0.3]: https://github.com/rector/spmb/compare/v1.0.2...v1.0.3
[v1.0.2]: https://github.com/rector/spmb/compare/v1.0.1...v1.0.2
[v1.0.1]: https://github.com/rector/spmb/compare/v1.0.0...v1.0.1
[v1.0.0]: https://github.com/rector/spmb/releases/tag/v1.0.0
