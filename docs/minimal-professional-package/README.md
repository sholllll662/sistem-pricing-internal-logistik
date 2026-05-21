# Paket Minimal Profesional

Paket ini adalah fondasi kerja sebelum kita membangun aplikasi.

Tujuannya:
- mengurangi bongkar pasang di tengah jalan
- membuat keputusan teknis lebih sadar
- memberi urutan kerja yang jelas dari ide sampai go-live

Isi paket:
1. `01-prd-lite.md`
2. `02-domain-and-business-rules.md`
3. `03-user-flow-and-screens.md`
4. `04-implementation-roadmap.md`
5. `05-go-live-checklist.md`
6. `06-scaffolding-execution-plan.md`
7. `07-database-schema-v0.1.md`
8. `08-pricing-and-approval-rules.md`
9. `09-role-and-permission-matrix.md`
10. `10-delivery-backlog-map.md`
11. `11-codex-standard-prompt.md`

Urutan pakai:
1. isi `01-prd-lite.md`
2. isi `02-domain-and-business-rules.md`
3. isi `03-user-flow-and-screens.md`
4. pakai `04-implementation-roadmap.md` untuk mulai scaffolding dan coding
5. pakai `05-go-live-checklist.md` saat mendekati release
6. pakai `06-scaffolding-execution-plan.md` saat siap mulai setup project Laravel beneran
7. pakai `07-database-schema-v0.1.md` saat mulai menulis migration dan model
8. pakai `08-pricing-and-approval-rules.md` saat mulai menulis logic bisnis
9. pakai `09-role-and-permission-matrix.md` saat mulai menulis auth dan authorization
10. pakai `10-delivery-backlog-map.md` saat mulai memecah issue implementasi
11. pakai `11-codex-standard-prompt.md` saat mulai workflow GitHub issue -> Codex -> PR

Cara baca istilah teknis di paket ini:
- setiap istilah teknis diikuti penjelasan singkat dalam kurung
- contoh: `vertical slice` (1 potongan fitur utuh dari UI sampai database)

Aturan kerja yang disarankan:
- jangan isi semuanya sekaligus
- cukup isi versi `v0.1` dulu
- kalau ada perubahan ide, update dokumen ini dulu sebelum ubah kode besar

Checklist fondasi yang dianggap cukup untuk mulai build end-to-end:
- arah produk jelas
- domain bisnis jelas
- user flow jelas
- roadmap implementasi jelas
- go-live checklist ada
- scaffolding plan ada
- schema database awal ada
- aturan pricing dan approval tertulis
- role dan permission jelas
- backlog awal bisa dipecah menjadi issue
