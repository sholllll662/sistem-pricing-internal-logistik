# Implementation Roadmap v0.1

Dokumen ini menjawab pertanyaan: mulai ngoding dari mana, urutannya bagaimana, dan bagian mana yang lebih cocok dibuat dengan `Filament` atau `Livewire`.

## 1. Keputusan Teknis yang Sudah Terkunci

Stack yang sudah dipilih:
- `Laravel` (framework backend PHP untuk routing, logic, model, auth, dan database)
- `Livewire` (cara membuat halaman interaktif tanpa harus membangun frontend SPA penuh)
- `Filament` (tool admin panel dan CRUD yang mempercepat pembuatan halaman data)
- `PostgreSQL` (database relasional yang kuat untuk data bisnis dan fleksibel untuk kebutuhan berkembang)

## 2. Prinsip Implementasi

Prinsip kerja yang kita pakai:
- bangun dari `vertical slice` (1 alur utuh dari UI sampai database), bukan backend semua dulu
- pakai `Filament` untuk area admin dan data yang bentuknya CRUD standar
- pakai `Livewire custom page` untuk layar kerja utama yang interaktif, terutama `scenario builder`
- mulai dari flow yang paling sering dipakai user, bukan dari fitur tambahan
- tunda kompleksitas yang belum wajib di MVP

## 3. Pembagian Tanggung Jawab Filament vs Livewire

Bagian yang cocok dibuat dengan `Filament resource` (halaman CRUD standar):
- user management
- customer management
- customer contact
- vendor / agent management
- location management
- vehicle type
- transport mode
- cost category
- approval queue sederhana
- histori data dengan filter dasar

Bagian yang lebih cocok dibuat dengan `Livewire custom page`:
- form inquiry yang lebih dinamis
- `scenario builder` untuk membuat banyak leg
- entry `cost item` per leg
- kalkulasi total modal, margin, dan harga jual secara interaktif
- perbandingan beberapa scenario dalam 1 inquiry

`Custom page` (halaman yang kita rancang sendiri, bukan hasil generator CRUD biasa) dipakai saat kebutuhan interaksinya lebih rumit dari form standar.

## 4. Fase Implementasi

### Fase 0: Persiapan

Tujuan:
- memastikan fondasi dokumen dan keputusan bisnis sudah cukup

Checklist:
- PRD ringan sudah ada
- domain model sudah ada
- user flow sudah ada
- scope MVP sudah jelas

Output fase ini:
- kita siap mulai scaffolding tanpa nebak-nebak terlalu banyak

### Fase 1: Project Scaffolding

`Scaffolding` (membangun kerangka awal project) adalah tahap pertama ngoding yang sebenarnya.

Tujuan:
- membuat project Laravel siap dikembangkan

Checklist:
- create project Laravel
- konfigurasi `.env`
- koneksi PostgreSQL
- setup authentication
- install dan setup Filament
- install dan setup Livewire
- buat struktur folder yang rapi
- setup base layout dan navigation awal

Output fase ini:
- project bisa jalan di local
- user bisa login
- panel admin dasar sudah terbuka

### Fase 2: Master Data Dasar

Tujuan:
- menyediakan data referensi minimum agar flow inquiry bisa dipakai

Modul prioritas:
- customer
- customer contact
- vendor / agent
- location
- vehicle type
- transport mode
- cost category

Cara implementasi:
- mayoritas dibuat pakai `Filament resource`

Output fase ini:
- admin bisa mengelola data referensi tanpa edit database manual

### Fase 3: Vertical Slice Pertama

Ini fase paling penting.

Tujuan:
- membuktikan bahwa arsitektur kita benar lewat 1 alur kerja utuh

Alur yang dibangun:
1. sales login
2. sales buat inquiry
3. sales buat 1 scenario
4. sales tambah 1 atau lebih leg
5. sales tambah cost item
6. sistem hitung total modal
7. sales isi margin
8. sistem hitung harga jual
9. sales simpan quote draft
10. data tampil di histori

Cara implementasi:
- inquiry dasar bisa gabungan `Filament` dan `Livewire`
- scenario builder dibuat dengan `Livewire custom page`

Output fase ini:
- ada 1 flow utama yang benar-benar bisa dipakai end-to-end

### Fase 4: Approval dan Histori

Tujuan:
- membuat proses bisnis lebih mendekati kondisi operasional nyata

Modul prioritas:
- approval queue untuk manager
- detail approval page
- histori pricing / quote
- filter pencarian dasar

Cara implementasi:
- approval queue bisa dimulai dengan `Filament resource`
- halaman review quote bisa `Filament page` atau `Livewire page`, tergantung kompleksitas tampilannya

Output fase ini:
- quote bisa diajukan, direview, dan dilacak

### Fase 5: Hardening MVP

`Hardening` (merapikan dan menguatkan sistem sebelum dipakai lebih serius):

Fokus:
- validasi input
- otorisasi role
- audit log dasar
- error handling
- test dasar
- performance query utama

Output fase ini:
- sistem lebih aman, lebih stabil, dan lebih siap dipakai user nyata

## 5. Urutan Modul Coding

Urutan modul yang paling aman:
1. auth dan role dasar
2. customer dan customer contact
3. vendor / agent
4. location
5. inquiry
6. scenario
7. leg
8. cost item
9. pricing summary
10. quote
11. approval
12. histori dan pencarian

Kenapa urutan ini aman:
- kita bangun dari referensi data dulu
- lalu masuk ke transaksi utama
- lalu masuk ke approval dan histori

## 6. Rencana Model dan Migration

`Migration` (file untuk membuat atau mengubah struktur tabel database) sebaiknya dibuat bertahap, bukan sekaligus semuanya.

Urutan migration yang disarankan:
1. users dan roles
2. customers
3. customer_contacts
4. vendors
5. locations
6. transport_modes
7. vehicle_types
8. cost_categories
9. inquiries
10. inquiry_scenarios
11. scenario_legs
12. leg_cost_items
13. quotes
14. quote_approvals
15. audit_logs jika diperlukan di fase awal

## 7. Definition of Done

`Definition of Done` (syarat fitur dianggap benar-benar selesai):
- flow utamanya jalan
- validasi input ada
- data tersimpan dengan benar di database
- relasi data bisa dipakai tanpa query aneh
- UI bisa dipakai user target
- error dasar tertangani
- ada test atau minimal checklist uji manual

## 8. Testing Minimal

`Unit test` (tes untuk 1 aturan logika kecil):
- hitung total cost item
- hitung total modal scenario
- hitung margin
- hitung harga jual
- cek masa berlaku quote

`Feature test` (tes untuk alur fitur dari request sampai response):
- user login
- user buat inquiry
- user simpan scenario
- user simpan quote draft
- manager approve quote

## 9. Struktur Kerja Harian

Cara kerja yang sehat per fitur:
1. cek apakah flow atau aturan bisnis berubah
2. update dokumen jika ada perubahan penting
3. buat migration
4. buat model dan relasi
5. buat resource atau page
6. hubungkan logic hitung
7. uji manual
8. rapikan sebelum lanjut

## 10. Warning yang Perlu Diingat

Hal yang sebaiknya tidak dilakukan terlalu cepat:
- bikin semua tabel sekaligus
- bikin semua API sekaligus
- bikin dashboard cantik sebelum flow utama stabil
- bikin mobile app native sebelum web flow terbukti dipakai
- menaruh terlalu banyak logic pricing langsung di controller atau page

Catatan penting:
- logic hitung nanti lebih sehat kalau dipisah ke `service` (kelas khusus untuk logika bisnis) agar tidak bercampur dengan tampilan

## 11. Kapan Kita Mulai Ngoding

Jawaban paling sederhananya:
- kita mulai ngoding di `Fase 1: Project Scaffolding`
- lalu lanjut serius di `Fase 3: Vertical Slice Pertama`

Artinya:
- sebelum itu kita sedang menyiapkan arah
- setelah itu kita benar-benar membangun produk
