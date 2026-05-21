# Go-Live Checklist v0.1

Dokumen ini dipakai saat sistem mulai mendekati `production` (lingkungan asli yang dipakai user nyata). Tujuannya bukan mencari sistem yang sempurna, tapi memastikan sistem cukup aman, cukup jelas, dan cukup siap untuk dipakai.

## 1. Prinsip Go-Live

Hal yang perlu diingat sebelum release:
- go-live tidak berarti semua fitur sudah selesai
- go-live berarti flow utama sudah cukup layak dipakai user nyata
- bug kecil masih mungkin ada
- yang penting adalah ada cara memantau, memperbaiki, dan rollback jika terjadi masalah

`Rollback` (balik ke versi sistem sebelumnya) penting supaya release tidak bikin panik saat ada error besar.

## 2. Product Readiness

Checklist kesiapan produk:
- flow utama user sudah selesai dari awal sampai akhir
- user bisa login, membuat inquiry, membuat scenario, menghitung pricing, dan menyimpan quote
- manager bisa melihat dan memproses approval
- histori pricing bisa dicari kembali
- data penting sudah tervalidasi
- edge case utama sudah dicoba

`Edge case` (kasus khusus di luar alur normal) contoh:
- inquiry punya lebih dari 1 scenario
- scenario punya banyak leg
- quote ditolak manager
- histori harga untuk rute tertentu belum ada

## 3. Business Readiness

Checklist kesiapan bisnis:
- owner bisnis setuju bahwa scope MVP sudah cukup untuk dipakai
- user target tahu apa yang sudah didukung sistem dan apa yang belum
- aturan kerja baru sudah dijelaskan ke tim sales
- proses lama yang diganti sudah diidentifikasi
- aturan approval quote sudah jelas
- masa berlaku quote sudah jelas

## 4. Master Data Readiness

Checklist kesiapan data referensi:
- customer utama sudah tersedia
- customer contact minimum sudah tersedia
- vendor / agent utama sudah tersedia
- location penting sudah tersedia
- vehicle type penting sudah tersedia
- transport mode penting sudah tersedia
- cost category minimum sudah tersedia jika dipakai

Catatan:
- banyak sistem gagal dipakai bukan karena coding-nya jelek, tapi karena master data awal belum siap

## 5. Technical Readiness

Checklist kesiapan teknis:
- environment production sudah siap
- file `.env` production sudah benar
- database production sudah dibuat
- migration sudah bisa jalan dengan benar
- backup database aktif
- restore test pernah dicoba minimal 1 kali
- logging error aktif
- permission dan role sudah dicek
- proses login berjalan stabil

`Migration` (file yang membuat atau mengubah struktur tabel database) harus sinkron dengan kondisi production.

`Permission` (hak akses detail per aksi) harus dicek bukan cuma di UI, tapi juga di backend.

## 6. Security Minimum

Checklist keamanan dasar:
- input penting tervalidasi
- user tidak bisa mengakses data tanpa login
- role sales tidak bisa masuk ke area admin yang tidak perlu
- role manager hanya bisa approve data yang memang harus direview
- data sensitif tidak tampil ke role yang tidak berhak
- endpoint penting dilindungi auth dan authorization

`Authorization` (pengecekan apakah user boleh melakukan aksi tertentu) berbeda dengan `authentication` (pengecekan apakah user sudah login).

## 7. Operational Readiness

Checklist kesiapan operasional:
- ada PIC teknis jika sistem error
- ada PIC bisnis jika user bingung proses
- ada langkah rollback jika deploy gagal
- ada SOP singkat cara input inquiry
- ada SOP singkat cara approval quote
- ada daftar `known issue` (masalah yang sudah diketahui tapi belum kritis)

`SOP` (Standard Operating Procedure: panduan langkah kerja) tidak perlu panjang, yang penting jelas.

## 8. UAT dan Staging

`UAT` (User Acceptance Test: uji coba oleh user asli) wajib dilakukan sebelum go-live.

Checklist UAT:
- user target sudah mencoba sistem di `staging`
- user berhasil menyelesaikan flow utama tanpa bantuan developer terus-menerus
- bug blocker sudah nol
- feedback penting sudah dicatat
- keputusan mana yang diperbaiki sekarang dan mana yang ditunda sudah jelas

`Staging` (lingkungan uji yang mirip production) penting supaya bug tidak langsung kena user asli.

`Bug blocker` (bug yang membuat sistem tidak layak dipakai) harus benar-benar selesai sebelum live.

## 9. Deployment Readiness

`Deployment` (proses menaikkan aplikasi ke server) harus punya checklist sendiri.

Checklist deploy:
- branch / code yang akan dirilis sudah jelas
- migration yang akan dijalankan sudah dicek
- config cache dan route cache tahu cara pakainya jika dibutuhkan
- asset frontend jika ada sudah dibuild
- jadwal deploy disepakati
- ada orang yang standby setelah deploy

## 10. Monitoring Setelah Release

Checklist 1 sampai 2 minggu pertama:
- pantau error harian
- pantau keluhan user harian
- pantau performa halaman penting
- cek apakah ada data yang gagal tersimpan
- cek apakah approval flow berjalan
- cek apakah user benar-benar memakai sistem atau balik ke cara lama

`Monitoring` (pemantauan sistem setelah live) sama pentingnya dengan coding.

## 11. KPI Setelah Go-Live

Checklist evaluasi awal:
- apakah waktu pembuatan quote menurun
- apakah sales mulai memakai histori pricing
- apakah frekuensi tanya ulang ke vendor berkurang
- apakah data inquiry semakin rapi dan terpusat
- apakah manager lebih cepat melakukan approval

## 12. Rule of Calm

Aturan yang penting setelah release:
- bug kecil setelah go-live itu normal
- jangan panik kalau muncul feedback banyak di minggu pertama
- bedakan `bug` (yang rusak) dengan `feature request` (permintaan baru)
- jangan langsung menambah banyak fitur baru sebelum flow utama stabil
- go-live bukan akhir proyek, tapi awal fase belajar dari user nyata
