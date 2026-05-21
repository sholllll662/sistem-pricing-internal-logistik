# User Flow and Screens v0.1

`User flow` (urutan langkah user saat memakai sistem) dipakai supaya kita tidak coding secara buta. Dokumen ini menjawab 3 hal: user masuk dari mana, klik apa dulu, dan halaman apa saja yang benar-benar perlu ada di MVP.

## 1. Tujuan Flow MVP

Flow MVP harus memungkinkan user melakukan pekerjaan utama tanpa pindah-pindah alat.

Tujuan flow versi awal:
- sales bisa mencatat inquiry baru
- sales bisa menyusun scenario pengiriman
- sales bisa menghitung modal dan harga jual
- sales bisa mengajukan quote untuk approval
- manager bisa approve atau reject quote
- user bisa mencari histori pricing sebagai referensi

## 2. Role dan Flow Utama

`Role` (jenis pengguna dengan hak akses berbeda) yang perlu kita pikirkan dari sisi layar:
- `Sales`
- `Manager`
- `Admin`

## 3. Flow Utama Sales

Flow inti sales yang perlu selesai dulu:
1. sales login
2. sales melihat daftar inquiry yang pernah dibuat atau sedang berjalan
3. sales membuat inquiry baru
4. sales mengisi data customer, muatan, origin, destination, dan contact pickup / drop
5. sales menyimpan inquiry sebagai `draft` (versi kerja awal)
6. sales membuat 1 atau lebih `scenario`
7. sales menambahkan beberapa `leg` ke tiap scenario
8. sales menambahkan `cost item` per leg
9. sistem menghitung total modal per leg dan per scenario
10. sales menentukan margin atau menyesuaikan margin default
11. sistem menghitung harga jual
12. sales membandingkan scenario jika ada lebih dari 1 opsi
13. sales memilih scenario yang akan dijadikan quote
14. sales mengajukan quote untuk approval manager
15. jika disetujui, sales mengirim quote ke customer
16. sales bisa mencari histori inquiry atau pricing saat inquiry serupa datang lagi

## 4. Flow Manager

Flow manager lebih sederhana:
1. manager login
2. manager melihat daftar quote yang menunggu approval
3. manager membuka detail inquiry dan scenario yang dipilih
4. manager melihat ringkasan modal, margin, harga jual, vendor, dan histori referensi
5. manager memilih `approve` atau `reject`
6. jika reject, manager memberi alasan

## 5. Flow Admin

Flow admin di MVP:
1. admin login
2. admin mengelola master data dasar
3. admin menambah atau memperbarui customer
4. admin menambah atau memperbarui vendor / agent
5. admin menambah atau memperbarui location, vehicle type, dan transport mode

## 6. Daftar Layar Minimum

`Screen` (halaman utama yang perlu ada) untuk MVP:
- halaman login
- dashboard ringkas
- halaman daftar inquiry
- halaman detail / form inquiry
- halaman scenario builder
- halaman approval queue
- halaman detail quote
- halaman histori pricing / quote
- halaman master data dasar

## 7. Detail Fungsi Tiap Layar

### 7.1 Login

Fungsi:
- user masuk ke sistem sesuai role

Yang perlu tampil:
- email / username
- password
- tombol login
- pesan error jika login gagal

### 7.2 Dashboard

Fungsi:
- memberi gambaran cepat tentang pekerjaan user hari ini

Yang perlu tampil untuk sales:
- jumlah inquiry aktif
- jumlah quote draft
- jumlah quote menunggu approval
- shortcut buat inquiry baru

Yang perlu tampil untuk manager:
- jumlah quote menunggu approval
- shortcut ke daftar approval

Catatan:
- dashboard MVP tidak perlu analitik rumit

### 7.3 Daftar Inquiry

Fungsi:
- melihat semua inquiry yang relevan untuk user
- menjadi pintu masuk ke proses kerja utama

Yang perlu tampil:
- nomor inquiry
- customer
- origin dan destination ringkas
- status inquiry
- tanggal dibuat
- sales owner
- aksi `lihat`, `edit`, atau `lanjutkan`

Filter minimum:
- status
- customer
- tanggal

### 7.4 Form Inquiry

Fungsi:
- mencatat konteks dasar inquiry

Yang perlu tampil:
- customer
- nama inquiry atau deskripsi singkat
- jenis barang / muatan
- origin utama
- destination utama
- contact pickup
- contact drop
- catatan umum
- tombol `save draft`
- tombol `lanjut ke scenario`

Catatan:
- ini bukan layar hitung harga penuh
- tugasnya hanya mengunci data dasar inquiry

### 7.5 Scenario Builder

Ini layar paling penting di MVP.

Fungsi:
- menyusun skema pengiriman
- menambahkan leg
- menambahkan cost item
- menghitung pricing

Yang perlu tampil:
- ringkasan inquiry di bagian atas
- daftar scenario di sisi kiri atau tab atas
- detail scenario aktif di area utama
- daftar leg berurutan
- tombol tambah leg
- form / tabel cost item di setiap leg
- ringkasan total modal per leg
- ringkasan total modal per scenario
- input margin
- hasil harga jual
- tombol `save scenario`
- tombol `ajukan approval`

Prinsip UX:
- sales harus bisa paham urutan logika dari atas ke bawah
- jangan pecah terlalu banyak halaman kecil
- kalau bisa, inquiry dan scenario terasa sebagai 1 workspace kerja

### 7.6 Approval Queue

Fungsi:
- tempat manager melihat daftar quote yang menunggu approval

Yang perlu tampil:
- nomor quote
- inquiry
- customer
- sales owner
- total modal
- total harga jual
- tanggal pengajuan
- aksi `review`

### 7.7 Detail Quote / Approval Screen

Fungsi:
- melihat detail quote sebelum approve atau reject

Yang perlu tampil:
- ringkasan inquiry
- scenario terpilih
- daftar leg
- total modal
- margin
- harga jual
- masa berlaku quote
- status approval
- tombol `approve`
- tombol `reject`
- field alasan reject

### 7.8 Histori Pricing / Quote

Fungsi:
- membantu sales mencari referensi lama

Yang perlu tampil:
- daftar histori inquiry atau quote
- customer
- origin
- destination
- vendor
- vehicle type
- harga modal
- harga jual
- tanggal

Filter minimum:
- customer
- route
- vendor
- vehicle type
- rentang tanggal

### 7.9 Master Data

Fungsi:
- mengelola data referensi

Layar minimum:
- customer
- customer contact
- vendor / agent
- location
- vehicle type
- transport mode

## 8. Wireframe Fokus

`Wireframe` (sketsa struktur layar, belum desain final) harus fokus ke struktur kerja, bukan kecantikan dulu.

Yang wajib terlihat di wireframe:
- field apa yang wajib diisi
- urutan aksi utama
- hubungan inquiry ke scenario
- hubungan scenario ke leg
- hubungan leg ke cost item
- area ringkasan total
- status inquiry / quote
- tombol aksi utama dan aksi sekunder

## 9. Empty, Error, Loading State

`Empty state` (tampilan saat belum ada data):
- belum ada inquiry
- inquiry belum punya scenario
- scenario belum punya leg
- leg belum punya cost item
- belum ada histori pricing yang cocok

`Error state` (tampilan saat terjadi masalah):
- field wajib belum diisi
- origin atau destination belum dipilih
- contact pickup / drop belum lengkap
- total biaya gagal dihitung
- data vendor tidak ditemukan
- quote belum bisa diajukan karena scenario belum lengkap

`Loading state` (tampilan saat sistem sedang memproses):
- simpan draft inquiry
- simpan scenario
- hitung ulang total
- cari histori pricing
- kirim approval

## 10. Vertical Slice Pertama

`Vertical slice` (1 potongan fitur utuh dari UI sampai database) yang paling sehat untuk dibangun lebih dulu:
- login
- daftar inquiry
- buat inquiry
- tambah 1 scenario
- tambah 1 leg
- tambah beberapa cost item
- hitung total modal
- isi margin
- simpan quote draft
- tampilkan di histori

Kenapa ini dulu:
- sudah menyentuh alur inti bisnis
- sudah melibatkan database, backend, dan UI
- cukup kecil untuk dibangun bertahap
- cukup besar untuk membuktikan arsitektur kita benar

## 11. Batasan Versi Awal

Hal yang belum perlu dipaksakan di layar awal:
- dashboard analytics kompleks
- grafik performa sales
- approval bertingkat
- export PDF mewah
- notifikasi real-time
- filter super detail

## 12. Catatan UX Penting

Hal yang perlu kita jaga saat nanti mulai bikin UI:
- sales harus bisa bekerja cepat dari HP, jadi form jangan terlalu berantakan
- layar scenario builder harus terasa seperti workspace, bukan form panjang yang membingungkan
- status harus selalu jelas supaya user tahu sedang ada di tahap mana
- aksi utama seperti `save draft`, `save scenario`, dan `ajukan approval` harus selalu mudah ditemukan
