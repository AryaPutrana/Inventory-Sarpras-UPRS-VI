@echo off
chcp 65001 >nul
color 0A
cls

echo.
echo ╔════════════════════════════════════════════════════════════════╗
echo ║   SISTEM INFORMASI INVENTORY MATERIAL SARPRAS UPRS VI         ║
echo ║   Setup Wizard                                                 ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.
echo Tanggal: 29 September 2026
echo.

:CHECK_MYSQL
echo [STEP 1/7] Memeriksa koneksi MySQL...
echo.

php -r "new PDO('mysql:host=127.0.0.1;port=3306','root','');" >nul 2>&1
if errorlevel 1 (
    color 0C
    echo ❌ MYSQL BELUM RUNNING!
    echo.
    echo ┌────────────────────────────────────────────────────────────┐
    echo │  PENTING: MySQL/MariaDB harus running terlebih dahulu!    │
    echo └────────────────────────────────────────────────────────────┘
    echo.
    echo Silakan start MySQL menggunakan:
    echo   • Laragon: Klik "Start All"
    echo   • XAMPP: Start MySQL di Control Panel
    echo   • WAMP: Start All Services
    echo.
    echo Setelah MySQL running, jalankan script ini lagi.
    echo.
    pause
    exit /b 1
)

color 0A
echo ✅ MySQL sudah running!
echo.

:CHECK_DATABASE
echo [STEP 2/7] Memeriksa database...
echo.

mysql -u root -e "USE sistem_inventory_uprs_vi;" >nul 2>&1
if errorlevel 1 (
    echo ⚠️  Database belum ada, membuat database...
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS sistem_inventory_uprs_vi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1
    if errorlevel 1 (
        color 0C
        echo ❌ Gagal membuat database!
        echo.
        echo Silakan buat database manual:
        echo 1. Buka phpMyAdmin: http://localhost/phpmyadmin
        echo 2. Klik "New" atau "Databases"
        echo 3. Nama: sistem_inventory_uprs_vi
        echo 4. Collation: utf8mb4_unicode_ci
        echo 5. Klik Create
        echo.
        pause
        exit /b 1
    )
    echo ✅ Database berhasil dibuat!
) else (
    echo ✅ Database sudah ada!
)
echo.

:SETUP_ENV
echo [STEP 3/7] Memeriksa file .env...
echo.
if not exist .env (
    echo ⚠️  File .env belum ada, membuat dari template...
    copy .env.example .env >nul
    echo ✅ File .env berhasil dibuat!
) else (
    echo ✅ File .env sudah ada!
)
echo.

:GENERATE_KEY
echo [STEP 4/7] Generate Application Key...
echo.
php artisan key:generate
echo.

:CREATE_STORAGE_LINK
echo [STEP 5/7] Membuat Storage Link...
echo.
php artisan storage:link
if errorlevel 1 (
    echo ⚠️  Storage link mungkin sudah ada atau error, melanjutkan...
)
echo.

:RUN_MIGRATION
echo [STEP 6/7] Menjalankan Migration dan Seeder...
echo.
echo Membuat tabel-tabel database...
php artisan migrate --force
if errorlevel 1 (
    color 0C
    echo.
    echo ❌ Migration gagal!
    echo.
    echo Kemungkinan penyebab:
    echo 1. Koneksi database terputus
    echo 2. Database sudah berisi tabel (gunakan: php artisan migrate:fresh --seed)
    echo 3. User MySQL tidak punya permission
    echo.
    pause
    exit /b 1
)
echo.

echo Mengisi data awal (User, 6 Rusun, 8 contoh barang)...
php artisan db:seed --force
echo.

:CLEAR_CACHE
echo [STEP 7/7] Clear Cache...
echo.
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo.

:SUCCESS
color 0A
cls
echo.
echo ╔════════════════════════════════════════════════════════════════╗
echo ║                    ✅ SETUP BERHASIL! ✅                       ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.
echo Sistem Inventory Sarpras UPRS VI sudah siap digunakan!
echo.
echo Kredensial login default (wajib diganti setelah login):
echo   Email    : petugas@sarpras.com
echo   Password : password123
echo.
echo ┌────────────────────────────────────────────────────────────────┐
echo │ CARA MENJALANKAN APLIKASI:                                     │
echo └────────────────────────────────────────────────────────────────┘
echo.
echo 1. Buka Command Prompt atau Terminal baru
echo 2. Jalankan command:
echo.
echo    cd C:\Laravel\Sistem_Inventory_UPRS_VI
echo    php artisan serve
echo.
echo 3. Buka browser dan akses:
echo.
echo    http://localhost:8000
echo.
echo ┌────────────────────────────────────────────────────────────────┐
echo │ ATAU jalankan otomatis sekarang?                              │
echo └────────────────────────────────────────────────────────────────┘
echo.
set /p choice="Jalankan server sekarang? (Y/N): "
if /i "%choice%"=="Y" (
    echo.
    echo Starting Laravel development server...
    echo Server akan berjalan di: http://localhost:8000
    echo.
    echo Tekan Ctrl+C untuk stop server.
    echo ────────────────────────────────────────────────────────────────
    echo.
    start http://localhost:8000
    php artisan serve
) else (
    echo.
    echo Untuk menjalankan server nanti, gunakan: php artisan serve
    echo.
    pause
)
