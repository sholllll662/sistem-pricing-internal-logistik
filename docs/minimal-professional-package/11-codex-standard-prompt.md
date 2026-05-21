# Codex Standard Prompt v0.1

Dokumen ini berisi prompt standar yang bisa kamu copas saat ingin meminta Codex mengerjakan 1 GitHub issue. Tujuannya supaya hasil kerja tetap konsisten: baca issue, patuhi scope, implementasi rapi, lalu siapkan ringkasan PR.

## 1. Prinsip Prompt

Prompt yang sehat untuk Codex harus meminta hal-hal ini:
- baca issue dulu
- pahami scope dan `out of scope`
- baca dokumen fondasi yang relevan
- implementasikan minimal tapi rapi
- jangan melebar ke fitur lain
- lakukan verifikasi yang masuk akal
- ringkas hasil kerja untuk PR

## 2. Prompt Standar Utama

Gunakan prompt ini saat issue sudah ada dan kamu mau Codex langsung mulai kerja.

```text
Kerjakan issue ini:
[PASTE LINK GITHUB ISSUE ATAU ISI ISSUE]

Instruksi kerja:
1. Baca issue dengan teliti.
2. Pahami `goal`, `scope`, `out of scope`, `acceptance criteria`, dan `technical notes`.
3. Baca dokumen fondasi project yang relevan dengan issue ini. Jangan baca dokumen yang tidak relevan kalau hanya akan menambah noise.
4. Sebelum mulai implementasi, rangkum pemahamanmu singkat:
   - tujuan issue
   - apa yang akan dikerjakan
   - apa yang tidak akan dikerjakan
   - risiko atau asumsi penting
5. Implementasikan issue ini secara minimal, rapi, dan sesuai struktur project yang ada.
6. Jangan memperluas scope tanpa alasan yang benar-benar kuat.
7. Jika issue punya area yang ambigu, ambil keputusan yang paling aman dan jelaskan asumsi yang kamu pakai.
8. Lakukan verifikasi yang relevan untuk perubahan ini.
9. Setelah selesai, berikan hasil akhir dalam format:
   - ringkasan implementasi
   - file penting yang diubah
   - verifikasi yang dijalankan
   - hal yang sengaja belum dikerjakan karena out of scope
   - draft judul PR
   - draft deskripsi PR

Aturan penting:
- patuhi scope issue
- hormati out of scope
- jangan refactor area lain kalau tidak diperlukan
- jangan tambahkan package baru kecuali memang diperlukan untuk menyelesaikan issue
- jaga perubahan tetap fokus dan mudah direview
```

## 3. Prompt Versi GitHub Issue Link

Gunakan versi ini kalau kamu benar-benar mau memberi link issue.

```text
Saya ingin kamu mengimplementasikan GitHub issue ini:
[PASTE LINK GITHUB ISSUE]

Kalau kamu tidak bisa mengakses link issue secara langsung dari environment ini, bilang secara eksplisit lalu minta saya paste isi issue-nya. Jangan menebak isi issue.

Kalau issue bisa diakses, lakukan hal berikut:
- baca issue dengan teliti
- baca dokumen fondasi yang relevan
- rangkum pemahamanmu
- implementasikan sesuai scope
- lakukan verifikasi yang relevan
- siapkan ringkasan PR
```

Catatan:
- prompt ini sengaja menyuruh Codex jujur kalau tidak bisa baca issue link
- ini penting kalau repo GitHub private atau plugin GitHub belum aktif

## 4. Prompt Versi File Lokal

Versi ini paling stabil kalau issue juga disimpan di repo sebagai file lokal.

```text
Kerjakan issue yang ada di file ini:
[PASTE PATH FILE ISSUE]

Baca isi issue tersebut, baca dokumen fondasi yang relevan, lalu implementasikan sesuai scope dan acceptance criteria. Jangan melebar ke luar issue. Setelah selesai, rangkum perubahan, verifikasi, dan siapkan draft PR title + PR description.
```

## 5. Prompt Versi Strict Scope

Gunakan kalau kamu ingin Codex benar-benar disiplin dan tidak melebar.

```text
Kerjakan issue ini dengan disiplin scope:
[PASTE LINK ATAU ISI ISSUE]

Instruksi tambahan:
- jangan kerjakan apa pun di luar scope issue
- kalau menemukan ide tambahan, catat sebagai rekomendasi saja, jangan diimplementasikan
- kalau ada blocker, jelaskan blocker-nya dan berhenti di titik yang aman
- prioritaskan hasil yang kecil, jelas, dan bisa di-review cepat
```

## 6. Prompt Versi Branch dan PR

Gunakan jika kamu ingin Codex sekalian memikirkan branch dan PR summary.

```text
Kerjakan issue ini:
[PASTE LINK ATAU ISI ISSUE]

Selain implementasi, lakukan juga hal berikut:
- usulkan nama branch yang sesuai
- implementasikan perubahan dengan scope yang fokus
- lakukan verifikasi yang relevan
- buat draft PR title
- buat draft PR description yang singkat dan jelas

Jika environment dan akses git memungkinkan, siapkan perubahan agar mudah dipush ke branch feature.
```

Catatan penting:
- kalimat `jika environment dan akses git memungkinkan` sengaja dipakai supaya Codex tidak pura-pura bisa membuat PR kalau akses GitHub memang tidak ada

## 7. Rekomendasi Format Output dari Codex

Supaya hasilnya konsisten, kamu bisa minta Codex menutup pekerjaannya dengan format seperti ini:

```text
Berikan hasil akhir dengan format:
- Summary
- Files Changed
- Verification
- Out of Scope Respected
- PR Title
- PR Description
```

## 8. Aturan Memilih Dokumen Fondasi

Tidak semua issue harus membaca semua dokumen.

Contoh:
- issue `project scaffolding` cukup fokus ke `01`, `04`, `06`, dan `10`
- issue `database migration` akan butuh `02`, `07`, dan `10`
- issue `pricing calculation` akan butuh `02`, `08`, dan `10`
- issue `authorization` akan butuh `02`, `04`, `09`, dan `10`

Ini penting supaya konteks yang dibaca tetap relevan dan tidak terlalu berat.

## 9. Kenapa Tidak Semua Dokumen Selalu Disebut

Alasannya sederhana:
- tidak semua dokumen relevan untuk semua issue
- terlalu banyak konteks bisa membuat implementasi melebar
- issue yang baik seharusnya menunjuk hanya ke fondasi yang paling relevan

Contoh untuk `ISSUE-001` scaffolding:
- `07-database-schema-v0.1.md` belum dibutuhkan karena belum masuk migration bisnis
- `08-pricing-and-approval-rules.md` belum dibutuhkan karena belum menghitung harga atau approval
- `09-role-and-permission-matrix.md` belum dibutuhkan penuh karena sistem role lengkap masih out of scope

## 10. Cara Pakai yang Disarankan

Urutan kerja yang paling sehat:
1. buat issue di GitHub
2. pastikan issue punya scope dan out of scope
3. copas prompt standar ini
4. tempel link atau isi issue
5. minta Codex implementasi
6. review hasil
7. merge lewat PR

## 11. Output dari Dokumen Ini

Kalau dokumen ini sudah ada, kamu tinggal:
- upload issue ke GitHub
- copas prompt yang sesuai
- minta Codex kerja per issue secara konsisten
