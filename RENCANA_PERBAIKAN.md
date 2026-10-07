# RENCANA PERBAIKAN SISTEM INVENTORY UPRS VI
**Tanggal**: 7 Oktober 2026  
**Project**: Sistem Inventory Material Sarpras UPRS VI  
**Status**: Planning Phase - Belum Dieksekusi

---

## RINGKASAN EKSEKUTIF

Berdasarkan audit menyeluruh, ditemukan **42 issue** yang terbagi dalam:
- **7 Critical Issues** (harus diperbaiki segera)
- **15 Medium Issues** (penting untuk stabilitas)
- **20 Enhancement** (nice to have)

Total estimasi: **3-5 hari kerja** untuk semua tahap.

---

## TAHAP 1: CRITICAL FIXES & SECURITY
**Prioritas**: URGENT  
**Estimasi Waktu**: 1.5 hari  
**Target**: Memperbaiki bug kritis dan menutup celah keamanan

### 1.1 Code Quality Fixes (30 menit)

#### Issue: Duplikasi `use` Statement
- **File**: `app/Http/Controllers/ReportController.php`
- **Action**: Hapus baris 134 `use SanitizesQueryInput;` (duplikat dari baris 121)
- **Risk**: Sangat rendah
- **Impact**: Code lebih clean

### 1.2 Database Performance (2 jam)

#### Issue: Missing Critical Indexes
- **File Baru**: `database/migrations/2026_10_07_create_performance_indexes.php`
- **Action**: 
  - Tambah index pada `withdrawals.taken_at` (filtering reports)
  - Tambah index pada `withdrawals.taken_by` (search)
  - Tambah index pada `items.name` (search)
  - Tambah composite index pada `items(item_code, name)` untuk search optimal
- **Query**:
  ```sql
  CREATE INDEX idx_withdrawals_taken_at ON withdrawals(taken_at);
  CREATE INDEX idx_withdrawals_taken_by ON withdrawals(taken_by);
  CREATE INDEX idx_items_name ON items(name);
  CREATE INDEX idx_items_search ON items(item_code, name);
  ```
- **Impact**: Query 5-10x lebih cepat saat data > 10,000 records
- **Risk**: Rendah, tapi perlu test setelah migration

### 1.3 Security Enhancements (3 jam)

#### 1.3.1 Rate Limiting untuk Semua Routes
- **File**: `routes/web.php`
- **Action**: Tambah throttle middleware
  ```php
  // Untuk routes CRUD
  Route::middleware(['auth', 'throttle:60,1'])->group(function () {
      // existing routes
  });
  
  // Untuk routes export (lebih ketat)
  Route::middleware(['throttle:10,1'])->group(function () {
      Route::get('/reports/export/pdf', ...);
      Route::get('/reports/export/excel', ...);
  });
  ```
- **Impact**: Mencegah abuse dan DoS attack
- **Risk**: Rendah, user normal tidak terpengaruh

#### 1.3.2 Enhanced File Upload Validation
- **File**: `app/Http/Controllers/ItemController.php`
- **Action**: 
  - Tambah custom validation rule untuk check magic bytes
  - Buat `app/Rules/ValidImage.php`
- **Implementation**:
  ```php
  'photo' => ['required', 'file', new ValidImage(), 'max:5120']
  ```
- **Impact**: Prevent malicious file upload
- **Risk**: Rendah

#### 1.3.3 Production Environment Configuration
- **File**: `.env.example`
- **Action**: Update default values untuk production-ready:
  ```
  APP_DEBUG=false  # dari true
  SESSION_LIFETIME=480  # dari 120 (jadi 8 jam)
  APP_KEY=base64:GENERATE_WITH_PHP_ARTISAN_KEY_GENERATE
  ```
- **Impact**: Security hardening
- **Risk**: None (hanya example file)

### 1.4 Password Reset Feature (4 jam)

#### Implementasi Password Reset Flow
- **Files Baru**:
  - `app/Http/Controllers/Auth/ForgotPasswordController.php`
  - `app/Http/Controllers/Auth/ResetPasswordController.php`
  - `resources/views/auth/passwords/email.blade.php`
  - `resources/views/auth/passwords/reset.blade.php`
  - `resources/views/emails/password-reset.blade.php`
  
- **Routes Baru** di `routes/web.php`:
  ```php
  Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm']);
  Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
  Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm']);
  Route::post('password/reset', [ResetPasswordController::class, 'reset']);
  ```

- **Migration**: Password reset tokens table (sudah ada di Laravel default)
  
- **Impact**: User bisa reset password sendiri tanpa admin
- **Risk**: Medium, perlu test email configuration

### 1.5 Cache Strategy untuk Thumbnails (1 jam)

#### Issue: Cache Cleanup
- **File Baru**: `app/Console/Commands/CleanupThumbnailCache.php`
- **Action**: Artisan command untuk cleanup cache
- **Schedule**: Daily cleanup di `app/Console/Kernel.php`
- **Impact**: Prevent disk space bloat
- **Risk**: Sangat rendah

---

## TAHAP 2: DATA INTEGRITY & OPTIMIZATION
**Prioritas**: HIGH  
**Estimasi Waktu**: 2 hari  
**Target**: Meningkatkan keandalan data dan performa sistem

### 2.1 Soft Deletes Implementation (3 jam)

#### Add Soft Deletes to Critical Tables
- **Files Modified**:
  - `app/Models/Item.php` - tambah `use SoftDeletes`
  - `app/Models/Withdrawal.php` - tambah `use SoftDeletes`
  - `database/migrations/2026_10_07_add_soft_deletes_to_items.php`
  - `database/migrations/2026_10_07_add_soft_deletes_to_withdrawals.php`

- **Migration SQL**:
  ```sql
  ALTER TABLE items ADD COLUMN deleted_at TIMESTAMP NULL;
  ALTER TABLE withdrawals ADD COLUMN deleted_at TIMESTAMP NULL;
  ```

- **Controller Updates**:
  - `ItemController@destroy` - ganti `$item->delete()` tetap sama (soft delete otomatis)
  - `WithdrawalController@destroy` - sama
  - Tambah method `forceDelete()` untuk permanent delete jika diperlukan

- **UI Updates**:
  - Tambah "Restore" button di index pages
  - Filter "Show Deleted" untuk admin

- **Impact**: Data recovery possible, audit trail lengkap
- **Risk**: Medium, perlu update existing queries dengan `withTrashed()`

### 2.2 Comprehensive Audit Trail (4 jam)

#### Create Audit System
- **Files Baru**:
  - `app/Models/AuditLog.php`
  - `database/migrations/2026_10_07_create_audit_logs_table.php`
  - `app/Observers/ItemObserver.php`
  - `app/Observers/WithdrawalObserver.php`
  - `app/Listeners/LogAuthenticationEvents.php`

- **Table Structure**:
  ```sql
  audit_logs:
    - id
    - user_id (nullable)
    - auditable_type (polymorphic)
    - auditable_id
    - event (created, updated, deleted, restored)
    - old_values (json)
    - new_values (json)
    - ip_address
    - user_agent
    - created_at
  ```

- **Events to Log**:
  - Login/Logout
  - Item CRUD
  - Withdrawal CRUD
  - Stock adjustments (sudah ada di stock_adjustments, tapi tambah di audit_logs juga)
  - Failed login attempts

- **Impact**: Complete audit trail untuk compliance
- **Risk**: Low-Medium, tambah sedikit overhead di setiap operation

### 2.3 Report Optimization (3 jam)

#### Handle Large Reports Better
- **Files Modified**:
  - `app/Http/Controllers/ReportController.php`

- **Changes**:
  1. **Chunk Processing untuk Excel Export**:
     ```php
     // Ganti limit(5000) dengan chunk processing
     $this->withdrawalDetailQuery(...)->chunk(1000, function($details) {
         // Write to CSV incrementally
     });
     ```
  
  2. **Add Warning untuk Large Datasets**:
     - Jika transaksi > 5000, tampilkan modal confirmation
     - Saran split by month untuk report besar
  
  3. **Background Job untuk Very Large Reports**:
     - Create `app/Jobs/GenerateReport.php`
     - Email download link when ready
     - Simpan report di `storage/app/reports/` dengan expiry 24 jam

- **Impact**: Bisa handle report unlimited size tanpa timeout
- **Risk**: Medium, perlu setup queue worker

### 2.4 Validation Refactoring (2 jam)

#### Extract to Form Requests
- **Files Baru**:
  - `app/Http/Requests/StoreItemRequest.php`
  - `app/Http/Requests/UpdateItemRequest.php`
  - `app/Http/Requests/StoreWithdrawalRequest.php`
  - `app/Http/Requests/UpdateWithdrawalRequest.php`

- **Files Modified**:
  - `app/Http/Controllers/ItemController.php` - ganti validasi inline
  - `app/Http/Controllers/WithdrawalController.php` - ganti validasi inline

- **Benefit**:
  - Controller lebih slim
  - Validasi bisa di-reuse
  - Easier to test

- **Impact**: Code maintainability meningkat
- **Risk**: Sangat rendah

---

## TAHAP 3: ENHANCEMENTS & BEST PRACTICES
**Prioritas**: MEDIUM  
**Estimasi Waktu**: 1.5 hari  
**Target**: Menambah fitur penting dan improve code quality

### 3.1 User Management System (4 jam)

#### Basic User CRUD dengan Roles
- **Files Baru**:
  - `app/Http/Controllers/UserController.php`
  - `database/migrations/2026_10_07_add_role_to_users.php`
  - `resources/views/users/index.blade.php`
  - `resources/views/users/create.blade.php`
  - `resources/views/users/edit.blade.php`

- **Migration**:
  ```sql
  ALTER TABLE users ADD COLUMN role ENUM('admin', 'staff') DEFAULT 'staff';
  ```

- **Routes**:
  ```php
  Route::middleware(['auth', 'role:admin'])->group(function () {
      Route::resource('users', UserController::class);
  });
  ```

- **Middleware Baru**:
  - `app/Http/Middleware/CheckRole.php`

- **Features**:
  - Admin bisa CRUD users
  - Admin bisa reset user password
  - Role-based access control
  - Staff hanya bisa akses operational features

- **Impact**: Proper user management
- **Risk**: Medium, perlu careful testing

### 3.2 Low Stock Notification (2 jam)

#### Email Alert System
- **Files Baru**:
  - `app/Mail/LowStockAlert.php`
  - `app/Console/Commands/CheckLowStock.php`
  - `resources/views/emails/low-stock-alert.blade.php`

- **Schedule** di `app/Console/Kernel.php`:
  ```php
  $schedule->command('stock:check-low')
           ->dailyAt('08:00');
  ```

- **Logic**:
  - Check items where `stock <= min_stock AND min_stock > 0`
  - Group by category jika ada
  - Email ke admin dengan list barang low stock
  - Jangan spam: hanya kirim jika ada perubahan dari kemarin

- **Config**:
  - Tambah `ADMIN_EMAIL` di .env
  - Bisa multiple recipients

- **Impact**: Proactive stock management
- **Risk**: Low, tapi perlu email configuration

### 3.3 Automated Backup (1.5 jam)

#### Database Backup Strategy
- **Package**: Install `spatie/laravel-backup`
  ```bash
  composer require spatie/laravel-backup
  ```

- **Configuration**:
  - `config/backup.php` - setup destination
  - Support local dan cloud (optional)

- **Schedule**:
  ```php
  $schedule->command('backup:run --only-db')
           ->dailyAt('02:00');
  
  $schedule->command('backup:clean')
           ->dailyAt('03:00');
  ```

- **Backup Destination**:
  - Local: `storage/app/backups/`
  - Retention: 7 days
  - Optional: S3, Google Drive, Dropbox

- **Impact**: Data protection & disaster recovery
- **Risk**: Low

### 3.4 Service Layer Extraction (3 hours)

#### Create Service Classes
- **Files Baru**:
  - `app/Services/WithdrawalService.php`
  - `app/Services/ItemStockService.php`
  - `app/Services/ReportService.php`

- **Extraction Plan**:
  1. **WithdrawalService**:
     - `createWithdrawal(array $data): Withdrawal`
     - `updateWithdrawal(Withdrawal $withdrawal, array $data): Withdrawal`
     - `deleteWithdrawal(Withdrawal $withdrawal): bool`
     - `adjustStock(array $items): void`
  
  2. **ItemStockService**:
     - `addStock(Item $item, int $quantity, string $reason): void`
     - `subtractStock(Item $item, int $quantity, string $reason): void`
     - `checkAvailability(int $itemId, int $quantity): bool`
  
  3. **ReportService**:
     - `generateWithdrawalReport(string $start, string $end, ?int $rusunId): Collection`
     - `exportPdf(array $data): Response`
     - `exportExcel(array $data): Response`

- **Files Modified**:
  - Controllers use services instead of direct model manipulation

- **Benefits**:
  - Single responsibility
  - Easier to test
  - Business logic reusable
  - Controller slim

- **Impact**: Better code architecture
- **Risk**: Low-Medium, perlu careful refactoring

### 3.5 Basic Testing Setup (2 jam)

#### Create Essential Tests
- **Files Baru**:
  - `tests/Feature/WithdrawalTest.php`
  - `tests/Feature/ItemStockTest.php`
  - `tests/Unit/ItemTest.php`
  - `tests/Unit/WithdrawalServiceTest.php`

- **Test Coverage**:
  1. **WithdrawalTest** (Feature):
     - Create withdrawal dengan stock sufficient
     - Create withdrawal dengan stock insufficient (should fail)
     - Update withdrawal increase quantity
     - Delete withdrawal (stock restored)
     - Concurrent withdrawal (race condition test)
  
  2. **ItemStockTest** (Feature):
     - Add stock via form
     - Subtract stock via form
     - Stock adjustment audit trail created
  
  3. **ItemTest** (Unit):
     - `isLowStock()` logic
     - `getStockStatus()` logic
     - `photoThumbnail()` generation
  
  4. **WithdrawalServiceTest** (Unit):
     - Service methods logic
     - Exception handling

- **Command**:
  ```bash
  php artisan test
  ```

- **Impact**: Prevent regression, confident refactoring
- **Risk**: None (testing doesn't affect production)

### 3.6 Additional Enhancements (1 jam)

#### Minor Improvements
1. **Pagination Configuration**:
   - Extract magic number 10 ke `config/app.php`:
     ```php
     'pagination' => [
         'per_page' => 10,
     ],
     ```

2. **Standardize Comments**:
   - Pilih Indonesian untuk semua comments
   - Update existing English comments

3. **Error Pages**:
   - Custom 404, 500, 403, 419 error pages
   - `resources/views/errors/404.blade.php` dst

4. **Loading States**:
   - Tambah loading spinner saat submit form
   - Prevent double submit

5. **Success Message Enhancement**:
   - Auto-dismiss setelah 5 detik
   - Better animation

---

## DEPENDENCY & RISK MATRIX

### Dependencies Antar Tahap
```
TAHAP 1 (Critical)
    ├── Must complete before TAHAP 2
    │   └── Database indexes needed for performance testing
    │   └── Security hardening required for audit trail
    │
TAHAP 2 (Optimization)
    ├── Can start parallel with TAHAP 1 completion
    │   └── Soft deletes independent
    │   └── Audit trail depends on security fixes
    │
TAHAP 3 (Enhancement)
    └── Can start after TAHAP 2 service layer
        └── User management independent
        └── Testing requires service layer
```

### Risk Assessment
| Tahap | Risk Level | Rollback Plan |
|-------|-----------|---------------|
| 1.1-1.3 | LOW | Simple rollback, no data impact |
| 1.4 | MEDIUM | Disable routes if email fails |
| 2.1 | MEDIUM | Keep hard delete as fallback |
| 2.2 | LOW | Audit is additive, doesn't break existing |
| 2.3 | MEDIUM | Keep old export as fallback |
| 2.4 | LOW | Pure refactor, same behavior |
| 3.1 | MEDIUM | Feature flag to disable |
| 3.2-3.6 | LOW | All additive features |

---

## TESTING CHECKLIST PER TAHAP

### TAHAP 1 Testing
- [ ] Login masih bekerja setelah rate limiting
- [ ] Upload foto masih bekerja dengan validasi baru
- [ ] Query performa improved (benchmark before/after)
- [ ] Password reset flow end-to-end
- [ ] Email diterima di inbox

### TAHAP 2 Testing
- [ ] Soft delete: item terhapus tidak muncul di list
- [ ] Soft delete: item bisa di-restore
- [ ] Audit log tercatat untuk semua events
- [ ] Report > 5000 records tidak timeout
- [ ] Excel export chunk bekerja
- [ ] Validation masih sama setelah extract ke FormRequest

### TAHAP 3 Testing
- [ ] User CRUD bekerja
- [ ] Role middleware block non-admin
- [ ] Low stock email terkirim di schedule
- [ ] Backup created dan bisa di-restore
- [ ] Service layer tidak ubah behavior
- [ ] All tests passing

---

## ROLLBACK STRATEGY

### Quick Rollback Commands
```bash
# Rollback last migration
php artisan migrate:rollback --step=1

# Rollback to specific batch
php artisan migrate:rollback --batch=X

# Full rollback (DANGER)
php artisan migrate:reset

# Restore from backup
php artisan backup:restore
```

### Git Strategy
```bash
# Setiap tahap = 1 feature branch
git checkout -b feature/tahap-1-critical-fixes
# ... work ...
git commit -m "Tahap 1: Critical fixes complete"
git push origin feature/tahap-1-critical-fixes

# Merge setelah testing
git checkout main
git merge feature/tahap-1-critical-fixes

# Tag untuk milestone
git tag -a v1.1.0 -m "Tahap 1 deployed"
```

---

## ESTIMASI DETAIL

### TAHAP 1: Critical (1.5 hari)
- Code cleanup: 0.5 jam
- Database indexes: 2 jam
- Security: 3 jam
- Password reset: 4 jam
- Cache cleanup: 1 jam
- **Total: 10.5 jam = 1.5 hari**

### TAHAP 2: Optimization (2 hari)
- Soft deletes: 3 jam
- Audit trail: 4 jam
- Report optimization: 3 jam
- Form requests: 2 jam
- **Total: 12 jam = 2 hari**

### TAHAP 3: Enhancement (1.5 hari)
- User management: 4 jam
- Notifications: 2 jam
- Backup: 1.5 jam
- Service layer: 3 jam
- Testing: 2 jam
- Minor enhancements: 1 jam
- **Total: 13.5 jam = 1.5 hari**

### GRAND TOTAL
**5 hari kerja** untuk semua tahap (40 jam)

---

## EXECUTION RECOMMENDATIONS

### Urutan Eksekusi Optimal
1. **Week 1, Day 1-2**: TAHAP 1 (Critical)
   - Deploy immediately after testing
   - Monitor closely for 24 hours

2. **Week 1, Day 3-4**: TAHAP 2 (Optimization)
   - Deploy di low-traffic hours
   - Siapkan rollback plan

3. **Week 2, Day 1-2**: TAHAP 3 (Enhancement)
   - Deploy features incrementally
   - Feature flags untuk on/off

### Team Assignment
- **1 Developer**: Bisa complete dalam 5 hari
- **2 Developers**: Bisa complete dalam 3 hari (parallel work)

### When to Deploy
- **Tahap 1**: Deploy ASAP (security critical)
- **Tahap 2**: Deploy setelah 1 week monitoring Tahap 1
- **Tahap 3**: Deploy when ready (not urgent)

---

## POST-DEPLOYMENT MONITORING

### Metrics to Track
1. **Performance**:
   - Query response time (before/after indexes)
   - Page load time
   - Report generation time

2. **Security**:
   - Failed login attempts
   - Rate limit hits
   - File upload rejections

3. **Stability**:
   - Error rate (500 errors)
   - Concurrent transaction conflicts
   - Cache hit ratio

4. **Business**:
   - Stock adjustment frequency
   - Low stock items count
   - Withdrawal transaction volume

### Monitoring Tools
- Laravel Telescope (development)
- Log monitoring di `storage/logs/`
- Database slow query log
- Server monitoring (CPU, memory, disk)

---

## NOTES & ASSUMPTIONS

### Assumptions
1. Database: MySQL/MariaDB 5.7+
2. PHP: 8.1+ (sudah confirmed)
3. Server: Apache/Nginx dengan mod_rewrite
4. Email: SMTP configured (untuk password reset & notifications)
5. Cron/Scheduler: Available untuk scheduled tasks
6. Backup storage: Minimal 10GB untuk retention 7 days

### Out of Scope (Tidak Termasuk dalam Rencana)
- Mobile app development
- API development untuk external integration
- Advanced analytics/reporting dashboard
- Multi-language support
- Real-time notifications via WebSocket
- Barcode/QR scanning integration
- Multi-warehouse support
- Approval workflow untuk withdrawals

### Future Enhancements (Post-Tahap 3)
- Dashboard advanced charts (Chart.js upgrade)
- Export format tambahan (XML, JSON)
- Bulk operations (bulk delete, bulk edit)
- Data import via Excel
- Advanced search dengan Elasticsearch
- Activity dashboard dengan real-time updates
- Integration dengan sistem lain (ERP, accounting)

---

## APPROVAL & SIGN-OFF

### Before Starting
- [ ] Review rencana dengan stakeholder
- [ ] Confirm backup strategy
- [ ] Prepare testing environment
- [ ] Setup rollback procedure
- [ ] Inform users tentang planned downtime (jika ada)

### After Each Tahap
- [ ] Code review completed
- [ ] All tests passing
- [ ] Documentation updated
- [ ] Stakeholder approval
- [ ] Deployed to production
- [ ] Post-deployment monitoring (24 hours)

---

**Dokumen ini adalah rencana, bukan eksekusi.**  
**Tidak ada perubahan code yang dilakukan saat membuat dokumen ini.**

---

## CONTACT & QUESTIONS

Jika ada pertanyaan tentang rencana ini:
1. Review setiap tahap dengan detail
2. Prioritaskan berdasarkan kebutuhan bisnis
3. Adjust estimasi sesuai resources available
4. Test thoroughly sebelum deploy ke production

**Siap untuk eksekusi?** Konfirmasi tahap mana yang akan dikerjakan terlebih dahulu.
