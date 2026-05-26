# UAT Release Checklist

Checklist ini dipakai sebelum memulai sesi UAT agar tim tidak perlu setup manual dari nol.

## 1. Persiapan Environment

- pastikan `.env` sudah mengarah ke database UAT/local yang benar
- pastikan ekstensi PHP `pdo_pgsql` dan `pgsql` aktif
- jalankan migrasi dan seed:
  - `php artisan migrate:fresh --seed`
- pastikan aplikasi bisa diakses:
  - app: `http://127.0.0.1:8000`
  - admin panel: `http://127.0.0.1:8000/admin`

## 2. Kredensial UAT

Gunakan akun berikut (default):

- `admin.uat@example.com` / `password` (role `admin`)
- `sales.uat@example.com` / `password` (role `sales`)
- `manager.uat@example.com` / `password` (role `manager`)

Catatan:
- akun admin bootstrap juga tetap tersedia via `ADMIN_EMAIL` dan `ADMIN_PASSWORD` di `.env`
- ganti password default jika environment dipakai banyak orang

## 3. Validasi Seed Master Data

Pastikan data berikut tersedia:

- role: `sales`, `manager`, `admin`
- customer: `PT Logistik Nusantara Demo`
- customer contact: pickup + drop
- vendor: `CV Armada Truk Demo`
- vendor contact: `Bagus Vendor Ops`
- location: `Jakarta Distribution Center`, `Bandung Retail Hub`
- transport mode: `Trucking`
- vehicle type: `CDD Box`
- cost category: `Freight`, `Handling`

## 4. Validasi Seed Transaksi

Pastikan data transaksi contoh tersedia:

- inquiry: `INQ-UAT-0001`
- scenario: `SCN-001` (`Direct Trucking Option`)
- minimal 1 leg dan cost item terkait
- quote approval queue: `Q-UAT-0001` (status `waiting_approval`)
- quote history sample: `Q-UAT-0002` (status `approved`)

## 5. UAT Flow Checklist

Checklist flow yang wajib diuji:

- login sesuai role (`sales`, `manager`, `admin`)
- create/edit inquiry
- buka scenario builder dari inquiry
- add/edit leg
- add/edit cost item
- lihat pricing summary
- create quote draft
- manager approve/reject di halaman review quote
- cek approval queue dan quote history

## 6. Catatan Eksekusi UAT

- dokumentasikan bug/perbaikan per flow (inquiry, scenario, pricing, approval, history)
- catat akun role yang dipakai saat bug muncul
- simpan screenshot untuk error yang tidak konsisten
- pastikan issue temuan UAT ditulis dengan langkah reproduksi
