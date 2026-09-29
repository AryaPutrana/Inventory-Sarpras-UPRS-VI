@extends('layouts.app')

@section('title', 'Laporan - SI Inventory Sarpras')
@section('page-title', 'Laporan')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-file-earmark-text"></i> Laporan Pengambilan Material
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('reports.index') }}" id="reportForm">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}" required>
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}" required>
                </div>
                <div class="col-md-4">
                    <label for="rusun_id" class="form-label">Rusun</label>
                    <select class="form-select" id="rusun_id" name="rusun_id">
                        <option value="all" {{ $rusunId == 'all' || !$rusunId ? 'selected' : '' }}>Semua Rusun</option>
                        @foreach($rusuns as $rusun)
                            <option value="{{ $rusun->id }}" {{ $rusunId == $rusun->id ? 'selected' : '' }}>
                                {{ $rusun->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Tampilkan Laporan
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
                @if($startDate && $endDate)
                    <button type="button" class="btn btn-success" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                    <a href="{{ route('reports.pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'rusun_id' => $rusunId]) }}" 
                       class="btn btn-danger" target="_blank">
                        <i class="bi bi-file-pdf"></i> Export PDF
                    </a>
                    <a href="{{ route('reports.excel', ['start_date' => $startDate, 'end_date' => $endDate, 'rusun_id' => $rusunId]) }}" 
                       class="btn btn-success">
                        <i class="bi bi-file-excel"></i> Export Excel
                    </a>
                @endif
            </div>
        </form>

        <hr>

        <!-- Report Content -->
        @if($startDate && $endDate)
            <div id="reportContent">
                <div class="text-center mb-4">
                    <h4>LAPORAN PENGAMBILAN MATERIAL</h4>
                    <p class="mb-0">
                        <strong>Periode:</strong> {{ date('d F Y', strtotime($startDate)) }} - {{ date('d F Y', strtotime($endDate)) }}
                    </p>
                    <p>
                        <strong>Rusun:</strong> 
                        @if($rusunId && $rusunId != 'all')
                            {{ $rusuns->find($rusunId)->name }}
                        @else
                            Semua Rusun
                        @endif
                    </p>
                </div>

                @if($withdrawals->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>ID Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Pengambil</th>
                                    <th>Tanggal Ambil</th>
                                    <th>Rusun</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Harga Satuan</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($withdrawals as $index => $withdrawal)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $withdrawal->item->item_code }}</td>
                                    <td>{{ $withdrawal->item->name }}</td>
                                    <td>{{ $withdrawal->taken_by }}</td>
                                    <td>{{ date('d/m/Y H:i', strtotime($withdrawal->taken_at)) }}</td>
                                    <td>{{ $withdrawal->rusun->code }}</td>
                                    <td class="text-end">{{ $withdrawal->quantity }}</td>
                                    <td>{{ $withdrawal->item->unit }}</td>
                                    <td class="text-end">Rp{{ number_format($withdrawal->unit_price, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp{{ number_format($withdrawal->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="6" class="text-end">TOTAL</th>
                                    <th class="text-end">{{ $totalQuantity }}</th>
                                    <th colspan="2"></th>
                                    <th class="text-end">Rp{{ number_format($totalValue, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="card bg-light mt-4">
                        <div class="card-body">
                            <h5 class="card-title">Ringkasan Laporan</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Total Transaksi:</strong> {{ $totalTransactions }} transaksi</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Total Barang Diambil:</strong> {{ $totalQuantity }} barang</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Total Nilai:</strong> Rp{{ number_format($totalValue, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> Tidak ada data pengambilan pada periode yang dipilih.
                    </div>
                @endif
            </div>
        @else
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle"></i> Silakan pilih periode tanggal untuk menampilkan laporan.
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .sidebar, .navbar-custom, .btn, form, .card-header {
        display: none !important;
    }
    .content-wrapper {
        padding: 0 !important;
    }
    .card {
        box-shadow: none !important;
        border: none !important;
    }
    body {
        background: white !important;
    }
}
</style>
@endpush
