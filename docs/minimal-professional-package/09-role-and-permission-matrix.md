# Role and Permission Matrix v0.1

Dokumen ini menjelaskan siapa boleh melakukan apa. Ini penting karena sistem internal sering terlihat sederhana, tapi kalau hak akses tidak jelas, data pricing bisa kacau.

## 1. Tujuan Dokumen

Tujuan dokumen ini:
- menentukan batas akses tiap role
- menjadi acuan `authorization` (pengecekan izin aksi)
- mengurangi keputusan mendadak saat coding

## 2. Role Inti

Role awal yang dipakai:
- `sales`
- `manager`
- `admin`

Catatan:
- untuk `v0.1`, kita tidak perlu role terlalu banyak
- kalau nanti dibutuhkan, role bisa dipecah lagi

## 3. Prinsip Permission

`Permission` (izin melakukan aksi tertentu) dibangun dengan prinsip:
- role hanya dapat akses yang benar-benar dibutuhkan
- user tidak boleh melihat atau mengubah data sensitif tanpa alasan bisnis
- pengecekan izin harus ada di backend, bukan hanya di tampilan

## 4. Matrix Akses Ringkas

### Inquiry

- `sales`: create, view own, update own draft, submit
- `manager`: view all, review related quote context
- `admin`: view all, update if needed for support

### Scenario

- `sales`: create, update, compare, select on own inquiry
- `manager`: view on review flow
- `admin`: view and support edit if diperlukan

### Leg and Cost Item

- `sales`: create and update on own working inquiry
- `manager`: view only saat review
- `admin`: support view/update jika dibutuhkan untuk maintenance data

### Quote

- `sales`: create draft, update draft, submit for approval, view own
- `manager`: view all for approval, approve, reject
- `admin`: view all, support operational correction sesuai kebijakan

### Master Data

- `sales`: view only atau limited create tergantung kebijakan
- `manager`: view
- `admin`: full manage

### Audit Log

- `sales`: no direct access
- `manager`: limited view jika dibutuhkan
- `admin`: full or near-full view

## 5. Rekomendasi Permission Detail

### `sales`

Boleh:
- login
- lihat inquiry milik sendiri
- buat inquiry
- edit inquiry yang masih `draft`
- buat scenario
- edit leg dan cost item pada inquiry milik sendiri
- buat quote draft
- ajukan quote untuk approval
- lihat status approval quote milik sendiri
- cari histori pricing

Tidak boleh:
- approve quote
- mengelola seluruh master data sensitif secara bebas
- mengubah quote yang sudah di-approve tanpa alur revisi
- melihat audit log penuh

### `manager`

Boleh:
- login
- lihat inquiry dan quote yang relevan untuk review
- lihat detail pricing sebelum approval
- approve quote
- reject quote dengan alasan
- lihat histori pricing dan ringkasan penting

Tidak boleh:
- mengubah semua master data secara bebas jika bukan tugasnya
- mengedit detail pricing diam-diam pada workflow normal, kecuali nanti memang dibuat fitur khusus

### `admin`

Boleh:
- login
- kelola user jika kita buka modul itu di MVP
- kelola role dasar
- kelola master data
- lihat data transaksi untuk support
- bantu koreksi data sesuai kebijakan operasional
- lihat audit log

Tidak boleh:
- melompati aturan bisnis yang harus tetap dijaga sistem tanpa jejak

## 6. Ownership Rules

`Ownership` (kepemilikan data) dipakai agar sales tidak saling mengganggu kerja satu sama lain.

Aturan awal:
- sales hanya boleh edit inquiry yang dia buat atau yang di-assign ke dia
- manager tidak perlu jadi editor utama inquiry, kecuali ada proses eskalasi
- admin bisa membantu koreksi, tapi perubahan penting tetap sebaiknya terekam di audit log

## 7. Status-Based Permission

Izin user juga dipengaruhi status data.

Contoh aturan:
- inquiry `draft`: sales boleh edit
- inquiry `submitted`: edit dibatasi
- quote `draft`: sales boleh edit
- quote `waiting_approval`: sales tidak boleh mengubah angka inti tanpa mengembalikan ke draft
- quote `approved`: hanya bisa diteruskan ke pengiriman atau direvisi
- quote `sent`: tidak boleh diubah diam-diam

`Status-based permission` artinya bukan cuma role yang menentukan, tapi juga status data saat itu.

## 8. Permission Minimum untuk MVP

Permission minimum yang wajib kita implementasikan dulu:
- login required untuk semua area internal
- `sales` hanya bisa edit data kerja miliknya
- `manager` bisa approve/reject quote
- `admin` bisa kelola master data
- hanya user tertentu yang bisa melihat `/admin`

## 9. Implementasi Teknis yang Disarankan

Cara implementasi di Laravel nanti:
- role disimpan di tabel `roles`
- gunakan `policy` (kelas izin per model) untuk aksi penting
- gunakan `middleware` (penjaga route sebelum halaman dibuka) untuk area admin
- gunakan pengecekan role di navigation agar menu tidak tampil sembarangan

`Policy` cocok untuk aturan seperti:
- siapa boleh edit inquiry
- siapa boleh approve quote
- siapa boleh melihat audit log

## 10. Keputusan yang Boleh Ditunda

Belum wajib diputuskan di `v0.1`:
- granular permission per tombol
- approver lebih dari 1 level
- akses berbasis cabang, wilayah, atau business unit
- delegated approval

## 11. Output dari Dokumen Ini

Kalau dokumen ini sudah disetujui, nanti bisa dipecah menjadi:
- role seeder
- user policy
- route middleware
- conditional menu di `Filament`
