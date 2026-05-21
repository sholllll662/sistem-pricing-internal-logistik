# PRD Lite v0.1

`PRD` (Product Requirement Document: dokumen ringkas yang menjelaskan apa yang dibangun, untuk siapa, dan kenapa) ini adalah pegangan versi awal proyek. Dokumen ini belum final dan boleh berubah saat pemahaman bisnis makin jelas.

## 1. Ringkasan Produk

Nama sistem:
- Sistem Pricing Internal Logistik

Bentuk produk:
- aplikasi internal berbasis web yang mobile-friendly
- fokus awal sebagai `internal tool` (alat kerja internal), bukan aplikasi publik

Tujuan utama:
- membantu tim sales mencatat inquiry masuk
- membantu tim sales menghitung harga modal dan harga jual lebih cepat
- menyimpan histori pricing agar inquiry serupa bisa dipakai kembali sebagai referensi
- mengurangi ketergantungan pada tanya manual ke agen trucking untuk kasus yang pernah terjadi

Masalah yang ingin diselesaikan:
- proses pencarian harga modal masih manual melalui chat, telepon, atau ingatan pribadi
- inquiry kompleks sering dihitung ulang dari nol
- data harga modal tidak terkumpul sebagai aset perusahaan
- sales sulit menemukan referensi harga lama saat menghadapi inquiry serupa

Contoh kasus bisnis:
- customer meminta pengiriman barang proyek seperti tiang listrik dari Jakarta ke Jayapura
- pengiriman bisa terdiri dari beberapa tahap seperti `first mile`, `middle mile`, dan `last mile`
- 1 inquiry bisa memiliki lebih dari 1 `scenario` (opsi skema pengiriman) dan masing-masing scenario bisa punya struktur biaya berbeda

## 2. Latar Belakang Proses Saat Ini

Proses saat ini:
1. inquiry masuk ke sales
2. sales bertanya ke agen trucking atau vendor untuk mendapatkan harga modal
3. sales menghitung harga jual secara manual
4. hasil perhitungan sering tersimpan di chat, spreadsheet pribadi, atau tidak terdokumentasi rapi
5. saat inquiry serupa datang lagi, proses pencarian harga diulang dari awal

Masalah proses saat ini:
- lambat
- tidak konsisten
- sulit diaudit
- knowledge pricing tidak terkumpul sebagai database perusahaan

## 3. User dan Role

`Role` (jenis pengguna dengan hak akses berbeda):
- `Sales`: membuat inquiry, menyusun scenario, menghitung pricing, mengajukan quote
- `Admin`: mengelola master data seperti customer, vendor, lokasi, dan jenis armada
- `Manager`: melakukan approval quote dan melihat ringkasan data penting

User utama MVP:
- tim sales internal perusahaan

## 4. Scope MVP

`MVP` (Minimum Viable Product: versi paling kecil yang sudah berguna dan bisa dipakai kerja):
- login user
- input inquiry baru
- menyimpan data customer dan detail kebutuhan inquiry
- menyimpan minimal 2 `contact point` (orang yang bisa dihubungi) untuk pickup dan drop
- membuat lebih dari 1 `scenario` dalam 1 inquiry
- membuat beberapa `leg` (tahap pengiriman) dalam 1 scenario
- input `cost item` (komponen biaya) per leg
- mencatat vendor / agen yang memberi harga modal
- menghitung total modal, margin, dan harga jual
- menyimpan quote dengan masa berlaku tertentu
- approval quote oleh manager
- menyimpan histori inquiry, pricing, dan quote
- mencari histori berdasarkan customer, vendor, rute, leg, atau jenis armada

## 5. Non-MVP

Yang sengaja ditunda dulu:
- mobile app native / APK
- notifikasi WhatsApp otomatis
- integrasi ke ERP, accounting, atau sistem operasional lain
- dashboard analitik lanjutan
- optimasi AI / rekomendasi harga otomatis
- approval bertingkat yang kompleks
- export dokumen quote yang sangat detail atau sangat mewah

## 6. Tujuan Bisnis dan Nilai Produk

Nilai bisnis yang diharapkan:
- proses penentuan harga lebih cepat
- data modal menjadi aset internal perusahaan
- kualitas penawaran lebih konsisten antar sales
- histori inquiry bisa dipakai sebagai referensi keputusan berikutnya
- manajemen punya visibilitas lebih baik terhadap sumber harga dan margin

## 7. Success Metric

`Metric` (angka untuk mengukur hasil):
- waktu pembuatan quote berkurang dibanding proses manual
- jumlah inquiry yang berhasil tercatat di sistem meningkat
- frekuensi bertanya ulang ke vendor untuk rute yang sama menurun
- jumlah quote yang dapat ditemukan kembali dari histori meningkat
- manager dapat melakukan approval tanpa harus cek data dari chat terpisah

Catatan:
- angka target detail bisa ditentukan setelah user flow versi awal diuji

## 8. Constraint

`Constraint` (batasan proyek):
- budget minimal
- tim pengembang sangat kecil
- kebutuhan bisnis masih berkembang
- sistem harus nyaman dipakai dari laptop dan HP
- desain data harus cukup fleksibel karena skema pengiriman bisa berbeda-beda

## 9. Asumsi Awal

`Assumption` (anggapan kerja sementara sebelum dibuktikan):
- user bersedia input data ke sistem jika flow-nya lebih cepat dari cara manual
- quote perlu approval sebelum dianggap final
- harga modal perlu dicatat sampai level vendor, rute, dan leg
- masa berlaku quote berada di rentang 3 sampai 6 bulan, tergantung kebijakan bisnis

## 10. Risiko Awal

`Risk` (hal yang bisa bikin proyek melenceng):
- aturan pricing belum stabil dan bisa berubah di tengah jalan
- inquiry kompleks bisa memunculkan kebutuhan data baru
- master data bisa membesar dengan cepat
- user bisa enggan memakai sistem jika input terlalu panjang
- struktur database bisa berubah saat pemahaman bisnis bertambah

Mitigasi awal:
- pakai model data yang fleksibel
- bangun fitur inti dulu sebelum fitur tambahan
- validasi flow dengan user nyata secepat mungkin
- simpan data inti yang stabil di kolom tetap, dan detail yang masih berubah di struktur yang lebih fleksibel saat dibutuhkan

## 11. Acceptance Criteria

`Acceptance criteria` (syarat fitur dianggap selesai dan layak dipakai):
- sales bisa membuat inquiry baru tanpa bantuan admin
- sales bisa mencatat contact pickup dan contact drop dalam inquiry
- sales bisa membuat minimal 1 scenario dengan beberapa leg
- sales bisa mencatat harga modal dari vendor per leg
- sistem bisa menghitung total biaya dari cost item
- sistem bisa menghasilkan quote dengan masa berlaku
- quote bisa masuk ke proses approval manager
- quote dan histori pricing bisa ditemukan kembali melalui pencarian
- data penting memiliki jejak perubahan dasar

## 12. Pertanyaan Lanjutan

Pertanyaan ini belum harus final hari ini, tapi perlu kita jawab sebelum implementasi besar:
- apakah approval manager cukup 1 level atau perlu lebih dari 1 level di masa depan
- apakah masa berlaku quote default akan dibuat 3 bulan, 6 bulan, atau dipilih manual saat membuat quote
- apakah semua sales boleh override margin atau hanya role tertentu
- apakah histori harga akan dipakai hanya sebagai referensi manual atau nanti menjadi dasar saran otomatis
