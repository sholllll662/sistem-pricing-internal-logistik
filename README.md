# Sistem Pricing Internal Logistik

Fondasi aplikasi untuk internal pricing logistik dengan stack:
- Laravel 12
- Livewire (Laravel Breeze starter kit)
- Filament Admin Panel
- PostgreSQL

## Prasyarat

- PHP 8.2+
- Composer
- Node.js + npm
- PostgreSQL
- Ekstensi PHP `pdo_pgsql` dan `pgsql` aktif

## Setup Lokal

1. Install dependency:

```bash
composer install
npm install
```

2. Salin env lalu sesuaikan koneksi database PostgreSQL:

```bash
cp .env.example .env
```

3. Generate app key, migrate, dan seed admin awal:

```bash
php artisan key:generate
php artisan migrate --seed
```

4. Jalankan aplikasi:

```bash
php artisan serve
```

5. Akses:
- Aplikasi: `http://127.0.0.1:8000`
- Admin panel Filament: `http://127.0.0.1:8000/admin`

## Kredensial Admin Awal

Seeder menggunakan variabel env berikut:
- `ADMIN_NAME` (default: `Admin User`)
- `ADMIN_EMAIL` (default: `admin@example.com`)
- `ADMIN_PASSWORD` (default: `password`)

Ubah variabel ini di `.env` sebelum menjalankan `php artisan migrate --seed`.

## Seed Data UAT

Selain role dan admin bootstrap, seeder juga menyiapkan data UAT minimum:
- user contoh role `sales`, `manager`, `admin`
- master data utama (customer, contact, vendor, location, transport mode, vehicle type, cost category)
- contoh transaksi (`INQ-UAT-0001`, `Q-UAT-0001`, `Q-UAT-0002`) untuk flow pricing, approval queue, dan history

Kredensial default:
- `sales.uat@example.com` / `password`
- `manager.uat@example.com` / `password`
- `admin.uat@example.com` / `password`

Catatan:
- role default yang akan dibuat: `sales`, `manager`, `admin`
- seeder bootstrap + UAT aktif untuk environment `local`, `development`, dan `uat`
- proses seeding idempotent (aman dijalankan berulang tanpa duplikasi data kunci)

## Checklist UAT

Panduan persiapan dan eksekusi UAT tersedia di:
- `docs/uat-release-checklist.md`
