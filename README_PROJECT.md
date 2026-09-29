# 📦 SISTEM INVENTORY MATERIAL SARPRAS UPRS VI

![Laravel](https://img.shields.io/badge/Laravel-10.50-red)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple)
![Status](https://img.shields.io/badge/Status-Ready-green)

Sistem Informasi Inventory Material untuk Divisi Sarana dan Prasarana Unit Pengelola Rumah Susun VI.

---

## 🎯 Tentang Sistem

Sistem ini dibuat untuk membantu Divisi Sarpras dalam:
- 📋 Mengelola data inventory material
- 📤 Mencatat pengambilan barang
- 📊 Membuat laporan pengambilan material
- 📈 Monitoring stok barang
- 📄 Export laporan (PDF & Excel)

---

## ✨ Fitur Utama

### 1. Dashboard
- Total jenis barang
- Total pengambilan
- Pengambilan bulan ini
- Total nilai pengambilan
- Riwayat pengambilan terbaru

### 2. Inventory Barang
- Tambah, Edit, Hapus barang
- Upload foto barang
- Detail barang dengan foto
- Search barang
- Pagination
- Badge stok berwarna

### 3. Pengambilan Barang
- Form pengambilan dengan validasi
- Auto-calculate subtotal
- Pengurangan stok otomatis
- Validasi stok mencukupi
- Riwayat lengkap
- Search pengambilan

### 4. Laporan
- Filter berdasarkan tanggal
- Filter berdasarkan rusun
- Ringkasan (total transaksi, qty, nilai)
- Export ke PDF
- Export ke Excel (CSV)
- Print-friendly

---

## 🛠️ Teknologi

- **Framework**: Laravel 10.50
- **PHP**: 8.1+
- **Database**: MySQL/MariaDB
- **Frontend**: Bootstrap 5, jQuery, Bootstrap Icons
- **PDF**: DomPDF (barryvdh/laravel-dompdf)

---

## 📥 Instalasi

### Prasyarat
- Laragon (atau XAMPP/WAMP)
- PHP 8.1+
- MySQL/MariaDB
- Composer

### Langkah Instalasi

1. **Clone/Download Project**
   ```bash
   cd C:\Laravel\Sistem_Inventory_UPRS_VI
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Setup Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database di `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sistem_inventory_uprs_vi
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Buat Database**
   ```sql
   CREATE DATABASE sistem_inventory_uprs_vi;
   ```

6. **Jalankan Migrasi & Seeder**
   ```bash
   php artisan migrate:fresh
   php artisan db:seed
   ```

7. **Setup Storage**
   ```bash
   php artisan storage:link
   ```

8. **Jalankan Server**
   ```bash
   php artisan serve
   ```

9. **Akses Aplikasi**
   ```
   http://127.0.0.1:8000
   ```

---

## 📖 Dokumentasi

### File Dokumentasi
- `PRD.md` - Product Requirement Document lengkap
- `QUICK_START.txt` - Panduan cepat untuk memulai
- `PANDUAN_INSTALASI_LENGKAP.md` - Panduan instalasi detail
- `VERIFIKASI_SISTEM.md` - Checklist lengkap sistem

### Data Rusun (Master Data)
Setelah seeder berjalan, akan ada 6 data rusun:
1. RABEK - Rawa Bebek
2. UMEN - Umen
3. TIPAR - Tipar
4. ALBO - Albo
5. CBT - CBT
6. KM2 - KM2

---

## 🗂️ Struktur Database

### Tabel Items (Inventory Barang)
- id, item_code (unique), name, photo, unit_price, stock, unit, description, timestamps

### Tabel Withdrawals (Pengambilan)
- id, item_id, taken_by, rusun_id, quantity, unit_price, subtotal, taken_at, description, timestamps

### Tabel Rusun (Master Rusun)
- id, code (unique), name, timestamps

---

## 🔒 Business Rules

- BR-01: ID barang harus unik
- BR-02: Nama barang wajib diisi
- BR-03: Foto format JPG/PNG/WEBP
- BR-04: Harga tidak boleh negatif
- BR-05: Stok tidak boleh negatif
- BR-06: Pengambil wajib diisi
- BR-07: Rusun wajib dipilih
- BR-08: Tanggal wajib diisi
- BR-09: Jumlah pengambilan > 0
- BR-10: Tidak boleh ambil melebihi stok
- BR-11: Subtotal dihitung otomatis
- BR-12: Stok berkurang otomatis
- BR-13: Transaksi masuk riwayat
- BR-14-16: Filter laporan (tanggal & rusun)

---

## 🧪 Testing

### Test Case 1: Tambah Barang
1. Klik "Inventory Barang" → "Tambah Barang"
2. Isi form lengkap dengan foto
3. Simpan
4. Cek barang muncul di list

### Test Case 2: Pengambilan Barang
1. Klik "Pengambilan Barang" → "Pengambilan Baru"
2. Pilih barang (harga auto-fill)
3. Isi jumlah (subtotal auto-calculate)
4. Simpan
5. Cek stok berkurang

### Test Case 3: Laporan
1. Klik "Laporan"
2. Pilih periode dan rusun
3. Tampilkan laporan
4. Coba export PDF & Excel

---

## 🚨 Troubleshooting

### Error: Connection Refused
**Penyebab**: MySQL belum berjalan  
**Solusi**: Start Laragon/XAMPP

### Error: Database doesn't exist
**Penyebab**: Database belum dibuat  
**Solusi**: `CREATE DATABASE sistem_inventory_uprs_vi;`

### Error: Table not found
**Penyebab**: Migrasi belum dijalankan  
**Solusi**: `php artisan migrate:fresh && php artisan db:seed`

### Foto tidak muncul
**Penyebab**: Storage link belum dibuat  
**Solusi**: `php artisan storage:link`

---

## 📁 Struktur Folder

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
│   └── seeders/
├── resources/views/
│   ├── layouts/
│   ├── dashboard/
│   ├── items/
│   ├── withdrawals/
│   └── reports/
├── routes/
│   └── web.php
├── storage/
│   └── app/public/items/  (foto barang)
└── public/
    └── storage/  (symlink)
```

---

## 🎨 Screenshots

### Dashboard
Menampilkan statistik inventory dan pengambilan terbaru.

### Inventory Barang
CRUD lengkap untuk mengelola barang dengan foto.

### Pengambilan Barang
Form pengambilan dengan auto-calculate dan validasi stok.

### Laporan
Laporan lengkap dengan filter dan export PDF/Excel.

---

## 👨‍💻 Developer Notes

### Validation
- Semua input tervalidasi (client & server-side)
- File upload max 2MB
- Stok validation sebelum pengambilan

### Security
- CSRF protection enabled
- SQL injection protected (Eloquent ORM)
- XSS protection (Blade escaping)
- File type validation

### Performance
- Eager loading relationships
- Pagination (10 items/page)
- Database indexing (unique constraints)
- Database transactions

---

## 📝 Changelog

### Version 1.0.0 (29 September 2026)
- ✅ Initial release
- ✅ CRUD Inventory Barang
- ✅ Pengambilan Barang
- ✅ Laporan dengan filter
- ✅ Export PDF & Excel
- ✅ Search & Pagination
- ✅ Responsive design

---

## 📄 License

Sistem ini dibuat untuk keperluan internal Divisi Sarana dan Prasarana UPRS VI.

---

## 🤝 Support

Untuk bantuan dan dokumentasi lengkap, lihat:
- `QUICK_START.txt` - Panduan cepat
- `PANDUAN_INSTALASI_LENGKAP.md` - Panduan detail
- `VERIFIKASI_SISTEM.md` - Checklist sistem
- `PRD.md` - Requirement lengkap

---

## ✅ Status Proyek

**🎉 SISTEM READY FOR PRODUCTION 🎉**

- ✅ Database schema complete
- ✅ All controllers implemented
- ✅ All views created
- ✅ Business rules validated
- ✅ Security implemented
- ✅ Documentation complete
- ✅ Testing scenarios ready

**Tanpa error • Sesuai PRD • Siap pakai**

---

**Developed with ❤️ for UPRS VI Sarana Prasarana Division**
