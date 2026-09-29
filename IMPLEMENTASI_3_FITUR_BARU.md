# 🚀 IMPLEMENTASI 3 FITUR BARU

## Status: READY TO IMPLEMENT

Dokumen ini berisi implementasi lengkap untuk 3 fitur yang diminta:
1. **Stok Masuk** - Flow inventory lengkap (masuk & keluar)
2. **Minimum Stok Alert** - Notifikasi stok menipis  
3. **Dashboard Chart** - Visual analytics dengan grafik

---

## 📋 PERSIAPAN

### Yang Sudah Dibuat:
✅ Migration: `create_incoming_stocks_table`
✅ Migration: `add_min_stock_to_items_table`
✅ Model: `IncomingStock`
✅ Controller: `IncomingStockController`
✅ Routes: `/incoming-stocks/*`
✅ Model Item: Tambah methods `isLowStock()` dan `getStockStatus()`

### Yang Perlu Dilakukan:
1. Jalankan migration
2. Buat views untuk stok masuk
3. Update dashboard dengan chart
4. Update form items (tambah min_stock)
5. Update layout (tambah menu stok masuk)

---

## 🔧 LANGKAH IMPLEMENTASI

### STEP 1: Jalankan Migration

```bash
php artisan migrate
```

Ini akan membuat:
- Tabel `incoming_stocks` (stok masuk)
- Kolom `min_stock` di tabel `items`

---

### STEP 2: Update Forms Items (Tambah Min Stock)

File yang perlu diupdate:
1. `resources/views/items/create.blade.php`
2. `resources/views/items/edit.blade.php`

Tambahkan field min_stock setelah field stock:

```blade
<div class="mb-3">
    <label for="min_stock" class="form-label">Minimum Stok</label>
    <input type="number" class="form-control @error('min_stock') is-invalid @enderror" 
           id="min_stock" name="min_stock" value="{{ old('min_stock', $item->min_stock ?? 0) }}" 
           placeholder="0" min="0">
    <small class="text-muted">Sistem akan memberikan peringatan jika stok <= minimum stok</small>
    @error('min_stock')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

Update validasi di `ItemController.php`:
```php
'min_stock' => 'nullable|integer|min:0',
```

---

### STEP 3: Update Layout (Tambah Menu Stok Masuk)

File: `resources/views/layouts/app.blade.php`

Tambahkan menu baru di sidebar setelah menu Inventory Barang:

```blade
<a class="nav-link {{ request()->routeIs('incoming-stocks.*') ? 'active' : '' }}" 
   href="{{ route('incoming-stocks.index') }}">
    <i class="bi bi-box-arrow-in-down"></i> Stok Masuk
</a>
```

---

### STEP 4: Buat Views Stok Masuk

Buat folder: `resources/views/incoming-stocks/`

#### A. `index.blade.php` - List Stok Masuk

```blade
@extends('layouts.app')

@section('title', 'Stok Masuk - SI Inventory Sarpras')
@section('page-title', 'Stok Masuk')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-box-arrow-in-down"></i> Riwayat Stok Masuk</span>
        <a href="{{ route('incoming-stocks.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah Stok Masuk
        </a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form method="GET" action="{{ route('incoming-stocks.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari barang / supplier / invoice..." value="{{ $search }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        @if($search)
                        <a href="{{ route('incoming-stocks.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        @if($incomingStocks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Terima</th>
                            <th>ID Barang</th>
                            <th>Nama Barang</th>
                            <th>Supplier</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incomingStocks as $index => $incoming)
                        <tr>
                            <td>{{ $incomingStocks->firstItem() + $index }}</td>
                            <td>{{ date('d/m/Y', strtotime($incoming->received_at)) }}</td>
                            <td><strong>{{ $incoming->item->item_code }}</strong></td>
                            <td>{{ $incoming->item->name }}</td>
                            <td>{{ $incoming->supplier ?? '-' }}</td>
                            <td>{{ $incoming->quantity }} {{ $incoming->item->unit }}</td>
                            <td>Rp{{ number_format($incoming->unit_price, 0, ',', '.') }}</td>
                            <td><strong>Rp{{ number_format($incoming->total_price, 0, ',', '.') }}</strong></td>
                            <td>
                                <a href="{{ route('incoming-stocks.show', $incoming->id) }}" 
                                   class="btn btn-info btn-sm" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $incomingStocks->links() }}
            </div>
        @else
            <p class="text-center text-muted py-4">
                @if($search)
                    Tidak ada data stok masuk yang ditemukan dengan kata kunci "{{ $search }}".
                @else
                    Belum ada data stok masuk. Silakan tambah stok masuk baru.
                @endif
            </p>
        @endif
    </div>
</div>
@endsection
```

#### B. `create.blade.php` - Form Tambah Stok Masuk

```blade
@extends('layouts.app')

@section('title', 'Tambah Stok Masuk - SI Inventory Sarpras')
@section('page-title', 'Tambah Stok Masuk')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-box-arrow-in-down"></i> Form Tambah Stok Masuk
            </div>
            <div class="card-body">
                <form action="{{ route('incoming-stocks.store') }}" method="POST" id="incomingForm">
                    @csrf

                    <div class="mb-3">
                        <label for="item_id" class="form-label">Barang <span class="text-danger">*</span></label>
                        <select class="form-select @error('item_id') is-invalid @enderror" 
                                id="item_id" name="item_id" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" 
                                        data-code="{{ $item->item_code }}"
                                        data-current-stock="{{ $item->stock }}"
                                        data-unit="{{ $item->unit }}"
                                        {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->item_code }} - {{ $item->name }} (Stok: {{ $item->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="item_code_display" class="form-label">ID Barang</label>
                        <input type="text" class="form-control" id="item_code_display" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Jumlah Masuk <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                               id="quantity" name="quantity" value="{{ old('quantity') }}" 
                               placeholder="Jumlah barang yang masuk" min="1" required>
                        <small class="text-muted" id="stock_info"></small>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="unit_price" class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control @error('unit_price') is-invalid @enderror" 
                                   id="unit_price" name="unit_price" value="{{ old('unit_price') }}" 
                                   placeholder="Harga per unit" min="0" step="0.01" required>
                        </div>
                        @error('unit_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="total_display" class="form-label">Total Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control bg-light" id="total_display" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="supplier" class="form-label">Supplier</label>
                        <input type="text" class="form-control @error('supplier') is-invalid @enderror" 
                               id="supplier" name="supplier" value="{{ old('supplier') }}" 
                               placeholder="Nama supplier (opsional)">
                        @error('supplier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="invoice_number" class="form-label">Nomor Invoice</label>
                        <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                               id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}" 
                               placeholder="Nomor invoice (opsional)">
                        @error('invoice_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="received_at" class="form-label">Tanggal Terima <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('received_at') is-invalid @enderror" 
                               id="received_at" name="received_at" 
                               value="{{ old('received_at', date('Y-m-d\TH:i')) }}" required>
                        @error('received_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('incoming-stocks.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Stok Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // When item is selected
    $('#item_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const itemCode = selectedOption.data('code');
        const currentStock = selectedOption.data('current-stock');
        const unit = selectedOption.data('unit');

        $('#item_code_display').val(itemCode || '');
        $('#stock_info').text(currentStock ? `Stok saat ini: ${currentStock} ${unit}` : '');
        
        calculateTotal();
    });

    // Calculate total when quantity or price changes
    $('#quantity, #unit_price').on('input', calculateTotal);

    function calculateTotal() {
        const quantity = parseInt($('#quantity').val()) || 0;
        const price = parseFloat($('#unit_price').val()) || 0;
        const total = quantity * price;
        
        if (quantity > 0 && price > 0) {
            $('#total_display').val(formatNumber(total));
        } else {
            $('#total_display').val('');
        }
    }

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
});
</script>
@endpush
```

#### C. `show.blade.php` - Detail Stok Masuk

```blade
@extends('layouts.app')

@section('title', 'Detail Stok Masuk - SI Inventory Sarpras')
@section('page-title', 'Detail Stok Masuk')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Detail Stok Masuk
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="35%">Tanggal Terima</th>
                        <td>{{ date('d F Y H:i', strtotime($incomingStock->received_at)) }}</td>
                    </tr>
                    <tr>
                        <th>ID Barang</th>
                        <td><strong>{{ $incomingStock->item->item_code }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <td>{{ $incomingStock->item->name }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Masuk</th>
                        <td><strong>{{ $incomingStock->quantity }} {{ $incomingStock->item->unit }}</strong></td>
                    </tr>
                    <tr>
                        <th>Harga Satuan</th>
                        <td>Rp{{ number_format($incomingStock->unit_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total Harga</th>
                        <td><strong class="text-success">Rp{{ number_format($incomingStock->total_price, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td>{{ $incomingStock->supplier ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nomor Invoice</th>
                        <td>{{ $incomingStock->invoice_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $incomingStock->notes ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Dicatat pada</th>
                        <td>{{ date('d F Y H:i', strtotime($incomingStock->created_at)) }}</td>
                    </tr>
                </table>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('incoming-stocks.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('items.show', $incomingStock->item->id) }}" class="btn btn-info">
                        <i class="bi bi-box-seam"></i> Lihat Detail Barang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

### STEP 5: Update Dashboard dengan Chart

File: `app/Http/Controllers/DashboardController.php`

Tambahkan data untuk chart:

```php
public function index()
{
    // Existing code...
    
    // Data untuk chart (6 bulan terakhir)
    $monthlyWithdrawals = [];
    $monthlyIncoming = [];
    $months = [];
    
    for ($i = 5; $i >= 0; $i--) {
        $date = Carbon::now()->subMonths($i);
        $monthName = $date->translatedFormat('M Y');
        
        $withdrawalCount = Withdrawal::whereYear('taken_at', $date->year)
            ->whereMonth('taken_at', $date->month)
            ->count();
            
        $incomingCount = IncomingStock::whereYear('received_at', $date->year)
            ->whereMonth('received_at', $date->month)
            ->count();
        
        $months[] = $monthName;
        $monthlyWithdrawals[] = $withdrawalCount;
        $monthlyIncoming[] = $incomingCount;
    }
    
    // Low stock items (stok menipis)
    $lowStockItems = Item::whereColumn('stock', '<=', 'min_stock')
        ->where('min_stock', '>', 0)
        ->orderBy('stock', 'asc')
        ->limit(5)
        ->get();

    return view('dashboard.index', compact(
        'totalItems',
        'totalWithdrawals',
        'withdrawalsThisMonth',
        'totalValue',
        'recentWithdrawals',
        'months',
        'monthlyWithdrawals',
        'monthlyIncoming',
        'lowStockItems'
    ));
}
```

File: `resources/views/dashboard/index.blade.php`

Tambahkan setelah stat cards:

```blade
<!-- Low Stock Alert -->
@if($lowStockItems->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning">
            <h5><i class="bi bi-exclamation-triangle"></i> Peringatan Stok Menipis</h5>
            <p class="mb-2">Berikut barang yang stoknya sudah mencapai/di bawah minimum stok:</p>
            <ul class="mb-0">
                @foreach($lowStockItems as $item)
                <li>
                    <strong>{{ $item->name }}</strong> - 
                    Stok: <span class="badge bg-danger">{{ $item->stock }}</span> / 
                    Min: {{ $item->min_stock }} {{ $item->unit }}
                    <a href="{{ route('items.show', $item->id) }}" class="ms-2">
                        <i class="bi bi-arrow-right-circle"></i> Lihat Detail
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<!-- Charts -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up"></i> Grafik Transaksi (6 Bulan Terakhir)
            </div>
            <div class="card-body">
                <canvas id="transactionChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>
```

Tambahkan scripts (sebelum `@endsection`):

```blade
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Transaction Chart
const ctx = document.getElementById('transactionChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($months),
        datasets: [{
            label: 'Stok Masuk',
            data: @json($monthlyIncoming),
            borderColor: 'rgb(46, 204, 113)',
            backgroundColor: 'rgba(46, 204, 113, 0.1)',
            tension: 0.4
        }, {
            label: 'Pengambilan',
            data: @json($monthlyWithdrawals),
            borderColor: 'rgb(52, 152, 219)',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Tren Stok Masuk & Pengambilan'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endpush
```

---

### STEP 6: Update Inventory List (Badge Stok Menipis)

File: `resources/views/items/index.blade.php`

Update bagian badge stok:

```blade
<td>
    @php
        $status = $item->getStockStatus();
    @endphp
    
    @if($status == 'empty')
        <span class="badge bg-danger">{{ $item->stock }}</span>
    @elseif($status == 'low')
        <span class="badge bg-warning text-dark">
            <i class="bi bi-exclamation-triangle"></i> {{ $item->stock }}
        </span>
    @elseif($status == 'good')
        <span class="badge bg-success">{{ $item->stock }}</span>
    @else
        <span class="badge bg-info">{{ $item->stock }}</span>
    @endif
</td>
```

---

## 📊 FITUR YANG AKAN DITAMBAHKAN

### 1. STOK MASUK
✅ Tabel `incoming_stocks` dengan field:
- item_id, quantity, unit_price, total_price
- supplier, invoice_number, received_at, notes

✅ Fitur:
- Riwayat stok masuk
- Tambah stok masuk baru
- Auto-increase stock
- Auto-update harga
- Search & pagination
- Detail stok masuk

### 2. MINIMUM STOK ALERT
✅ Kolom `min_stock` di tabel items

✅ Fitur:
- Setting min_stock per barang
- Alert box di dashboard (stok <= min_stock)
- Badge warning di inventory list
- Method `isLowStock()` di Model

### 3. DASHBOARD CHART
✅ Chart.js integration

✅ Fitur:
- Line chart stok masuk vs pengambilan (6 bulan)
- Visual analytics
- Responsive chart
- Legend & tooltip

---

## 🎯 TESTING

### Test Stok Masuk:
1. Akses menu "Stok Masuk"
2. Klik "Tambah Stok Masuk"
3. Pilih barang, isi jumlah & harga
4. Simpan
5. ✅ Cek stok barang bertambah
6. ✅ Cek riwayat stok masuk

### Test Minimum Stok:
1. Edit barang, set min_stock = 10
2. Buat stok barang jadi 5
3. ✅ Lihat alert di dashboard
4. ✅ Lihat badge warning di inventory

### Test Chart:
1. Buka dashboard
2. ✅ Lihat grafik transaksi
3. ✅ Hover untuk lihat detail
4. ✅ Chart responsive di mobile

---

## ✅ CHECKLIST IMPLEMENTASI

- [x] Migration incoming_stocks
- [x] Migration add_min_stock
- [x] Model IncomingStock
- [x] Controller IncomingStockController
- [x] Routes stok masuk
- [x] Model Item: methods isLowStock(), getStockStatus()
- [ ] Views: index, create, show untuk incoming-stocks
- [ ] Update layout: tambah menu Stok Masuk
- [ ] Update items create/edit: tambah field min_stock
- [ ] Update ItemController: validasi min_stock
- [ ] Update DashboardController: data chart & low stock
- [ ] Update dashboard view: chart & alert
- [ ] Update items index: badge low stock
- [ ] Testing semua fitur

---

## 📝 CATATAN

- Stok masuk **tidak bisa diedit/dihapus** (audit trail)
- Min stock default = 0 (tidak ada alert)
- Chart menggunakan Chart.js (CDN)
- Low stock alert max 5 items di dashboard
- Grafik menampilkan 6 bulan terakhir

---

Dokumen ini sudah 100% lengkap untuk diimplementasikan!
Cukup jalankan migration, copy-paste code views, dan test! 🚀
