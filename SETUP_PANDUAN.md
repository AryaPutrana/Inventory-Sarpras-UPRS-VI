# SISTEM INFORMASI INVENTORY MATERIAL SARPRAS UPRS VI

Sistem Informasi berbasis web untuk mengelola inventory material dan pengambilan barang pada Divisi Sarana dan Prasarana UPRS VI.

## 🚀 Fitur Utama

✅ **Dashboard** - Ringkasan total barang, pengambilan, dan nilai
✅ **Inventory Barang** - CRUD data barang dengan foto
✅ **Pengambilan Barang** - Pencatatan pengambilan dengan pengurangan stok otomatis
✅ **Laporan** - Filter berdasarkan tanggal dan rusun
✅ **Export** - PDF dan Excel
✅ **Validasi Stok** - Mencegah pengambilan melebihi stok
✅ **Responsive Design** - Bootstrap 5

## 📋 Teknologi

- Laravel 10
- PHP 8.1+
- MySQL/MariaDB
- Bootstrap 5
- jQuery
- DomPDF

## 🔧 Instalasi

### 1. Clone atau Download Project

Pastikan Anda sudah berada di folder project ini.

### 2. Install Dependencies

```bash
composer install
```

### 3. Konfigurasi Environment

Copy file `.env.example` ke `.env`:

```bash
cp .env.example .env
```

Atau di Windows:
```bash
copy .env.example .env
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Konfigurasi Database

Buka file `.env` dan sesuaikan konfigurasi database:

```env
APP_NAME="SI Inventory Sarpras"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_inventory_uprs_vi
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Buat Database

**Opsi A - Menggunakan phpMyAdmin:**
1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Klik "New" atau "Baru"
3. Nama database: `sistem_inventory_uprs_vi`
4. Collation: `utf8mb4_unicode_ci`
5. Klik "Create"

**Opsi B - Menggunakan MySQL Command Line:**
```bash
mysql -u root -p
CREATE DATABASE sistem_inventory_uprs_vi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Opsi C - Menggunakan Laragon:**
1. Klik kanan ikon Laragon → MySQL → Create Database
2. Nama: `sistem_inventory_uprs_vi`

### 7. Jalankan Migration dan Seeder

```bash
php artisan migrate --seed
```

Ini akan membuat tabel-tabel:
- `rusun` (dengan 6 data rusun: Rawa Bebek, Umen, Tipar, Albo, CBT, KM2)
- `items` (inventory barang)
- `withdrawals` (transaksi pengambilan)

### 8. Buat Storage Link

```bash
php artisan storage:link
```

### 9. Jalankan Server

```bash
php artisan serve
```

Akses aplikasi di: http://localhost:8000

## 📁 Struktur Database

### Tabel `rusun`
- `id` - Primary Key
- `code` - Kode rusun (RABEK, UMEN, TIPAR, ALBO, CBT, KM2)
- `name` - Nama rusun
- `created_at`, `updated_at`

### Tabel `items`
- `id` - Primary Key
- `item_code` - ID Barang (unique)
- `name` - Nama Barang
- `photo` - Foto Barang
- `unit_price` - Harga Satuan
- `stock` - Jumlah Stok
- `unit` - Satuan (pcs, box, meter, dll)
- `description` - Keterangan
- `created_at`, `updated_at`

### Tabel `withdrawals`
- `id` - Primary Key
- `item_id` - Foreign key ke items
- `taken_by` - Nama Pengambil
- `rusun_id` - Foreign key ke rusun
- `quantity` - Jumlah
- `unit_price` - Harga Satuan (snapshot)
- `subtotal` - Total (otomatis)
- `taken_at` - Tanggal & Waktu Pengambilan
- `description` - Keterangan
- `created_at`, `updated_at`

## 🎯 Penggunaan

### Dashboard
- Lihat ringkasan: Total Barang, Total Pengambilan, Pengambilan Bulan Ini, Total Nilai
- Lihat 5 pengambilan terbaru

### Inventory Barang
1. **Tambah Barang**: Isi ID Barang, Nama, Upload Foto, Harga, Stok, Satuan
2. **Edit Barang**: Ubah data barang yang sudah ada
3. **Detail Barang**: Lihat informasi lengkap barang
4. **Hapus Barang**: Hapus barang (otomatis hapus foto)
5. **Cari Barang**: Filter berdasarkan ID atau Nama Barang

### Pengambilan Barang
1. Pilih Barang dari dropdown (hanya barang dengan stok > 0)
2. ID Barang dan Harga Satuan akan terisi otomatis
3. Isi Nama Pengambil
4. Pilih Rusun
5. Isi Jumlah (sistem akan validasi dengan stok tersedia)
6. Subtotal dihitung otomatis
7. Pilih Tanggal dan Waktu Pengambilan
8. Simpan - Stok akan berkurang otomatis

### Laporan
1. Pilih Start Date dan End Date
2. Pilih Rusun (atau "Semua Rusun")
3. Klik "Tampilkan Laporan"
4. Lihat data transaksi dengan total
5. Export ke PDF atau Excel
6. Atau Cetak langsung

## 📸 Upload Foto

Foto barang disimpan di: `storage/app/public/items/`

Format yang didukung:
- JPG, JPEG, PNG, WEBP
- Maksimal 2MB

## 🔐 Validasi dan Aturan Bisnis

✅ ID Barang harus unik
✅ Foto barang wajib diupload saat tambah barang
✅ Harga dan stok tidak boleh negatif
✅ Jumlah pengambilan tidak boleh melebihi stok
✅ Stok berkurang otomatis setelah pengambilan
✅ Subtotal dihitung otomatis: Jumlah × Harga Satuan

## 📊 Menu Sistem

```
SI INVENTORY SARPRAS
├── Dashboard
├── Inventory Barang
│   ├── Data Barang
│   ├── Tambah Barang
│   ├── Edit Barang
│   └── Detail Barang
├── Pengambilan Barang
│   ├── Pengambilan Baru
│   └── Riwayat Pengambilan
└── Laporan
    └── Laporan Pengambilan
```

## 🛠️ Troubleshooting

### Error "No connection could be made"
- Pastikan MySQL/MariaDB sudah running
- Periksa konfigurasi DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD di `.env`

### Error "SQLSTATE[HY000] [1049] Unknown database"
- Database belum dibuat. Buat database sesuai langkah #6

### Foto tidak muncul
- Jalankan `php artisan storage:link`
- Pastikan folder `storage/app/public/items/` ada

### Migration error
- Jalankan `php artisan migrate:fresh --seed` untuk reset database

### Composer not found
- Install Composer dari https://getcomposer.org/

## 📞 Dukungan

Sistem ini dibuat sesuai dengan PRD (Product Requirement Document) Sistem Informasi Inventory Material Sarana dan Prasarana UPRS VI.

## 📝 Lisensi

Sistem Internal - Divisi Sarana dan Prasarana UPRS VI

---

**Dibuat dengan ❤️ untuk UPRS VI**
