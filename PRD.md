PRODUCT REQUIREMENT DOCUMENT (PRD)

SISTEM INFORMASI INVENTORY MATERIAL
SARANA DAN PRASARANA UPRS VI

============================================================
A. INFORMASI UMUM
============================================================

Nama Sistem       : Sistem Informasi Inventory Material Sarpras UPRS VI
Nama Singkat      : SI Inventory Sarpras
Jenis Sistem      : Aplikasi Web Internal
Pengguna          : Divisi Sarana dan Prasarana (Sarpras) UPRS VI
Platform           : Web
Status             : Sistem Baru
Sifat Sistem       : Internal Divisi Sarpras
Metode             : Web-Based Information System

============================================================
B. LATAR BELAKANG
============================================================

Divisi Sarana dan Prasarana (Sarpras) UPRS VI memiliki berbagai
material dan barang yang digunakan untuk menunjang kegiatan
operasional, pemeliharaan, dan kebutuhan fasilitas di lingkungan
rumah susun.

Dalam proses pengelolaannya diperlukan pencatatan data material
yang tersedia, seperti ID barang, nama barang, foto barang, harga,
tanggal pencatatan, serta informasi mengenai barang yang diambil
untuk digunakan.

Selain pencatatan data inventory, diperlukan pula pencatatan
pengambilan material yang berisi informasi mengenai siapa yang
mengambil barang, barang yang diambil, jumlah barang, lokasi rusun
yang menggunakan barang, harga satuan, subtotal, serta tanggal dan
waktu pengambilan.

Berdasarkan kebutuhan tersebut, diperlukan sebuah sistem informasi
inventory material berbasis web yang dapat digunakan secara internal
oleh Divisi Sarpras UPRS VI.

Sistem ini dibuat secara terpisah dari sistem atau website UPRS
yang telah dibuat sebelumnya karena penggunaannya dikhususkan untuk
kebutuhan internal Divisi Sarpras.

============================================================
C. TUJUAN SISTEM
============================================================

Sistem ini bertujuan untuk:

1. Membantu Divisi Sarpras dalam melakukan pencatatan data
   inventory material.

2. Menyimpan informasi setiap barang secara terstruktur.

3. Menyimpan foto barang sebagai dokumentasi.

4. Mencatat harga satuan setiap barang.

5. Mencatat jumlah atau stok barang yang tersedia.

6. Mencatat setiap proses pengambilan material.

7. Mencatat nama orang yang mengambil barang.

8. Mencatat lokasi rusun tempat barang digunakan.

9. Mencatat tanggal dan waktu pengambilan barang.

10. Menghitung subtotal nilai barang yang diambil.

11. Mengurangi stok barang secara otomatis ketika terjadi
    pengambilan.

12. Menyediakan riwayat pengambilan barang.

13. Menyediakan laporan berdasarkan rentang tanggal.

14. Menyediakan filter laporan berdasarkan lokasi rusun.

15. Mempermudah pencarian dan monitoring data inventory.

16. Mengurangi proses pencatatan manual.

17. Mempermudah dokumentasi dan pelaporan internal Divisi Sarpras.


============================================================
D. PERMASALAHAN YANG INGIN DISELESAIKAN
============================================================

Beberapa permasalahan yang ingin diselesaikan melalui sistem ini
adalah:

1. Data material belum dikelola dalam satu sistem inventory.

2. Informasi mengenai barang masih perlu dicatat secara manual.

3. Riwayat pengambilan barang sulit ditelusuri apabila tidak
   disimpan secara terstruktur.

4. Informasi mengenai siapa yang mengambil barang perlu dicatat
   dengan lebih rapi.

5. Penggunaan barang berdasarkan lokasi rusun perlu dapat
   diketahui.

6. Pencarian transaksi berdasarkan tanggal membutuhkan proses
   manual apabila tidak tersedia sistem filter.

7. Pembuatan laporan pengambilan material membutuhkan data yang
   tersusun secara otomatis.

8. Dokumentasi foto barang perlu disimpan bersama dengan data
   barang.


============================================================
E. RUANG LINGKUP SISTEM
============================================================

Sistem yang dikembangkan mencakup:

1. Dashboard
2. Manajemen data inventory barang
3. Tambah data barang
4. Edit data barang
5. Hapus data barang
6. Detail barang
7. Upload foto barang
8. Pencatatan stok
9. Pencatatan pengambilan barang
10. Pencatatan nama pengambil
11. Pencatatan lokasi rusun
12. Pencatatan tanggal dan waktu pengambilan
13. Perhitungan subtotal
14. Pengurangan stok otomatis
15. Riwayat pengambilan
16. Pencarian data
17. Laporan pengambilan
18. Filter berdasarkan tanggal
19. Filter berdasarkan rusun
20. Cetak laporan
21. Export laporan PDF
22. Export laporan Excel


============================================================
F. FITUR YANG TIDAK TERMASUK
============================================================

Untuk versi awal, sistem tidak mencakup:

1. Sistem pengaduan penghuni.
2. Data penghuni.
3. Sistem pembayaran.
4. Sistem keuangan.
5. Sistem pembelian atau procurement.
6. Sistem approval berjenjang.
7. Integrasi dengan sistem eksternal.
8. IoT atau sensor gudang.
9. Sistem multi-role yang kompleks.
10. Sistem pemesanan material secara otomatis.


============================================================
G. PENGGUNA SISTEM
============================================================

Berdasarkan kebutuhan Divisi Sarpras, sistem menggunakan satu jenis
pengguna utama, yaitu:

PETUGAS SARPRAS

Petugas Sarpras memiliki akses untuk:

1. Melihat dashboard.
2. Melihat data inventory.
3. Menambahkan data barang.
4. Mengubah data barang.
5. Menghapus data barang.
6. Melihat detail barang.
7. Mengupload foto barang.
8. Mencatat pengambilan barang.
9. Melihat riwayat pengambilan.
10. Membuat laporan.
11. Melakukan filter laporan.
12. Mencetak laporan.
13. Mengexport laporan.


============================================================
H. STRUKTUR MENU SISTEM
============================================================

Struktur menu utama sistem:

SI INVENTORY SARPRAS

├── Dashboard
│
├── Inventory Barang
│   ├── Data Barang
│   ├── Tambah Barang
│   └── Detail Barang
│
├── Pengambilan Barang
│   ├── Pengambilan Baru
│   └── Riwayat Pengambilan
│
├── Laporan
│   └── Laporan Pengambilan
│
└── Pengaturan


============================================================
I. MODUL DASHBOARD
============================================================

Dashboard merupakan halaman utama setelah pengguna masuk ke sistem.

Dashboard digunakan untuk menampilkan ringkasan kondisi inventory
dan aktivitas pengambilan barang.

Informasi yang ditampilkan:

1. Total Jenis Barang
2. Total Pengambilan
3. Total Pengambilan Bulan Ini
4. Total Nilai Pengambilan
5. Daftar Pengambilan Terbaru

Contoh tampilan:

------------------------------------------------------------
SI INVENTORY SARPRAS                         PETUGAS
------------------------------------------------------------

Dashboard

+----------------------+  +----------------------+
| TOTAL BARANG         |  | TOTAL PENGAMBILAN    |
|                      |  |                      |
|       125            |  |        38            |
+----------------------+  +----------------------+

+----------------------+  +----------------------+
| PENGAMBILAN BULAN INI|  | TOTAL NILAI          |
|                      |  |                      |
|        17            |  |   Rp8.750.000        |
+----------------------+  +----------------------+

Pengambilan Terbaru

+----+-------------+-----------+-----------+----------------+
| No | Barang      | Pengambil | Rusun     | Tanggal        |
+----+-------------+-----------+-----------+----------------+
| 1  | Cat Tembok  | Budi      | Rawa Bebek| 29/09/2026     |
| 2  | Lampu LED   | Andi      | Albo      | 29/09/2026     |
| 3  | Kabel NYM   | Rudi      | Umen      | 28/09/2026     |
+----+-------------+-----------+-----------+----------------+


============================================================
J. MODUL INVENTORY BARANG
============================================================

Modul Inventory Barang digunakan untuk menyimpan seluruh data
material yang tersedia di Divisi Sarpras.

Data utama barang:

1. ID Barang
2. Nama Barang
3. Foto Barang
4. Harga Satuan
5. Jumlah/Stok
6. Satuan
7. Tanggal Input
8. Keterangan

------------------------------------------------------------
FIELD DATA BARANG
------------------------------------------------------------

1. ID Barang
   - Tipe       : Text
   - Wajib      : Ya
   - Ketentuan  : Harus unik
   - Contoh     : BRG-0001

2. Nama Barang
   - Tipe       : Text
   - Wajib      : Ya
   - Contoh     : Cat Tembok 5 Kg

3. Foto Barang
   - Tipe       : Upload Image
   - Wajib      : Ya
   - Format     : JPG, JPEG, PNG, WEBP

4. Harga Satuan
   - Tipe       : Numeric
   - Wajib      : Ya
   - Contoh     : Rp125.000

5. Jumlah/Stok
   - Tipe       : Numeric
   - Wajib      : Ya
   - Contoh     : 20

6. Satuan
   - Tipe       : Select/Text
   - Contoh     : pcs, unit, meter, box, liter

7. Tanggal Input
   - Tipe       : Date
   - Wajib      : Ya

8. Keterangan
   - Tipe       : Textarea
   - Wajib      : Tidak


============================================================
K. PREVIEW HALAMAN INVENTORY
============================================================

------------------------------------------------------------
INVENTORY BARANG                 [+ TAMBAH BARANG]
------------------------------------------------------------

[ Cari ID / Nama Barang........................ ]

+----+----------+----------------+-------------+-------+--------+
| No | ID       | Nama Barang    | Harga       | Stok  | Aksi   |
+----+----------+----------------+-------------+-------+--------+
| 1  | BRG-001  | Cat Tembok     | Rp125.000   | 20    | Detail |
| 2  | BRG-002  | Lampu LED      | Rp35.000    | 50    | Detail |
| 3  | BRG-003  | Kabel NYM      | Rp15.000    | 100   | Detail |
+----+----------+----------------+-------------+-------+--------+

Aksi:

[ DETAIL ] [ EDIT ] [ HAPUS ]

Pagination:

< 1 2 3 4 >


============================================================
L. FORM TAMBAH BARANG
============================================================

------------------------------------------------------------
TAMBAH BARANG
------------------------------------------------------------

ID Barang

[ BRG-0001                              ]

Nama Barang

[ Cat Tembok 5 Kg                       ]

Foto Barang

[ PILIH FILE ]

Harga Satuan

[ Rp125.000                              ]

Jumlah / Stok

[ 20                                     ]

Satuan

[ Kaleng                         ▼       ]

Tanggal Input

[ 29/09/2026                            ]

Keterangan

[                                       ]
[                                       ]

[ BATAL ]                    [ SIMPAN ]


============================================================
M. DETAIL BARANG
============================================================

Halaman detail digunakan untuk menampilkan informasi lengkap dari
satu barang.

Contoh:

------------------------------------------------------------
DETAIL BARANG
------------------------------------------------------------

                  [ FOTO BARANG ]

ID Barang
BRG-0001

Nama Barang
Cat Tembok 5 Kg

Harga Satuan
Rp125.000

Stok
20 Kaleng

Tanggal Input
29 September 2026

Keterangan
Material untuk kebutuhan pemeliharaan.

[ EDIT ]       [ PENGAMBILAN BARANG ]


============================================================
N. MODUL PENGAMBILAN BARANG
============================================================

Modul Pengambilan Barang digunakan untuk mencatat material yang
diambil atau digunakan untuk kebutuhan operasional.

Data yang dicatat:

1. Barang
2. ID Barang
3. Pengambil
4. Rusun
5. Jumlah
6. Harga Satuan
7. Subtotal
8. Tanggal dan Waktu Pengambilan
9. Keterangan


============================================================
O. FORM PENGAMBILAN BARANG
============================================================

------------------------------------------------------------
PENGAMBILAN BARANG
------------------------------------------------------------

Barang

[ Cari / Pilih Barang                   ▼ ]

ID Barang

[ BRG-0001 ]

Pengambil

[ Budi                                  ]

Rusun

[ Rawa Bebek                            ▼ ]

Jumlah

[ 2                                     ]

Harga Satuan

Rp125.000

Subtotal

Rp250.000

Tanggal dan Waktu Pengambilan

[ 29/09/2026 09:30                      ]

Keterangan

[ Untuk pemeliharaan gedung             ]
[                                       ]

[ BATAL ]              [ SIMPAN PENGAMBILAN ]


============================================================
P. DAFTAR RUSUN
============================================================

Pilihan rusun yang tersedia pada sistem:

1. Semua Rusun
2. Rawa Bebek
3. Umen
4. Tipar
5. Albo
6. CBT
7. KM2

Kode rusun:

RABEK = Rawa Bebek
UMEN  = Umen
TIPAR = Tipar
ALBO  = Albo
CBT   = CBT
KM2   = KM2


============================================================
Q. PERHITUNGAN SUBTOTAL
============================================================

Subtotal dihitung secara otomatis oleh sistem.

Rumus:

SUBTOTAL = JUMLAH × HARGA SATUAN

Contoh:

Nama Barang    : Cat Tembok
Jumlah         : 3
Harga Satuan   : Rp125.000

Subtotal:

3 × Rp125.000 = Rp375.000

Pengguna tidak perlu memasukkan subtotal secara manual.


============================================================
R. PENGURANGAN STOK
============================================================

Ketika transaksi pengambilan berhasil disimpan, sistem akan
mengurangi stok barang secara otomatis.

Contoh:

Stok awal       : 20
Jumlah diambil  : 3
Stok akhir      : 17

Maka:

STOK AKHIR = STOK AWAL - JUMLAH DIAMBIL


============================================================
S. VALIDASI STOK
============================================================

Sistem harus mencegah pengguna mengambil barang melebihi stok
yang tersedia.

Contoh:

Stok tersedia : 5

Jumlah yang ingin diambil:

10

Maka sistem menampilkan pesan:

"Stok tidak mencukupi. Stok tersedia hanya 5."

Transaksi tidak dapat disimpan sampai jumlah pengambilan sesuai
dengan stok yang tersedia.


============================================================
T. MODUL RIWAYAT PENGAMBILAN
============================================================

Modul ini digunakan untuk melihat seluruh transaksi pengambilan
material yang pernah dilakukan.

------------------------------------------------------------
RIWAYAT PENGAMBILAN
------------------------------------------------------------

[ Cari barang / pengambil..................... ]

+----+----------+-------------+----------+-------+---------------+
| No | ID       | Barang      | Pengambil| Rusun | Tanggal       |
+----+----------+-------------+----------+-------+---------------+
| 1  | BRG-001  | Cat Tembok  | Budi     | RABEK | 29/09/2026    |
| 2  | BRG-002  | Lampu LED   | Andi     | ALBO  | 29/09/2026    |
| 3  | BRG-003  | Kabel NYM   | Rudi     | UMEN  | 28/09/2026    |
+----+----------+-------------+----------+-------+---------------+


============================================================
U. MODUL LAPORAN
============================================================

Modul laporan digunakan untuk menghasilkan laporan pengambilan
material berdasarkan periode dan lokasi rusun.

Filter laporan yang tersedia:

1. Start Date
2. End Date
3. Rusun

Input:

Start Date
[ 01/09/2026 ]

End Date
[ 30/09/2026 ]

Rusun
[ Semua Rusun                         ▼ ]

Tombol:

[ TAMPILKAN LAPORAN ]
[ RESET ]
[ CETAK ]
[ EXPORT PDF ]
[ EXPORT EXCEL ]


============================================================
V. PREVIEW HALAMAN LAPORAN
============================================================

------------------------------------------------------------
LAPORAN PENGAMBILAN MATERIAL
------------------------------------------------------------

Periode

[ 01/09/2026 ] s/d [ 30/09/2026 ]

Rusun

[ Semua Rusun                         ▼ ]

[ TAMPILKAN ]     [ RESET ]

[ CETAK ] [ EXPORT PDF ] [ EXPORT EXCEL ]

------------------------------------------------------------

LAPORAN PENGAMBILAN MATERIAL

Periode : 01 September 2026 - 30 September 2026
Rusun   : Semua Rusun

+----+----------+-------------+----------+----------------+
| No | ID Barang| Nama Barang | Pengambil| Tanggal Ambil  |
+----+----------+-------------+----------+----------------+
| 1  | BRG-001  | Cat Tembok  | Budi     | 29/09/2026     |
| 2  | BRG-002  | Lampu LED   | Andi     | 29/09/2026     |
| 3  | BRG-003  | Kabel NYM    | Rudi     | 28/09/2026     |
+----+----------+-------------+----------+----------------+

+-------+----------+-------------+---------------+
| Rusun | Jumlah   | Harga Satuan| Subtotal      |
+-------+----------+-------------+---------------+
| RABEK | 2        | Rp125.000   | Rp250.000     |
| ALBO  | 5        | Rp35.000    | Rp175.000     |
+-------+----------+-------------+---------------+


============================================================
W. ISI LAPORAN
============================================================

Sesuai kebutuhan Divisi Sarpras, laporan memiliki kolom:

1. No
2. ID Barang
3. Nama Barang
4. Pengambil
5. Tanggal Ambil
6. Rusun
7. Harga Satuan
8. Subtotal

Untuk kebutuhan inventory yang lebih lengkap, ditambahkan:

9. Jumlah
10. Satuan

Sehingga format laporan yang direkomendasikan adalah:

+----+----------+-------------+----------+----------------+
| No | ID Barang| Nama Barang | Pengambil| Tanggal Ambil  |
+----+----------+-------------+----------+----------------+
| 1  | BRG-001  | Cat Tembok  | Budi     | 29/09/2026     |
+----+----------+-------------+----------+----------------+

+-------+--------+-------------+---------------+
| Rusun | Jumlah | Harga Satuan| Subtotal      |
+-------+--------+-------------+---------------+
| RABEK | 2      | Rp125.000   | Rp250.000     |
+-------+--------+-------------+---------------+


============================================================
X. TOTAL PADA LAPORAN
============================================================

Pada bagian bawah laporan sistem menampilkan:

Total Transaksi
: 25

Total Barang Diambil
: 87

Total Nilai Pengambilan
: Rp8.750.000

Contoh:

------------------------------------------------------------
RINGKASAN LAPORAN

Total Transaksi       : 25 transaksi
Total Barang Diambil  : 87 barang
Total Nilai           : Rp8.750.000
------------------------------------------------------------


============================================================
Y. FILTER LAPORAN BERDASARKAN RUSUN
============================================================

Sistem dapat menampilkan laporan berdasarkan:

1. Semua Rusun
2. Rawa Bebek
3. Umen
4. Tipar
5. Albo
6. CBT
7. KM2

Contoh:

Start Date : 01/09/2026
End Date   : 30/09/2026
Rusun      : Rawa Bebek

Maka sistem hanya menampilkan transaksi pengambilan barang yang
digunakan untuk Rawa Bebek pada periode tersebut.


============================================================
Z. ALUR SISTEM
============================================================

1. ALUR MENAMBAHKAN BARANG

Petugas
   ↓
Menu Inventory
   ↓
Tambah Barang
   ↓
Mengisi ID Barang
   ↓
Mengisi Nama Barang
   ↓
Upload Foto
   ↓
Mengisi Harga
   ↓
Mengisi Jumlah/Stok
   ↓
Memilih Satuan
   ↓
Mengisi Tanggal
   ↓
Mengisi Keterangan
   ↓
Simpan
   ↓
Data masuk Database
   ↓
Barang muncul pada Inventory


2. ALUR PENGAMBILAN BARANG

Petugas
   ↓
Menu Pengambilan
   ↓
Pilih Barang
   ↓
Sistem menampilkan harga
   ↓
Isi Nama Pengambil
   ↓
Pilih Rusun
   ↓
Isi Jumlah
   ↓
Sistem menghitung Subtotal
   ↓
Sistem mengecek Stok
   ↓
Stok mencukupi?
   ├── Tidak
   │    ↓
   │  Tampilkan pesan error
   │
   └── Ya
        ↓
      Simpan Transaksi
        ↓
      Stok Berkurang
        ↓
      Transaksi Tersimpan
        ↓
      Masuk Riwayat Pengambilan


3. ALUR MEMBUAT LAPORAN

Petugas
   ↓
Menu Laporan
   ↓
Pilih Start Date
   ↓
Pilih End Date
   ↓
Pilih Rusun
   ↓
Klik Tampilkan
   ↓
Sistem mengambil data transaksi
   ↓
Data ditampilkan
   ↓
Petugas dapat:
   ├── Melihat
   ├── Mencetak
   ├── Export PDF
   └── Export Excel


============================================================
AA. ATURAN BISNIS
============================================================

BR-01
ID barang harus unik.

BR-02
Nama barang wajib diisi.

BR-03
Foto barang harus menggunakan format file gambar.

BR-04
Harga satuan harus berupa angka dan tidak boleh bernilai negatif.

BR-05
Stok harus berupa angka dan tidak boleh bernilai negatif.

BR-06
Pengambil wajib diisi ketika melakukan transaksi pengambilan.

BR-07
Rusun wajib dipilih.

BR-08
Tanggal dan waktu pengambilan wajib diisi.

BR-09
Jumlah pengambilan wajib lebih besar dari 0.

BR-10
Jumlah pengambilan tidak boleh melebihi stok tersedia.

BR-11
Subtotal dihitung otomatis oleh sistem.

BR-12
Setelah transaksi berhasil, stok barang berkurang secara otomatis.

BR-13
Data transaksi yang sudah tersimpan masuk ke riwayat pengambilan.

BR-14
Laporan dapat difilter berdasarkan start date dan end date.

BR-15
Laporan dapat difilter berdasarkan rusun.

BR-16
Jika filter rusun dipilih "Semua", sistem menampilkan seluruh
transaksi pada periode yang dipilih.


============================================================
AB. STRUKTUR DATABASE
============================================================

Sistem menggunakan minimal tiga tabel utama:

1. items
2. withdrawals
3. rusun


------------------------------------------------------------
1. TABEL ITEMS
------------------------------------------------------------

Digunakan untuk menyimpan data master barang.

Field:

id
item_code
name
photo
unit_price
stock
unit
description
created_at
updated_at


Contoh data:

id          : 1
item_code   : BRG-001
name        : Cat Tembok 5 Kg
photo       : cat-tembok.jpg
unit_price  : 125000
stock       : 20
unit        : Kaleng
description : Material pemeliharaan
created_at  : 2026-09-29
updated_at  : 2026-09-29


------------------------------------------------------------
2. TABEL WITHDRAWALS
------------------------------------------------------------

Digunakan untuk menyimpan transaksi pengambilan barang.

Field:

id
item_id
taken_by
rusun_id
quantity
unit_price
subtotal
taken_at
description
created_at
updated_at


Contoh:

id          : 1
item_id     : 1
taken_by    : Budi
rusun_id    : 1
quantity    : 2
unit_price  : 125000
subtotal    : 250000
taken_at    : 2026-09-29 09:30
description : Untuk pemeliharaan gedung


------------------------------------------------------------
3. TABEL RUSUN
------------------------------------------------------------

Digunakan untuk menyimpan daftar rusun.

Field:

id
code
name
created_at
updated_at


Contoh:

1 | RABEK | Rawa Bebek
2 | UMEN  | Umen
3 | TIPAR | Tipar
4 | ALBO  | Albo
5 | CBT   | CBT
6 | KM2   | KM2


============================================================
AC. RELASI DATABASE
============================================================

Relasi antar tabel:

ITEMS
  |
  | 1
  |
  |-------------------<
                       |
                       |
                  WITHDRAWALS
                       |
                       |
                       >-----------------|
                                         |
                                         |
                                       RUSUN

Penjelasan:

1. Satu barang dapat memiliki banyak transaksi pengambilan.

2. Satu rusun dapat memiliki banyak transaksi pengambilan.

3. Setiap transaksi pengambilan hanya berhubungan dengan satu
   barang.

4. Setiap transaksi pengambilan memiliki satu lokasi rusun.


============================================================
AD. STRUKTUR LARAVEL
============================================================

Struktur project yang disarankan:

app/
│
├── Http/
│   └── Controllers/
│       ├── DashboardController.php
│       ├── ItemController.php
│       ├── WithdrawalController.php
│       └── ReportController.php
│
├── Models/
│   ├── Item.php
│   ├── Withdrawal.php
│   └── Rusun.php
│
database/
│
├── migrations/
│   ├── create_items_table.php
│   ├── create_withdrawals_table.php
│   └── create_rusun_table.php
│
└── seeders/
    └── RusunSeeder.php
│
resources/
│
└── views/
    │
    ├── layouts/
    │
    ├── dashboard/
    │   └── index.blade.php
    │
    ├── items/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   └── show.blade.php
    │
    ├── withdrawals/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── show.blade.php
    │
    └── reports/
        └── index.blade.php


============================================================
AE. ROUTING
============================================================

Route yang disarankan:

GET     /dashboard

GET     /items
GET     /items/create
POST    /items
GET     /items/{id}
GET     /items/{id}/edit
PUT     /items/{id}
DELETE  /items/{id}

GET     /withdrawals
GET     /withdrawals/create
POST    /withdrawals
GET     /withdrawals/{id}

GET     /reports
GET     /reports/export/pdf
GET     /reports/export/excel


============================================================
AF. KEBUTUHAN FUNGSIONAL
============================================================

FR-01
Sistem dapat menampilkan dashboard.

FR-02
Sistem dapat menampilkan jumlah barang.

FR-03
Sistem dapat menambahkan data barang.

FR-04
Sistem dapat mengubah data barang.

FR-05
Sistem dapat menghapus data barang.

FR-06
Sistem dapat menampilkan detail barang.

FR-07
Sistem dapat mengupload foto barang.

FR-08
Sistem dapat mencatat harga barang.

FR-09
Sistem dapat mencatat jumlah stok barang.

FR-10
Sistem dapat mencatat satuan barang.

FR-11
Sistem dapat mencatat transaksi pengambilan.

FR-12
Sistem dapat mencatat nama pengambil.

FR-13
Sistem dapat memilih lokasi rusun.

FR-14
Sistem dapat mencatat tanggal dan waktu pengambilan.

FR-15
Sistem dapat menghitung subtotal secara otomatis.

FR-16
Sistem dapat mengurangi stok secara otomatis.

FR-17
Sistem dapat mencegah pengambilan melebihi stok.

FR-18
Sistem dapat menampilkan riwayat pengambilan.

FR-19
Sistem dapat mencari barang.

FR-20
Sistem dapat mencari transaksi.

FR-21
Sistem dapat membuat laporan.

FR-22
Sistem dapat memfilter laporan berdasarkan tanggal.

FR-23
Sistem dapat memfilter laporan berdasarkan rusun.

FR-24
Sistem dapat mencetak laporan.

FR-25
Sistem dapat mengexport laporan ke PDF.

FR-26
Sistem dapat mengexport laporan ke Excel.


============================================================
AG. KEBUTUHAN NON-FUNGSIONAL
============================================================

1. PERFORMANCE

Sistem harus dapat digunakan dengan cepat untuk jumlah data
inventory dan transaksi yang normal.

2. SECURITY

Sistem harus memiliki:

- Validasi input.
- Proteksi CSRF.
- Validasi upload file.
- Pembatasan ekstensi file.
- Password menggunakan hashing.
- Session management.

3. USABILITY

Sistem harus memiliki tampilan yang sederhana dan mudah dipahami
oleh petugas Sarpras.

4. RESPONSIVE

Sistem dapat digunakan melalui:

- Laptop
- Desktop
- Tablet
- Smartphone

5. MAINTAINABILITY

Sistem dibuat menggunakan struktur Laravel MVC sehingga dapat
dikembangkan dan dipelihara dengan mudah.

6. DATABASE

Database menggunakan MySQL/MariaDB.


============================================================
AH. TEKNOLOGI YANG DIGUNAKAN
============================================================

Backend:

Laravel
PHP

Frontend:

Blade
Bootstrap 5
HTML
CSS
JavaScript

Database:

MySQL / MariaDB

Development Environment:

Laragon
Visual Studio Code
phpMyAdmin

Version Control:

Git
GitHub


============================================================
AI. DESAIN ANTARMUKA
============================================================

Desain antarmuka menggunakan konsep dashboard admin sederhana.

Struktur:

------------------------------------------------------------
| LOGO / NAMA SISTEM                     PETUGAS ▼         |
------------------------------------------------------------
| SIDEBAR           | CONTENT                            |
|                   |                                    |
| Dashboard         |                                    |
| Inventory         |                                    |
| Pengambilan       |                                    |
| Laporan           |                                    |
|                   |                                    |
------------------------------------------------------------

Sidebar:

🏠 Dashboard
📦 Inventory Barang
📤 Pengambilan Barang
📊 Laporan


============================================================
AJ. PREVIEW KESELURUHAN SISTEM
============================================================

+----------------------------------------------------------------+
| SI INVENTORY SARPRAS                           PETUGAS          |
+-------------------+--------------------------------------------+
|                   |                                            |
| Dashboard         |  DASHBOARD                                 |
|                   |                                            |
| Inventory Barang  |  +-------------+  +-------------+          |
|                   |  | Total Barang|  | Pengambilan |          |
| Pengambilan       |  |     125     |  |      38     |          |
|                   |  +-------------+  +-------------+          |
| Laporan           |                                            |
|                   |  +-------------+  +-------------+          |
|                   |  | Bulan Ini   |  | Total Nilai |          |
|                   |  |     17      |  | Rp8.750.000 |          |
|                   |  +-------------+  +-------------+          |
|                   |                                            |
|                   | Pengambilan Terbaru                        |
|                   |                                            |
|                   | +----------------------------------------+ |
|                   | | Barang | Pengambil | Rusun | Tanggal   | |
|                   | +----------------------------------------+ |
|                   | | Cat    | Budi      | RABEK | 29/09/26  | |
|                   | | Lampu  | Andi      | ALBO  | 29/09/26  | |
|                   | +----------------------------------------+ |
+-------------------+--------------------------------------------+


============================================================
AK. PREVIEW INVENTORY
============================================================

+----------------------------------------------------------------+
| INVENTORY BARANG                         [+ TAMBAH BARANG]     |
+----------------------------------------------------------------+
|                                                                |
| [ 🔍 Cari ID / Nama Barang......................... ]          |
|                                                                |
| +----+----------+----------------+-------------+------+-------+ |
| | No | ID       | Nama Barang    | Harga       | Stok | Aksi  | |
| +----+----------+----------------+-------------+------+-------+ |
| | 1  | BRG-001  | Cat Tembok     | Rp125.000   | 20   | Detail| |
| | 2  | BRG-002  | Lampu LED      | Rp35.000    | 50   | Detail| |
| | 3  | BRG-003  | Kabel NYM      | Rp15.000    | 100  | Detail| |
| +----+----------+----------------+-------------+------+-------+ |
|                                                                |
|                     < 1 2 3 4 >                                |
+----------------------------------------------------------------+


============================================================
AL. PREVIEW PENGAMBILAN BARANG
============================================================

+---------------------------------------------------------------+
|                    PENGAMBILAN BARANG                         |
+---------------------------------------------------------------+
|                                                               |
| Barang                                                        |
| [ Cat Tembok 5 Kg                              ▼ ]            |
|                                                               |
| ID Barang                                                     |
| BRG-001                                                       |
|                                                               |
| Pengambil                                                     |
| [ Budi                                        ]               |
|                                                               |
| Rusun                                                         |
| [ Rawa Bebek                                  ▼ ]             |
|                                                               |
| Jumlah                                                        |
| [ 2                                           ]               |
|                                                               |
| Harga Satuan                                                  |
| Rp125.000                                                     |
|                                                               |
| Subtotal                                                      |
| Rp250.000                                                     |
|                                                               |
| Tanggal dan Waktu                                             |
| [ 29/09/2026 09:30                             ]              |
|                                                               |
| Keterangan                                                    |
| [ Untuk pemeliharaan gedung                    ]              |
|                                                               |
|               [ BATAL ]    [ SIMPAN ]                         |
+---------------------------------------------------------------+


============================================================
AM. PREVIEW LAPORAN
============================================================

+----------------------------------------------------------------+
|                       LAPORAN INVENTORY                        |
+----------------------------------------------------------------+
|                                                                |
| Start Date                                                     |
| [ 01/09/2026 ]                                                 |
|                                                                |
| End Date                                                       |
| [ 30/09/2026 ]                                                 |
|                                                                |
| Rusun                                                          |
| [ Semua Rusun                                    ▼ ]           |
|                                                                |
| [ TAMPILKAN ] [ RESET ] [ CETAK ] [ PDF ] [ EXCEL ]            |
|                                                                |
+----------------------------------------------------------------+
|                                                                |
| LAPORAN PENGAMBILAN MATERIAL                                   |
| Periode : 01 September 2026 - 30 September 2026               |
| Rusun   : Semua Rusun                                          |
|                                                                |
| +----+----------+-------------+----------+------------------+ |
| | No | ID Barang| Nama Barang | Pengambil| Tanggal Ambil    | |
| +----+----------+-------------+----------+------------------+ |
| | 1  | BRG-001  | Cat Tembok  | Budi     | 29/09/2026       | |
| | 2  | BRG-002  | Lampu LED   | Andi     | 29/09/2026       | |
| | 3  | BRG-003  | Kabel NYM    | Rudi     | 28/09/2026       | |
| +----+----------+-------------+----------+------------------+ |
|                                                                |
| +-------+--------+-------------+---------------+               |
| | Rusun | Jumlah | Harga       | Subtotal      |               |
| +-------+--------+-------------+---------------+               |
| | RABEK | 2      | Rp125.000   | Rp250.000     |               |
| | ALBO  | 5      | Rp35.000    | Rp175.000     |               |
| +-------+--------+-------------+---------------+               |
|                                                                |
| Total Transaksi      : 25 transaksi                            |
| Total Barang Diambil : 87 barang                               |
| Total Nilai          : Rp8.750.000                             |
+----------------------------------------------------------------+


============================================================
AN. PREVIEW LAPORAN CETAK
============================================================

============================================================

              UNIT PENGELOLA RUMAH SUSUN VI
                    DIVISI SARANA PRASARANA

                LAPORAN PENGAMBILAN MATERIAL

Periode : 01 September 2026 - 30 September 2026
Rusun   : Semua Rusun

============================================================

+----+----------+-------------+----------+----------+-----------+
| No | ID Barang| Nama Barang | Pengambil| Rusun    | Tgl Ambil |
+----+----------+-------------+----------+----------+-----------+
| 1  | BRG-001  | Cat Tembok  | Budi     | RABEK    | 29/09/26  |
| 2  | BRG-002  | Lampu LED   | Andi     | ALBO     | 29/09/26  |
+----+----------+-------------+----------+----------+-----------+

+--------+-------------+---------------+
| Jumlah | Harga Satuan| Subtotal      |
+--------+-------------+---------------+
| 2      | Rp125.000   | Rp250.000     |
| 5      | Rp35.000    | Rp175.000     |
+--------+-------------+---------------+

Total Transaksi : 25
Total Nilai     : Rp8.750.000

Dibuat pada:
30 September 2026


Petugas Sarpras,


(________________________)


============================================================
AO. TAHAPAN PENGEMBANGAN
============================================================

TAHAP 1 - DATABASE

1. Membuat database.
2. Membuat tabel items.
3. Membuat tabel withdrawals.
4. Membuat tabel rusun.
5. Membuat relasi antar tabel.
6. Membuat seeder data rusun.


TAHAP 2 - INVENTORY

1. Membuat halaman inventory.
2. Membuat fitur tambah barang.
3. Membuat fitur edit barang.
4. Membuat fitur hapus barang.
5. Membuat detail barang.
6. Membuat upload foto.
7. Membuat pencarian barang.
8. Membuat pagination.


TAHAP 3 - PENGAMBILAN

1. Membuat form pengambilan.
2. Membuat pilihan barang.
3. Membuat pilihan rusun.
4. Membuat input pengambil.
5. Membuat input jumlah.
6. Mengambil harga barang secara otomatis.
7. Menghitung subtotal.
8. Validasi stok.
9. Mengurangi stok.
10. Menyimpan transaksi.


TAHAP 4 - LAPORAN

1. Membuat halaman laporan.
2. Filter Start Date.
3. Filter End Date.
4. Filter Rusun.
5. Menampilkan data transaksi.
6. Menampilkan total transaksi.
7. Menampilkan total barang.
8. Menampilkan total nilai.


TAHAP 5 - OUTPUT

1. Cetak laporan.
2. Export PDF.
3. Export Excel.


TAHAP 6 - PENYEMPURNAAN

1. Dashboard.
2. Search.
3. Pagination.
4. Validasi form.
5. Responsive design.
6. Notifikasi berhasil/gagal.
7. Konfirmasi sebelum hapus.
8. Perbaikan tampilan.


============================================================
AP. MVP / FITUR MINIMAL VERSI PERTAMA
============================================================

Apabila sistem ingin dibuat terlebih dahulu dalam versi sederhana,
fitur minimum yang harus tersedia adalah:

1. Dashboard
2. Inventory Barang
3. Tambah Barang
4. Edit Barang
5. Hapus Barang
6. Detail Barang
7. Upload Foto
8. Stok
9. Pengambilan Barang
10. Pengambil
11. Rusun
12. Tanggal Pengambilan
13. Jumlah
14. Harga Satuan
15. Subtotal
16. Riwayat Pengambilan
17. Laporan
18. Filter Start Date
19. Filter End Date
20. Filter Rusun
21. Cetak Laporan


============================================================
AQ. FITUR TAMBAHAN YANG DIREKOMENDASIKAN
============================================================

Fitur berikut dapat dikembangkan setelah versi utama selesai:

1. Export Excel.
2. Export PDF.
3. Dashboard grafik penggunaan material.
4. Grafik penggunaan berdasarkan rusun.
5. Notifikasi stok rendah.
6. Filter berdasarkan nama barang.
7. Filter berdasarkan pengambil.
8. Riwayat perubahan stok.
9. Barcode atau QR Code barang.
10. Pencatatan stok masuk.
11. Pencatatan stok keluar.
12. Laporan stok.
13. Laporan penggunaan per rusun.


============================================================
AR. KESIMPULAN PRODUK
============================================================

Sistem Informasi Inventory Material Sarana dan Prasarana UPRS VI
merupakan aplikasi web internal yang digunakan untuk membantu
Divisi Sarpras dalam melakukan pengelolaan data material.

Sistem memiliki tiga proses utama, yaitu:

1. INVENTORY

Digunakan untuk mencatat dan mengelola data barang/material yang
tersedia, meliputi ID barang, nama barang, foto, harga, stok,
satuan, tanggal, dan keterangan.

2. PENGAMBILAN BARANG

Digunakan untuk mencatat barang yang diambil, siapa yang mengambil,
lokasi rusun, jumlah barang, harga satuan, subtotal, serta tanggal
dan waktu pengambilan.

3. LAPORAN

Digunakan untuk menampilkan riwayat pengambilan barang berdasarkan
rentang tanggal dan lokasi rusun.

Sistem ini dibuat secara terpisah dari sistem website UPRS yang
telah dibuat sebelumnya karena sistem ini dikhususkan untuk
kebutuhan internal Divisi Sarpras.

Dengan adanya sistem ini, data inventory dan transaksi pengambilan
material dapat dikelola secara lebih terstruktur, mudah dicari,
mudah dimonitor, serta dapat digunakan untuk kebutuhan pelaporan
internal Divisi Sarpras.


============================================================
AS. RINGKASAN KEBUTUHAN DARI DIVISI SARPRAS
============================================================

Berdasarkan kebutuhan yang disampaikan oleh Divisi Sarpras:

INPUT INVENTORY:

- Nama Barang
- ID Barang
- Foto
- Harga
- Tanggal

INPUT PENGAMBILAN:

- Barang
- Pengambil
- Rusun
- Jumlah
- Harga Satuan
- Tanggal dan Waktu Pengambilan
- Keterangan

RUSUN:

- Rawa Bebek
- Umen
- Tipar
- Albo
- CBT
- KM2

FILTER LAPORAN:

- Start Date
- End Date
- Rusun

ISI LAPORAN:

- ID Barang
- Nama Barang
- Pengambil
- Tanggal Ambil
- Rusun
- Harga Satuan
- Subtotal
- Jumlah (direkomendasikan)

PENGGUNA:

- 1 orang/petugas internal yang membuat laporan

STATUS SISTEM:

- Terpisah dari website/sistem sebelumnya
- Khusus internal Divisi Sarpras


============================================================
AT. JUDUL PROYEK
============================================================

Judul yang direkomendasikan:

"Sistem Informasi Inventory Material Sarana dan Prasarana
UPRS VI Berbasis Web"

Alternatif judul yang lebih formal:

"Rancang Bangun Sistem Informasi Inventory Material Sarana dan
Prasarana Berbasis Web pada Unit Pengelola Rumah Susun VI"

Alternatif lainnya:

"Perancangan Sistem Informasi Pengelolaan Inventory dan
Pengambilan Material pada Divisi Sarana dan Prasarana UPRS VI"


============================================================
AU. CATATAN PENGEMBANGAN
============================================================

1. Sistem tidak menggunakan role yang kompleks karena berdasarkan
   kebutuhan saat ini hanya terdapat satu pengguna/petugas yang
   membuat laporan.

2. Sistem dibuat terpisah dari sistem sebelumnya.

3. Field jumlah/stok ditambahkan karena sistem disebut sebagai
   inventory dan terdapat kebutuhan subtotal.

4. Subtotal dihitung otomatis berdasarkan jumlah dikalikan harga
   satuan.

5. Stok tidak boleh menjadi negatif.

6. Laporan dapat difilter berdasarkan tanggal dan rusun.

7. Data yang ditampilkan pada laporan harus sesuai dengan
   transaksi pengambilan yang tersimpan di database.

8. Foto barang digunakan sebagai dokumentasi inventory.

9. Sistem dapat dikembangkan lebih lanjut apabila Divisi Sarpras
   membutuhkan fitur tambahan.

10. Untuk tahap awal, fokus pengembangan adalah Inventory,
    Pengambilan Barang, dan Laporan.


============================================================
AV. END OF PRODUCT REQUIREMENT DOCUMENT
============================================================