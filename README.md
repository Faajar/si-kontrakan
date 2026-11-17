# SI Kontrakan — Sistem Informasi Pengelolaan Rumah Kontrakan

Tugas mata kuliah **Framework Pemrograman Web** — Kelompok 2, Informatika, Fakultas Ilmu Komputer, Universitas Singaperbangsa Karawang (2026).

Aplikasi web untuk mengelola usaha sewa rumah kontrakan milik satu pemilik: **11 unit (pintu) di 9 lokasi**, sewa bulanan atau tahunan. Pemilik memantau status unit, kontrak, pembayaran, biaya perbaikan/upgrade, dan laporan keuangan. Penyewa bisa login untuk melihat kontraknya, tagihan, mengunggah bukti bayar, dan melaporkan kerusakan.

## Teknologi

| Lapisan | Teknologi |
| --- | --- |
| Backend | Laravel 13 (PHP 8.3+) |
| Database | MySQL / MariaDB (XAMPP) |
| Frontend | Blade + Tailwind CSS (Vite) |
| Autentikasi | Laravel Breeze (session) + middleware role |
| Ekspor (rencana) | Dompdf & Laravel Excel |

> Laporan awal menyebut Laravel 11; proyek dibuat dengan versi stabil terbaru (Laravel 13), struktur dan konsepnya sama.

## Peran pengguna

- **Pemilik (admin):** mengelola lokasi, unit, penyewa, kontrak, verifikasi pembayaran, pencatatan perbaikan/upgrade, laporan keuangan.
- **Penyewa:** registrasi akun, melihat unit & masa kontrak, jadwal jatuh tempo, unggah bukti bayar, lapor kerusakan.

Login bisa memakai **email atau nomor WhatsApp** + kata sandi. Registrasi publik selalu menjadi penyewa; akun pemilik hanya dibuat lewat seeder.

## Progres

| Minggu | Isi | Status |
| --- | --- | --- |
| 3 | Skema database semua entitas, autentikasi multi-peran, login email/WA, dashboard pemilik & penyewa, CRUD Lokasi & Unit, pengingat tagihan wa.me, seeder data contoh, feature test | ✅ Selesai |
| berikutnya | CRUD Penyewa + unggah KTP, pengajuan & persetujuan kontrak sewa | ⏳ |
| berikutnya | Tagihan & pembayaran: unggah bukti bayar, verifikasi lunas/nunggak, riwayat | ⏳ |
| berikutnya | Laporan kerusakan penyewa, pencatatan biaya perbaikan & upgrade | ⏳ |
| berikutnya | Laporan keuangan + filter + ekspor PDF/Excel, kalender sewa | ⏳ |

## Skema database

```
lokasi 1──* unit 1──* kontrak_sewa *──1 penyewa 0..1──1 users
                │            │
                │            └──* pembayaran   (status: lunas | nunggak)
                ├──* perbaikan (jenis: kerusakan | upgrade) ──* pengeluaran
                └──* pengeluaran
```

| Tabel | Isi utama |
| --- | --- |
| `users` | nama, email, no_whatsapp, role (`admin` / `penyewa`) |
| `lokasi` | nama, alamat, latitude/longitude, link Google Maps, lingkungan sekitar |
| `unit` | nomor unit, tarif bulanan/tahunan, jumlah kamar, luas, fasilitas, status (`kosong`/`terisi`/`perbaikan`), ketentuan penghuni, izin hewan |
| `penyewa` | data diri, NIK, no WhatsApp, foto KTP (jaminan pengganti deposit) |
| `kontrak_sewa` | unit ↔ penyewa, periode (`bulanan`/`tahunan`), tanggal mulai/selesai, tarif, status (`diajukan`/`aktif`/`ditolak`/`selesai`), file perjanjian |
| `pembayaran` | jatuh tempo, nominal, tanggal bayar, metode (`tunai`/`transfer`), bukti bayar, status (`lunas`/`nunggak`) |
| `perbaikan` | kerusakan atau upgrade per unit, pelapor, foto, status pengerjaan |
| `pengeluaran` | rincian biaya (material, upah tukang, operasional) per unit/perbaikan |

Batasan sesuai laporan: tanpa deposit, tanpa denda, status bayar hanya lunas/nunggak, tanpa payment gateway, pengingat WA lewat tautan `wa.me` (bukan bot).

## Menjalankan di komputer sendiri

Prasyarat: PHP 8.3+, Composer, Node.js, XAMPP (MySQL/MariaDB menyala).

```bash
git clone <url-repo-ini> si-kontrakan
cd si-kontrakan
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
```

Buat database `kontrakan` di phpMyAdmin (atau `CREATE DATABASE kontrakan;`), sesuaikan `DB_USERNAME`/`DB_PASSWORD` di `.env` bila perlu, lalu:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Buka http://localhost:8000.

### Akun demo (dari seeder, hanya untuk pengembangan lokal)

| Peran | Login | Kata sandi |
| --- | --- | --- |
| Pemilik | `admin@kontrakan.test` | `password` |
| Penyewa | `penyewa@kontrakan.test` atau `081200000001` | `password` |

Data lokasi/unit/penyewa di seeder hanyalah contoh dan perlu diganti dengan data asli pemilik.

## Test

```bash
php artisan test
```

## Anggota kelompok

- Lacinta Theo Gracia Hutasoit (2410631170078)
- Rois Alif Pradipa (2410631170107)
- Fajar (2410631170018)
- Nadya Febriana Rachmadanty
