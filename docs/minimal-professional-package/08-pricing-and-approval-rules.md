# Pricing and Approval Rules v0.1

Dokumen ini menjelaskan logika bisnis paling sensitif di sistem: cara menghitung harga dan cara quote disetujui. Tanpa dokumen ini, backend biasanya cepat jadi kacau karena angka dan status mudah berubah-ubah.

## 1. Tujuan Dokumen

Tujuan dokumen ini:
- menyamakan cara hitung modal dan harga jual
- menentukan kapan user boleh override angka
- menentukan kapan quote boleh diajukan
- menentukan alur approval secara konsisten

## 2. Istilah Perhitungan

Istilah yang dipakai:
- `Base Cost / Modal`: total biaya yang keluar ke vendor atau biaya operasional
- `Cost Item`: satu komponen biaya
- `Leg Cost`: total modal dari satu leg
- `Scenario Cost`: total modal dari semua leg dalam satu scenario
- `Margin`: keuntungan yang ditambahkan di atas modal
- `Surcharge`: biaya tambahan di luar modal dasar
- `Selling Price`: harga jual akhir ke customer
- `Snapshot`: salinan angka final pada saat quote dibuat

## 3. Rumus Dasar

Rumus per `cost item`:

```text
line_total = quantity x unit_price
```

Rumus per `leg`:

```text
leg_base_cost = sum(line_total semua cost item pada leg)
```

Rumus per `scenario`:

```text
scenario_base_cost = sum(leg_base_cost semua leg)
```

Rumus harga jual sederhana:

```text
selling_price = scenario_base_cost + margin + surcharge
```

Catatan:
- di `v0.1`, margin lebih aman diperlakukan sebagai nominal tetap dulu
- kalau nanti dibutuhkan, kita bisa tambah mode margin persen

## 4. Mode Margin

Mode margin yang disarankan untuk versi awal:
- `fixed amount` (nominal tetap)
- `percentage` opsional untuk fase berikutnya

Alasan mulai dari nominal tetap:
- lebih mudah dipahami user
- lebih aman untuk implementasi awal
- lebih gampang diaudit

Kalau nanti margin persen dipakai, rumusnya:

```text
margin_amount = scenario_base_cost x margin_percent
selling_price = scenario_base_cost + margin_amount + surcharge
```

## 5. Urutan Hitung

Urutan sistem menghitung:
1. hitung semua `line_total`
2. jumlahkan menjadi `leg_base_cost`
3. jumlahkan semua leg menjadi `scenario_base_cost`
4. terapkan margin
5. tambahkan surcharge jika ada
6. hasil akhir menjadi `selling_price`

Ini penting supaya hasil hitung konsisten di semua halaman.

## 6. Aturan Override

`Override` (user mengganti nilai default secara manual) tetap harus diperbolehkan, karena dunia logistik tidak selalu rapi.

Aturan awal:
- sales boleh input harga manual pada `cost item`
- sales boleh mengubah margin jika role-nya mengizinkan
- perubahan manual harus ditandai
- perubahan manual sebaiknya menyimpan alasan singkat atau sumber harga

Field minimum yang perlu ditandai:
- `is_manual_override`
- `price_source_reference`
- `price_source_date`

## 7. Aturan Histori Harga

Histori harga dipakai sebagai referensi, bukan autopilot.

Aturan awal:
- histori harga boleh ditampilkan berdasarkan route, vendor, vehicle type, dan service pattern
- user tetap memilih angka final secara sadar
- histori tidak langsung menggantikan harga baru tanpa input user

Hal yang sebaiknya ditampilkan ke user:
- harga modal sebelumnya
- vendor sumber harga
- tanggal harga
- route terkait
- vehicle type terkait

## 8. Aturan Scenario

Aturan untuk `scenario`:
- 1 inquiry boleh punya beberapa scenario
- tiap scenario dihitung terpisah
- hanya 1 scenario yang dipilih untuk menjadi quote utama pada satu versi quote
- scenario yang tidak dipilih tetap disimpan sebagai referensi perbandingan

## 9. Aturan Quote

Aturan quote awal:
- quote harus mengacu ke 1 inquiry dan 1 scenario
- quote wajib punya `valid_from` dan `valid_until`
- masa berlaku awal berada di rentang `3-6 bulan`
- angka final quote harus disimpan sebagai snapshot
- perubahan scenario setelah quote dibuat tidak boleh otomatis mengubah quote lama

Alasan:
- histori quote harus tetap sesuai angka yang pernah diajukan ke customer

## 10. Aturan Approval

Aturan approval awal:
- quote tidak boleh dikirim ke customer sebelum status approval `approved`
- quote diajukan oleh sales
- quote direview oleh manager
- manager bisa `approve` atau `reject`
- jika reject, alasan reject wajib disimpan

## 11. Alur Status Approval

Alur status yang disarankan:

```text
draft -> waiting_approval -> approved -> sent
draft -> waiting_approval -> rejected
sent -> accepted / rejected / expired
```

## 12. Aturan Setelah Approval

Setelah quote berstatus `approved`:
- sales boleh mengirim quote ke customer
- jika ada perubahan angka penting, quote sebaiknya kembali ke `draft` atau dibuat revisi
- perubahan setelah approval tidak boleh diam-diam menimpa snapshot lama

`Revision` (versi baru dari quote) bisa ditambahkan di fase berikutnya jika diperlukan.

## 13. Edge Case yang Harus Dipikirkan

`Edge case` (kasus khusus di luar alur normal):
- scenario belum punya leg lengkap tapi user menekan submit
- ada leg tanpa cost item
- margin negatif
- quote sudah expired tapi ingin dipakai lagi
- manager reject karena harga terlalu rendah
- histori harga belum tersedia untuk route tertentu

Aturan awal untuk edge case:
- scenario tidak bisa diajukan jika belum lengkap
- cost item kosong berarti total belum valid
- margin negatif tidak diizinkan di `v0.1`
- quote expired harus dibuat ulang atau direvisi

## 14. Validasi Minimum

Validasi hitung yang disarankan:
- quantity tidak boleh kurang dari 0
- unit_price tidak boleh kurang dari 0
- line_total harus sinkron dengan quantity x unit_price
- scenario_base_cost harus sinkron dengan total leg
- selling_price tidak boleh lebih kecil dari scenario_base_cost kecuali ada aturan bisnis khusus
- valid_until tidak boleh lebih awal dari valid_from

## 15. Output Teknis untuk Backend

Backend nanti minimal perlu punya:
- kalkulasi per `cost item`
- kalkulasi total per leg
- kalkulasi total per scenario
- kalkulasi selling price
- validasi approval eligibility (apakah quote layak diajukan)
- penyimpanan snapshot quote

`Eligibility` (syarat sebuah data boleh masuk tahap berikutnya) penting supaya status tidak loncat sembarangan.

## 16. Keputusan yang Boleh Ditunda

Belum wajib diputuskan di `v0.1`:
- margin per customer
- margin per service type
- auto-suggestion harga
- tax / PPN detail
- diskon khusus
- kurs multi-currency

## 17. Output dari Dokumen Ini

Kalau dokumen ini sudah disetujui, nanti bisa dipecah menjadi:
- service class pricing
- validation rules
- approval policy
- feature test untuk alur quote
