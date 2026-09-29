# PANDUAN INSTALASI LENGKAP
# Sistem Inventory Material Sarpras UPRS VI

## TAHAP 1: PERSIAPAN DATABASE

### A. Jalankan Laragon
1. Buka aplikasi Laragon
2. Klik tombol "Start All" untuk menjalankan Apache dan MySQL
3. Tunggu hingga status berubah menjadi hijau (Running)

### B. Buat Database
1. Klik kanan pada Laragon → MySQL → Create Database
   - Nama Database: `sistem_inventory_uprs_vi`
   
ATAU melalui phpMyAdmin:
1. Klik "Database" pada Laragon
2. Buka phpMyAdmin
3. Klik tab "Databases"
4. Buat database baru dengan nama: `sistem_inventory_uprs_vi`
5. Collation: `utf8mb4_unicode_ci`

## TAHAP 2: KONFIGURASI .ENV

Pastikan file `.env` memiliki konfigurasi database yang benar:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_inventory_uprs_vi
DB_USERNAME=root
DB_PASSWORD=
```

## TAHAP 3: MIGRASI DATABASE DAN SEEDER

Jalankan perintah berikut secara berurutan di terminal:

### 1. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 2. Jalankan Migrasi
```bash
php artisan migrate:fresh
```

Ini akan membuat tabel-tabel:
- rusun
- items
- withdrawals
- users
- password_reset_tokens
- failed_jobs
- personal_access_tokens

### 3. Jalankan Seeder
```bash
php artisan db:seed
```

Ini akan mengisi data rusun:
- Rawa Bebek (RABEK)
- Umen (UMEN)
- Tipar (TIPAR)
- Albo (ALBO)
- CBT (CBT)
- KM2 (KM2)

### 4. Setup Storage Link (sudah dilakukan)
```bash
php artisan storage:link
```

## TAHAP 4: JALANKAN APLIKASI

```bash
php artisan serve
```

Aplikasi akan berjalan di: http://127.0.0.1:8000

## TAHAP 5: TESTING SISTEM

### 1. Buka Browser
Akses: http://127.0.0.1:8000

### 2. Menu Dashboard
- Akan menampilkan statistik inventory

### 3. Tambah Barang (Menu Inventory Barang)
- Klik "Tambah Barang"
- Isi form:
  * ID Barang: BRG-001
  * Nama Barang: Cat Tembok 5 Kg
  * Foto: Upload gambar (JPG/PNG/WEBP)
  * Harga Satuan: 125000
  * Jumlah/Stok: 20
  * Satuan: kaleng
  * Keterangan: Material untuk pemeliharaan
- Klik "Simpan"

### 4. Pengambilan Barang
- Klik "Pengambilan Barang" di menu
- Klik "Pengambilan Baru"
- Isi form:
  * Barang: Pilih barang yang sudah ada
  * Pengambil: Budi
  * Rusun: Pilih Rawa Bebek
  * Jumlah: 2
  * Tanggal dan Waktu: (otomatis terisi)
  * Keterangan: Untuk pemeliharaan gedung
- Subtotal akan dihitung otomatis
- Klik "Simpan Pengambilan"
- Stok akan berkurang otomatis

### 5. Laporan
- Klik "Laporan" di menu
- Pilih Start Date dan End Date
- Pilih Rusun (atau "Semua Rusun")
- Klik "Tampilkan Laporan"
- Export PDF atau Excel

## TROUBLESHOOTING

### Error: Connection Refused (2002)
**Solusi:**
1. Pastikan Laragon sudah berjalan
2. Cek MySQL status di Laragon (harus hijau)
3. Restart Laragon jika perlu
4. Periksa file `.env` apakah konfigurasi database sudah benar

### Error: Database doesn't exist
**Solusi:**
1. Buat database manual di phpMyAdmin
2. Nama harus sama dengan yang di `.env`

### Error: SQLSTATE[42S02]: Base table or view not found
**Solusi:**
```bash
php artisan migrate:fresh
php artisan db:seed
```

### Foto tidak muncul
**Solusi:**
```bash
php artisan storage:link
```
Pastikan folder `public/storage` terlink ke `storage/app/public`

### Error 500 saat upload foto
**Solusi:**
1. Pastikan folder `storage/app/public/items` writable
2. Cek permission folder storage

## STRUKTUR FILE PROJECT

```
Sistem_Inventory_UPRS_VI/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── ItemController.php
│   │   ├── WithdrawalController.php
│   │   └── ReportController.php
│   └── Models/
│       ├── Item.php
│       ├── Withdrawal.php
│       └── Rusun.php
├── database/
│   ├── migrations/
│   │   ├── create_rusun_table.php
│   │   ├── create_items_table.php
│   │   └── create_withdrawals_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── RusunSeeder.php
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── dashboard/
│   │   └── index.blade.php
│   ├── items/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── withdrawals/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── show.blade.php
│   └── reports/
│       ├── index.blade.php
│       └── pdf.blade.php
└── routes/
    └── web.php
```

## FITUR YANG SUDAH DIIMPLEMENTASI

✅ Dashboard dengan statistik
✅ CRUD Inventory Barang
✅ Upload foto barang
✅ Pencatatan pengambilan barang
✅ Validasi stok
✅ Pengurangan stok otomatis
✅ Perhitungan subtotal otomatis
✅ Riwayat pengambilan
✅ Laporan dengan filter tanggal dan rusun
✅ Export laporan ke PDF
✅ Export laporan ke Excel (CSV)
✅ Search/pencarian barang dan pengambilan
✅ Pagination
✅ Responsive design (Bootstrap 5)
✅ Notifikasi sukses/error
✅ Konfirmasi sebelum hapus
✅ Data rusun (RABEK, UMEN, TIPAR, ALBO, CBT, KM2)

## VALIDASI YANG SUDAH DITERAPKAN

✅ BR-01: ID barang harus unik
✅ BR-02: Nama barang wajib diisi
✅ BR-03: Foto barang format JPG/PNG/WEBP
✅ BR-04: Harga satuan tidak boleh negatif
✅ BR-05: Stok tidak boleh negatif
✅ BR-06: Pengambil wajib diisi
✅ BR-07: Rusun wajib dipilih
✅ BR-08: Tanggal dan waktu wajib diisi
✅ BR-09: Jumlah pengambilan > 0
✅ BR-10: Validasi stok mencukupi
✅ BR-11: Subtotal dihitung otomatis
✅ BR-12: Stok berkurang otomatis
✅ BR-13: Transaksi masuk riwayat
✅ BR-14: Filter laporan berdasarkan tanggal
✅ BR-15: Filter laporan berdasarkan rusun
✅ BR-16: Filter "Semua Rusun" menampilkan semua data

## CHECKLIST FINAL

### Database
- [ ] Laragon berjalan
- [ ] Database `sistem_inventory_uprs_vi` sudah dibuat
- [ ] Migrasi berhasil (php artisan migrate:fresh)
- [ ] Seeder berhasil (php artisan db:seed)
- [ ] 6 data rusun sudah terisi

### Storage
- [ ] Storage link sudah dibuat (php artisan storage:link)
- [ ] Folder `storage/app/public/items` sudah ada

### Testing
- [ ] Dashboard terbuka tanpa error
- [ ] Bisa tambah barang dengan foto
- [ ] Foto barang tampil di detail
- [ ] Bisa edit barang
- [ ] Bisa hapus barang
- [ ] Bisa buat pengambilan barang
- [ ] Stok berkurang setelah pengambilan
- [ ] Subtotal dihitung otomatis
- [ ] Validasi stok bekerja (tidak bisa ambil > stok)
- [ ] Riwayat pengambilan tampil
- [ ] Laporan bisa ditampilkan
- [ ] Export PDF berfungsi
- [ ] Export Excel berfungsi
- [ ] Search barang berfungsi
- [ ] Search pengambilan berfungsi

## CATATAN PENTING

1. **Database Connection**: Pastikan Laragon SELALU berjalan saat menggunakan aplikasi
2. **Foto Upload**: Max 2MB, format JPG/JPEG/PNG/WEBP
3. **Stok Management**: Stok berkurang otomatis, tidak ada fitur edit/hapus pengambilan
4. **Backup**: Backup database secara berkala melalui phpMyAdmin
5. **Testing**: Selalu test semua fitur setelah setup

## JIKA SEMUA SUDAH BENAR

Sistem siap digunakan untuk:
- Mencatat inventory material Divisi Sarpras
- Tracking pengambilan barang
- Membuat laporan pengambilan material
- Export laporan untuk keperluan dokumentasi

## KONTAK SUPPORT

Jika ada masalah atau pertanyaan, dokumentasikan:
1. Error message yang muncul
2. Langkah yang dilakukan sebelum error
3. Screenshot jika perlu
4. File `.env` configuration (tanpa password)
