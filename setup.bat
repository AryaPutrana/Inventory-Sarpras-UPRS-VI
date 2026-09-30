@echo off
echo ================================================
echo SETUP SISTEM INVENTORY SARPRAS UPRS VI
echo ================================================
echo.

echo [1/6] Menyalin file .env...
if not exist .env (
    copy .env.example .env
    echo File .env berhasil dibuat!
) else (
    echo File .env sudah ada, dilewati.
)
echo.

echo [2/6] Generate Application Key...
php artisan key:generate
echo.

echo [3/6] Membuat Storage Link...
php artisan storage:link
echo.

echo ================================================
echo PENTING: Setup Database
echo ================================================
echo Sebelum melanjutkan, pastikan:
echo 1. MySQL/MariaDB sudah running
echo 2. Database 'sistem_inventory_uprs_vi' sudah dibuat
echo.
echo Cara membuat database:
echo - Buka phpMyAdmin (http://localhost/phpmyadmin)
echo - Klik "New" atau "Baru"
echo - Nama database: sistem_inventory_uprs_vi
echo - Collation: utf8mb4_unicode_ci
echo - Klik "Create"
echo.
echo Atau gunakan MySQL command:
echo CREATE DATABASE sistem_inventory_uprs_vi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
echo.
pause

echo [4/6] Menjalankan Migration...
php artisan migrate
echo.

echo [5/6] Menjalankan Seeder (User + Rusun + Contoh Barang)...
php artisan db:seed --force
echo.

echo [6/6] Clear Cache...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo.

echo ================================================
echo SETUP SELESAI!
echo ================================================
echo.
echo Kredensial login default (wajib diganti setelah login):
echo   Email    : petugas@sarpras.com
echo   Password : password123
echo.
echo Untuk menjalankan aplikasi:
echo   php artisan serve
echo.
echo Kemudian buka browser dan akses:
echo   http://localhost:8000
echo.
echo ================================================
pause
