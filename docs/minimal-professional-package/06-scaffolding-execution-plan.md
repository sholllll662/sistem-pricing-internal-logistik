# Scaffolding Execution Plan v0.1

Dokumen ini adalah jembatan dari fase diskusi ke fase terminal. Isinya bukan desain bisnis lagi, tapi langkah teknis awal saat kita benar-benar mulai membuat project `Laravel + Livewire + Filament`.

## 1. Tujuan Fase Scaffolding

`Scaffolding` (membangun kerangka awal project) bertujuan untuk:
- membuat project bisa jalan di local
- menyiapkan auth dasar
- menyiapkan panel admin dasar
- menyiapkan struktur folder dan kebiasaan kerja yang rapi
- mengurangi keputusan teknis yang mendadak saat sudah mulai coding fitur

Target akhir fase ini:
- aplikasi bisa dibuka di browser
- user bisa login
- panel `Filament` bisa dibuka
- project siap masuk ke modul pertama

## 2. Rekomendasi Jalur Setup

Jalur yang paling aman untuk proyek ini:
1. buat project Laravel dengan `Livewire starter kit`
2. jalankan dependency frontend
3. hidupkan project secara lokal
4. install `Filament` panel
5. rapikan konfigurasi dasar
6. baru mulai migration dan modul data

Kenapa jalur ini paling aman:
- auth dasar sudah tersedia dari starter kit
- layout dan komponen Livewire dasar sudah tersedia
- kita tidak perlu merakit auth dari nol
- `Filament` bisa ditambahkan setelah fondasi aplikasi utama ada

## 3. Prasyarat Sebelum Buka Terminal

`Prerequisite` (syarat awal sebelum setup) yang harus ada:
- `PHP 8.2+`
- `Composer`
- `Node.js` dan `npm`
- `PostgreSQL`
- `Git`

Catatan:
- `Filament 5` menurut docs resminya membutuhkan `PHP 8.2+` dan `Laravel 11.28+`
- `Livewire` adalah package Laravel, jadi instalasinya mengikuti project Laravel

## 4. Keputusan Sebelum Create Project

Sebelum jalankan command pertama, kita perlu kunci beberapa keputusan kecil:

- nama project folder
- nama database lokal
- apakah kita pakai auth bawaan starter kit
- apakah panel admin `Filament` langsung diletakkan di `/admin`
- apakah role awal cukup `sales`, `admin`, dan `manager`

Rekomendasi awal:
- nama project: `logistics-pricing-system`
- panel admin: `/admin`
- role awal: `sales`, `admin`, `manager`
- database lokal: pisahkan khusus project ini

## 5. Urutan Command yang Direkomendasikan

### 5.1 Create Project

Menurut docs resmi Laravel, cara paling aman adalah membuat app baru lewat `laravel new` lalu memilih starter kit saat prompt muncul.

Command:

```powershell
laravel new logistics-pricing-system
```

Saat prompt berjalan, pilih:
- starter kit: `Livewire`
- authentication: bawaan starter kit
- database: `PostgreSQL`

`Starter kit` (template awal resmi Laravel) akan memberi kita auth, layout, dan struktur frontend awal.

### 5.2 Masuk ke Folder Project

```powershell
cd logistics-pricing-system
```

### 5.3 Install Frontend Dependencies

Menurut docs Laravel starter kit, setelah project dibuat kita perlu install dependency frontend lalu build.

```powershell
npm install
npm run build
```

`Dependency frontend` (paket yang dibutuhkan tampilan) tetap ada walaupun kita tidak membuat SPA React.

### 5.4 Jalankan Project Lokal

```powershell
composer run dev
```

`composer run dev` akan menjalankan development workflow lokal yang disediakan Laravel.

### 5.5 Install Filament

Menurut docs resmi `Filament 5`, untuk PowerShell di Windows lebih aman gunakan constraint `~5.0` daripada `^5.0`.

```powershell
composer require filament/filament:"~5.0"
php artisan filament:install --panels
```

Output penting dari tahap ini:
- panel provider `Filament` dibuat
- route admin dasar tersedia
- panel admin siap dikembangkan

### 5.6 Buat User Awal Filament

Jika dibutuhkan user admin awal:

```powershell
php artisan make:filament-user
```

## 6. Jika Tidak Pakai Starter Kit

Ini bukan jalur utama yang gue rekomendasikan, tapi berguna sebagai catatan.

Kalau suatu saat kita mulai dari Laravel kosong, docs resmi Livewire menyebut instalasinya cukup:

```powershell
composer require livewire/livewire
```

Tapi untuk proyek ini, lebih aman tetap mulai dari `Livewire starter kit`, karena auth dan layout dasarnya sudah jadi.

## 7. Konfigurasi Dasar Setelah Install

Setelah semua package terpasang, hal pertama yang perlu dirapikan:

- isi `.env`
- set koneksi `PostgreSQL`
- set `APP_NAME`
- set `APP_URL`
- cek `timezone`
- cek mail config jika nanti butuh reset password

Minimal `.env` yang harus benar:
- `DB_CONNECTION=pgsql`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

## 8. Struktur Area Aplikasi

Sesudah scaffold selesai, area aplikasi kita akan terbagi 2:

- area `app web utama`
- area `Filament admin panel`

Pembagian tanggung jawabnya:
- `Filament`: master data, admin CRUD, approval queue sederhana, histori tabel
- `Livewire custom page`: inquiry form, scenario builder, leg builder, pricing workspace

Ini penting supaya dari awal kita tidak memaksa semua layar masuk ke `Filament`.

## 9. Langkah Teknis Setelah Scaffolding

Begitu scaffold selesai, urutan langkah teknis yang paling aman:
1. setup role dasar
2. buat migration master data inti
3. buat `Filament resources` untuk master data
4. buat migration `inquiries`
5. buat migration `inquiry_scenarios`
6. buat migration `scenario_legs`
7. buat migration `leg_cost_items`
8. buat halaman `Livewire` untuk flow inquiry

`Migration` (file pembentuk struktur tabel database) sebaiknya dibuat sedikit demi sedikit, jangan semua sekaligus.

## 10. Checklist Scaffolding Selesai

Fase scaffolding dianggap selesai kalau:
- project bisa dijalankan di local
- login bawaan berjalan
- database terkoneksi
- migration dasar jalan
- panel `/admin` bisa dibuka
- ada minimal 1 user admin
- struktur project siap dipakai untuk modul pertama

## 11. Jebakan yang Sebaiknya Dihindari

Hal yang sering bikin capek di awal:
- langsung buat semua tabel sekaligus
- langsung install terlalu banyak package tambahan
- mencampur logic pricing ke controller atau view
- memaksa semua layar dibuat dengan `Filament`
- mengubah desain arsitektur sebelum vertical slice pertama jadi

## 12. Keputusan Teknis yang Boleh Ditunda

Hal yang belum perlu diputuskan di hari pertama:
- queue worker
- Redis
- scheduler rumit
- search engine khusus
- file storage production
- observability yang terlalu kompleks

Kita cukup fokus dulu ke fondasi yang membuat MVP bisa jalan.

## 13. Next Step Setelah Dokumen Ini

Setelah dokumen ini, langkah paling logis adalah membuat:
- checklist command eksekusi lokal
- keputusan struktur folder dan namespace
- daftar migration pertama

Kalau semua itu sudah siap, kita baru mulai scaffold project beneran dengan lebih tenang.
