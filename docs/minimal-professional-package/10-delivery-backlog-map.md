# Delivery Backlog Map v0.1

Dokumen ini mengubah fondasi proyek menjadi urutan kerja yang bisa dipecah menjadi GitHub issue. Ini penting untuk workflow `vibe coding` yang ingin kamu pakai: 1 issue, 1 implementasi, 1 PR.

## 1. Tujuan Dokumen

Tujuan dokumen ini:
- memecah proyek besar jadi unit kerja kecil
- mengurangi issue yang terlalu kabur
- mempermudah Codex mengimplementasikan pekerjaan satu per satu

## 2. Aturan Memecah Issue

Prinsip backlog:
- 1 issue = 1 hasil yang jelas
- issue awal fokus ke fondasi, bukan fitur mewah
- issue harus punya `scope` dan `out of scope`
- issue harus bisa diverifikasi selesai atau belum
- jangan gabungkan terlalu banyak area ke 1 issue

## 3. Epic Utama

`Epic` (kelompok besar issue yang sejenis) yang disarankan:
- Epic A: project foundation
- Epic B: identity and access
- Epic C: master data
- Epic D: inquiry workspace
- Epic E: pricing and quote
- Epic F: approval and history
- Epic G: hardening and release readiness

## 4. Urutan Issue Level Tinggi

### Epic A: Project Foundation

- `ISSUE-001` scaffold Laravel + Livewire + Filament + PostgreSQL
- `ISSUE-002` setup role seeder dan admin bootstrap
- `ISSUE-003` setup base layout, navigation, dan access gate dasar

### Epic B: Identity and Access

- `ISSUE-004` implement role model dan relationship user-role
- `ISSUE-005` implement policy dasar untuk inquiry dan quote
- `ISSUE-006` protect `/admin` dan menu berdasarkan role

### Epic C: Master Data

- `ISSUE-007` create customer migration, model, and Filament resource
- `ISSUE-008` create customer contact module
- `ISSUE-009` create vendor and vendor contact module
- `ISSUE-010` create location module
- `ISSUE-011` create transport mode and vehicle type module
- `ISSUE-012` create cost category module

### Epic D: Inquiry Workspace

- `ISSUE-013` create inquiry migration and model
- `ISSUE-014` build inquiry create/edit page
- `ISSUE-015` create inquiry scenario migration and model
- `ISSUE-016` build scenario builder skeleton with Livewire
- `ISSUE-017` create scenario leg migration and model
- `ISSUE-018` add leg management to scenario builder
- `ISSUE-019` create leg cost item migration and model
- `ISSUE-020` add cost item entry UI to scenario builder

### Epic E: Pricing and Quote

- `ISSUE-021` implement pricing calculation service
- `ISSUE-022` show pricing summary in scenario builder
- `ISSUE-023` create quote migration and model
- `ISSUE-024` create quote draft flow from selected scenario
- `ISSUE-025` add quote validity period and validation

### Epic F: Approval and History

- `ISSUE-026` create quote approval migration and model
- `ISSUE-027` build manager approval queue
- `ISSUE-028` build quote review page with approve/reject action
- `ISSUE-029` build pricing history and quote history list
- `ISSUE-030` add history filters by customer, route, vendor, and vehicle type

### Epic G: Hardening and Release Readiness

- `ISSUE-031` add audit log foundation
- `ISSUE-032` add feature tests for inquiry and quote flow
- `ISSUE-033` add validation hardening for pricing and approval
- `ISSUE-034` polish empty, loading, and error states
- `ISSUE-035` prepare release checklist and seed data for UAT

## 5. Rekomendasi Issue yang Dikerjakan Dulu

Untuk mulai paling sehat:
1. `ISSUE-001`
2. `ISSUE-002`
3. `ISSUE-007`
4. `ISSUE-008`
5. `ISSUE-009`
6. `ISSUE-013`
7. `ISSUE-014`

Kenapa:
- auth dan role perlu ada dulu
- master data minimum harus siap sebelum inquiry dipakai
- inquiry adalah pintu masuk seluruh flow berikutnya

## 6. Template Mental untuk Menulis Issue

Walau kita belum bikin file template, pola isi issue sebaiknya selalu begini:
- `Background`
- `Goal`
- `Scope`
- `Out of Scope`
- `Acceptance Criteria`
- `Technical Notes`
- `Test Plan`

## 7. Aturan Scope Supaya Codex Enak Kerja

Scope issue yang sehat:
- 1 sampai 3 model maksimum
- 1 area UI utama maksimum
- 1 perubahan besar logic bisnis maksimum

Scope issue yang terlalu besar:
- “bangun semua inquiry module”
- “bikin seluruh approval system”
- “buat semua master data”

Lebih baik pecah kecil, karena:
- review lebih mudah
- PR lebih kecil
- bug lebih gampang dicari
- Codex lebih akurat saat implementasi

## 8. Definition of Done per Issue

Issue dianggap selesai kalau:
- scope terpenuhi
- out of scope tidak ikut melebar
- migration / model / UI sesuai kebutuhan issue
- validasi minimum ada
- user flow utama untuk issue itu bisa diuji
- ada ringkasan PR yang jelas

## 9. Hubungan Dokumen Ini dengan Workflow GitHub

Workflow yang disarankan:
1. pilih 1 issue dari backlog map ini
2. tulis issue di GitHub
3. copy link atau isi issue ke Codex
4. minta Codex implementasi sesuai scope
5. review hasil
6. merge lewat PR

## 10. Keputusan yang Boleh Ditunda

Belum wajib dimasukkan ke backlog awal:
- export PDF final
- AI recommendation
- advanced analytics
- multi-branch approval
- complex notification system

## 11. Output dari Dokumen Ini

Kalau dokumen ini sudah disetujui, kita bisa langsung:
- menulis `ISSUE-001`
- memulai scaffolding
- menjalankan workflow GitHub issue -> Codex -> PR secara konsisten
