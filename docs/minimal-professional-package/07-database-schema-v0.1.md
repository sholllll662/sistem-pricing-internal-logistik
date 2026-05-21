# Database Schema v0.1

Dokumen ini adalah jembatan dari `domain model` ke `migration` Laravel. Tujuannya bukan membuat schema final yang kaku, tapi memberi struktur awal yang cukup stabil agar implementasi tidak liar.

## 1. Prinsip Schema

Prinsip yang kita pegang:
- tabel inti dibuat `relasional` (hubungan tabel yang jelas) supaya data mudah dicari dan diaudit
- detail yang sangat mungkin berubah bisa ditaruh di kolom `jsonb` (kolom JSON PostgreSQL yang fleksibel)
- status penting disimpan sebagai nilai yang konsisten
- angka hasil pricing final disimpan sebagai `snapshot`
- nama tabel mengikuti bentuk jamak sederhana

## 2. Kelompok Tabel

Schema dibagi menjadi 4 kelompok:
- `identity and access` (user dan role)
- `master data` (data referensi)
- `transaction data` (inquiry sampai quote)
- `supporting data` (audit, attachment, notes)

## 3. Identity and Access

### `users`

Tujuan:
- menyimpan user yang bisa login ke sistem

Kolom inti:
- `id`
- `name`
- `email`
- `password`
- `is_active`
- `last_login_at`
- `created_at`
- `updated_at`

Catatan:
- auth dasar Laravel bisa dipakai sebagai fondasi awal

### `roles`

Tujuan:
- menyimpan role seperti `sales`, `manager`, `admin`

Kolom inti:
- `id`
- `code`
- `name`
- `description`

### `role_user`

Tujuan:
- tabel pivot (tabel penghubung many-to-many) antara `users` dan `roles`

Kolom inti:
- `user_id`
- `role_id`

## 4. Master Data

### `customers`

Tujuan:
- menyimpan perusahaan customer

Kolom inti:
- `id`
- `code`
- `name`
- `industry`
- `address`
- `notes`
- `is_active`
- `created_at`
- `updated_at`

### `customer_contacts`

Tujuan:
- menyimpan contact person milik customer

Kolom inti:
- `id`
- `customer_id`
- `name`
- `phone`
- `email`
- `job_title`
- `contact_type`
- `is_primary`
- `created_at`
- `updated_at`

`Contact type` (jenis contact) awal yang aman:
- `general`
- `pickup`
- `drop`

### `vendors`

Tujuan:
- menyimpan vendor atau agen sumber harga modal

Kolom inti:
- `id`
- `code`
- `name`
- `vendor_type`
- `address`
- `notes`
- `is_active`
- `created_at`
- `updated_at`

### `vendor_contacts`

Tujuan:
- menyimpan contact vendor

Kolom inti:
- `id`
- `vendor_id`
- `name`
- `phone`
- `email`
- `job_title`
- `is_primary`
- `created_at`
- `updated_at`

### `locations`

Tujuan:
- menyimpan titik geografis atau operasional

Kolom inti:
- `id`
- `code`
- `name`
- `location_type`
- `country`
- `province`
- `city`
- `district`
- `postal_code`
- `address`
- `latitude`
- `longitude`
- `created_at`
- `updated_at`

`Location type` bisa dipakai untuk:
- `city`
- `warehouse`
- `port`
- `project_site`
- `other`

### `transport_modes`

Tujuan:
- menyimpan moda transport seperti trucking, sea freight, ferry

Kolom inti:
- `id`
- `code`
- `name`
- `description`

### `vehicle_types`

Tujuan:
- menyimpan jenis armada

Kolom inti:
- `id`
- `code`
- `name`
- `description`
- `capacity_notes`

### `cost_categories`

Tujuan:
- menyimpan kategori biaya agar cost item lebih rapi

Kolom inti:
- `id`
- `code`
- `name`
- `description`

Contoh nilai:
- `trucking`
- `handling`
- `admin`
- `ferry`
- `surcharge`
- `other`

## 5. Transaction Data

### `inquiries`

Tujuan:
- menyimpan permintaan harga dari customer

Kolom inti:
- `id`
- `inquiry_number`
- `customer_id`
- `sales_owner_id`
- `pickup_contact_id`
- `drop_contact_id`
- `origin_location_id`
- `destination_location_id`
- `cargo_name`
- `cargo_description`
- `cargo_weight`
- `cargo_volume`
- `cargo_dimension_notes`
- `service_notes`
- `status`
- `submitted_at`
- `closed_at`
- `metadata_jsonb`
- `created_at`
- `updated_at`

Catatan:
- `metadata_jsonb` dipakai untuk detail yang belum stabil bentuknya

### `inquiry_scenarios`

Tujuan:
- menyimpan beberapa opsi skema pengiriman untuk 1 inquiry

Kolom inti:
- `id`
- `inquiry_id`
- `scenario_code`
- `scenario_name`
- `description`
- `status`
- `is_selected`
- `total_base_cost_snapshot`
- `total_margin_snapshot`
- `total_selling_price_snapshot`
- `calculation_notes`
- `metadata_jsonb`
- `created_at`
- `updated_at`

### `scenario_legs`

Tujuan:
- menyimpan tahap pengiriman per scenario

Kolom inti:
- `id`
- `scenario_id`
- `sequence_no`
- `leg_type`
- `origin_location_id`
- `destination_location_id`
- `transport_mode_id`
- `vehicle_type_id`
- `primary_vendor_id`
- `distance_notes`
- `lead_time_notes`
- `operation_notes`
- `base_cost_snapshot`
- `metadata_jsonb`
- `created_at`
- `updated_at`

`Leg type` awal:
- `first_mile`
- `middle_mile`
- `last_mile`
- `custom`

### `leg_cost_items`

Tujuan:
- menyimpan komponen biaya di dalam tiap leg

Kolom inti:
- `id`
- `leg_id`
- `cost_category_id`
- `vendor_id`
- `item_name`
- `description`
- `quantity`
- `unit_name`
- `unit_price`
- `line_total`
- `price_source_date`
- `price_source_reference`
- `is_manual_override`
- `created_at`
- `updated_at`

`Manual override` (nilai diubah manual oleh user) penting untuk menandai angka yang tidak diambil dari pola normal.

### `quotes`

Tujuan:
- menyimpan penawaran ke customer

Kolom inti:
- `id`
- `quote_number`
- `inquiry_id`
- `scenario_id`
- `prepared_by_user_id`
- `valid_from`
- `valid_until`
- `total_base_cost_snapshot`
- `total_margin_snapshot`
- `total_selling_price_snapshot`
- `status`
- `approval_status`
- `customer_notes`
- `internal_notes`
- `sent_at`
- `accepted_at`
- `rejected_at`
- `expired_at`
- `created_at`
- `updated_at`

### `quote_approvals`

Tujuan:
- menyimpan jejak approval quote

Kolom inti:
- `id`
- `quote_id`
- `approver_user_id`
- `decision`
- `decision_notes`
- `decided_at`
- `created_at`
- `updated_at`

`Decision` awal:
- `pending`
- `approved`
- `rejected`

## 6. Supporting Data

### `attachments`

Tujuan:
- menyimpan lampiran seperti bukti harga vendor atau file pendukung inquiry

Kolom inti:
- `id`
- `attachable_type`
- `attachable_id`
- `file_name`
- `file_path`
- `mime_type`
- `uploaded_by_user_id`
- `created_at`

`Attachable` berarti pola `polymorphic relation` (1 tabel bisa menempel ke beberapa jenis data).

### `notes`

Tujuan:
- menyimpan catatan internal yang tidak selalu cocok menjadi kolom tetap

Kolom inti:
- `id`
- `noteable_type`
- `noteable_id`
- `body`
- `created_by_user_id`
- `created_at`
- `updated_at`

### `audit_logs`

Tujuan:
- menyimpan jejak perubahan data penting

Kolom inti:
- `id`
- `auditable_type`
- `auditable_id`
- `event_name`
- `old_values_jsonb`
- `new_values_jsonb`
- `changed_by_user_id`
- `changed_at`

## 7. Status yang Disarankan

### `inquiries.status`
- `draft`
- `submitted`
- `pricing_in_progress`
- `waiting_approval`
- `quoted`
- `closed`
- `canceled`

### `inquiry_scenarios.status`
- `draft`
- `calculated`
- `selected`
- `archived`

### `quotes.status`
- `draft`
- `waiting_approval`
- `approved`
- `sent`
- `accepted`
- `rejected`
- `expired`

## 8. Relasi Inti

Relasi yang harus aman dari awal:
- `customers` 1-to-many `inquiries`
- `customers` 1-to-many `customer_contacts`
- `inquiries` 1-to-many `inquiry_scenarios`
- `inquiry_scenarios` 1-to-many `scenario_legs`
- `scenario_legs` 1-to-many `leg_cost_items`
- `inquiries` 1-to-many `quotes`
- `quotes` 1-to-many `quote_approvals`
- `vendors` 1-to-many `vendor_contacts`

## 9. Index yang Disarankan

`Index` (struktur database untuk mempercepat pencarian) awal yang layak dipasang:
- `inquiries.inquiry_number`
- `quotes.quote_number`
- `inquiries.customer_id`
- `inquiries.sales_owner_id`
- `inquiries.status`
- `inquiry_scenarios.inquiry_id`
- `scenario_legs.scenario_id`
- `leg_cost_items.leg_id`
- `quotes.inquiry_id`
- `quotes.scenario_id`
- `quotes.status`
- `quote_approvals.quote_id`

## 10. Aturan Desain Migration

Aturan saat nanti kita menulis migration:
- mulai dari tabel yang paling independen dulu
- pakai foreign key untuk relasi inti
- pakai `nullable` hanya jika memang masuk akal
- jangan terlalu banyak `jsonb` kalau sebenarnya datanya stabil
- tambah index hanya untuk query yang memang sering dipakai

## 11. Keputusan yang Boleh Ditunda

Belum wajib diputuskan di `v0.1`:
- soft delete di semua tabel atau tidak
- multi-currency
- tax breakdown detail
- version table khusus untuk quote revision
- table khusus route master

## 12. Output dari Dokumen Ini

Kalau dokumen ini sudah disetujui, langkah berikutnya nanti adalah:
- pecah menjadi urutan migration
- pecah menjadi model Laravel
- pecah menjadi resource `Filament`
- pecah menjadi `Livewire` page untuk flow transaksi
