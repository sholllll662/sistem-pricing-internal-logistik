# Domain and Business Rules v0.1

`Domain model` (peta objek bisnis dan hubungannya) dipakai supaya kita tidak langsung lompat ke tabel database tanpa arah. Dokumen ini menjelaskan bahasa bisnis yang harus kita samakan sebelum coding.

## 1. Prinsip Model

Prinsip dasar yang kita pegang:
- 1 inquiry tidak dianggap 1 pengiriman sederhana, tapi 1 kebutuhan customer yang bisa punya beberapa opsi
- 1 inquiry bisa punya banyak `scenario` (opsi skema pengiriman)
- 1 scenario bisa punya banyak `leg` (tahap pengiriman)
- 1 leg bisa punya banyak `cost item` (komponen biaya)
- hasil pricing harus disimpan sebagai `snapshot` (salinan nilai saat itu), bukan hanya dihitung ulang terus

Alasan:
- flow logistik proyek bisa kompleks
- jumlah step pengiriman bisa berubah
- rumus dan struktur biaya bisa berkembang
- histori lama harus tetap valid walaupun aturan baru muncul

## 2. Glossary Bisnis

`Glossary` (kamus istilah bisnis yang dipakai konsisten):
- `Inquiry`: permintaan harga atau permintaan penawaran dari customer
- `Scenario`: salah satu opsi rencana pengiriman untuk menjawab 1 inquiry
- `Leg`: satu tahap perjalanan dalam scenario, misalnya pickup ke pelabuhan, pelabuhan ke pelabuhan tujuan, atau titik akhir ke lokasi proyek
- `Cost Item`: satu komponen biaya di dalam leg, misalnya trucking, handling, stuffing, admin, atau surcharge
- `Base Cost / Modal`: total biaya dasar yang keluar ke vendor / agen
- `Margin`: selisih keuntungan yang ingin ditambahkan ke modal
- `Selling Price / Harga Jual`: harga akhir yang akan diajukan ke customer
- `Route`: jalur asal ke tujuan, bisa level umum atau per leg
- `Vendor / Agent`: pihak yang memberikan harga modal
- `Contact Point`: orang yang bisa dihubungi di titik pickup atau drop
- `Quote`: penawaran resmi yang diajukan ke customer
- `Approval`: proses persetujuan internal sebelum quote dianggap final

## 3. Kelompok Entitas

`Entity` (objek/data utama dalam sistem) kita bagi menjadi 3 kelompok supaya lebih gampang dipahami.

### 3.1 Master Data

`Master data` (data referensi yang dipakai berulang):
- `Customer`
- `Customer Contact`
- `Vendor / Agent`
- `Vendor Contact` opsional
- `Location`
- `Transport Mode`
- `Vehicle Type`
- `Cost Category`
- `Service Type` jika nanti dibutuhkan

### 3.2 Data Transaksi

`Transaction data` (data yang tercipta dari aktivitas harian):
- `Inquiry`
- `Inquiry Scenario`
- `Scenario Leg`
- `Leg Cost Item`
- `Quote`
- `Quote Approval`

### 3.3 Data Pendukung

`Support data` (data tambahan untuk pelacakan dan kenyamanan kerja):
- `Attachment`
- `Note`
- `Audit Log`
- `Price Reference` atau histori harga

## 4. Entitas Inti dan Tanggung Jawabnya

### 4.1 Inquiry

Inquiry menyimpan konteks permintaan dari customer.

Data minimum yang perlu dipikirkan:
- nomor inquiry
- customer
- sales owner
- tanggal inquiry
- deskripsi barang / muatan
- titik asal utama
- titik tujuan utama
- contact pickup
- contact drop
- catatan umum
- status inquiry

Aturan:
- 1 inquiry wajib punya 1 customer
- 1 inquiry wajib punya minimal 2 contact point, yaitu pickup dan drop
- 1 inquiry boleh punya lebih dari 1 scenario

### 4.2 Scenario

Scenario mewakili opsi pengiriman untuk 1 inquiry.

Contoh:
- skema via laut
- skema via trucking penuh
- skema campuran trucking + kapal

Data minimum:
- inquiry induk
- nama / kode scenario
- deskripsi pendek
- status scenario
- total modal snapshot
- total margin snapshot
- total harga jual snapshot

Aturan:
- 1 scenario minimal punya 1 leg
- 1 inquiry bisa punya banyak scenario untuk dibandingkan
- 1 scenario bisa dipilih sebagai opsi utama

### 4.3 Leg

Leg adalah tahap pengiriman yang membentuk scenario.

Data minimum:
- scenario induk
- urutan leg
- tipe leg
- origin
- destination
- transport mode
- vehicle type jika relevan
- vendor / agent
- catatan operasional

`Leg type` (jenis tahap) boleh memakai nilai seperti:
- `first_mile`
- `middle_mile`
- `last_mile`
- `custom`

Aturan:
- `first mile`, `middle mile`, dan `last mile` jangan dijadikan kolom tetap
- simpan sebagai data leg supaya jumlah tahap fleksibel
- 1 leg wajib punya origin dan destination
- 1 leg boleh punya 1 vendor utama, tapi struktur ke depan harus tetap memungkinkan lebih dari 1 sumber harga jika dibutuhkan

### 4.4 Cost Item

Cost item menyimpan rincian biaya di dalam leg.

Contoh:
- biaya trucking
- biaya handling
- biaya bongkar muat
- biaya ferry
- biaya admin
- biaya surcharge

Data minimum:
- leg induk
- nama biaya
- kategori biaya
- nominal
- vendor terkait jika berbeda dari vendor utama leg
- keterangan sumber harga

Aturan:
- total modal leg dihitung dari penjumlahan cost item
- cost item wajib punya nominal
- cost item tidak boleh bernilai negatif kecuali nanti ada kebutuhan diskon / koreksi yang disepakati

### 4.5 Quote

Quote adalah hasil penawaran yang dikirim ke customer.

Data minimum:
- inquiry induk
- scenario yang dipilih
- nomor quote
- masa berlaku
- total modal snapshot
- total margin snapshot
- total harga jual snapshot
- status quote

Aturan:
- quote harus mengacu ke scenario tertentu
- quote wajib punya masa berlaku
- masa berlaku awal berada di rentang 3 sampai 6 bulan
- quote tidak boleh dianggap final sebelum approval selesai

## 5. Hubungan Data

`Relationship` (hubungan antar data):
- 1 `Customer` bisa punya banyak `Inquiry`
- 1 `Inquiry` bisa punya banyak `Scenario`
- 1 `Scenario` bisa punya banyak `Leg`
- 1 `Leg` bisa punya banyak `Cost Item`
- 1 `Scenario` bisa menghasilkan 1 atau lebih `Quote`
- 1 `Vendor / Agent` bisa terkait ke banyak `Leg` dan `Cost Item`
- 1 `Customer` bisa punya banyak `Contact Point`

## 6. Status Lifecycle

`Status lifecycle` (alur status data dari awal sampai akhir):

Status inquiry yang disarankan:
- `draft`
- `submitted`
- `pricing_in_progress`
- `waiting_approval`
- `quoted`
- `closed`
- `canceled`

Status scenario yang disarankan:
- `draft`
- `calculated`
- `selected`
- `archived`

Status quote yang disarankan:
- `draft`
- `waiting_approval`
- `approved`
- `sent`
- `accepted`
- `rejected`
- `expired`

## 7. Aturan Pricing

`Business rules` (aturan bisnis yang mengendalikan logika sistem):

Aturan inti:
- histori harga boleh dipakai sebagai referensi, tapi user tetap bisa input manual
- harga modal harus bisa dilacak per vendor, per rute, dan per leg
- total modal scenario dihitung dari total semua leg
- harga jual dihitung dari total modal + margin + surcharge jika ada
- margin bisa punya nilai default, tapi user tertentu boleh override
- semua angka final yang dipakai untuk quote harus disimpan sebagai snapshot

Aturan referensi harga:
- sistem harus bisa menampilkan histori harga serupa
- histori sebaiknya bisa dicari berdasarkan origin, destination, vendor, vehicle type, dan jenis layanan
- histori adalah alat bantu keputusan, bukan pengganti keputusan sales

Aturan approval:
- quote perlu approval manager
- setelah quote masuk approval, perubahan harga sebaiknya dibatasi atau harus membuat revisi

`Revision` (versi baru dari quote atau scenario) bisa dipakai nanti jika kita butuh histori perubahan yang lebih formal.

## 8. Validasi Dasar

`Validation` (aturan input agar data tidak rusak):
- inquiry wajib punya customer
- inquiry wajib punya contact pickup dan contact drop
- scenario wajib punya minimal 1 leg
- leg wajib punya origin dan destination
- leg wajib punya urutan yang jelas
- cost item wajib punya nama biaya dan nominal
- quote tidak boleh disimpan tanpa total modal dan harga jual
- quote tidak boleh diajukan ke approval jika scenario belum lengkap

## 9. Audit dan Jejak Data

`Audit log` (catatan siapa mengubah apa dan kapan):
- simpan pembuat data
- simpan pengubah terakhir
- simpan waktu pembuatan dan perubahan
- simpan perubahan angka penting seperti total modal, margin, dan harga jual
- simpan siapa yang approve dan kapan approval terjadi

Data yang paling penting diaudit:
- perubahan harga modal
- perubahan margin
- perubahan masa berlaku quote
- pergantian vendor sumber harga

## 10. Keputusan Desain Sementara

Keputusan sementara yang aman untuk MVP:
- route boleh dibentuk dari kombinasi `origin` dan `destination`, belum wajib punya master route yang rumit
- vendor boleh punya lebih dari 1 contact, tapi MVP cukup mulai dari contact utama dulu
- `Cost Category` sebaiknya ada, tapi daftar kategorinya jangan terlalu banyak di awal
- quote PDF boleh ditunda, yang penting data quote tersimpan rapi dulu

## 11. Open Design Decision

Keputusan yang belum harus final sekarang:
- apakah approval cukup 1 level atau nanti bertingkat
- apakah satu leg boleh punya beberapa vendor pembanding dalam 1 layar
- apakah sistem perlu menyimpan lampiran bukti harga vendor di MVP
- apakah margin default akan dibedakan per customer, per layanan, atau global
