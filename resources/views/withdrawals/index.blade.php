@extends('layouts.app')

@section('title', 'Riwayat Pengambilan - SI Inventory Sarpras')
@section('page-title', 'Pengambilan Barang')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history"></i> Riwayat Pengambilan</span>
        <a href="{{ route('withdrawals.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Pengambilan Baru
        </a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form method="GET" action="{{ route('withdrawals.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari barang / pengambil..." value="{{ $search }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        @if($search)
                        <a href="{{ route('withdrawals.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        <!-- Withdrawals Table -->
        @if($withdrawals->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>ID Barang</th>
                            <th>Barang</th>
                            <th>Pengambil</th>
                            <th>Rusun</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawals as $index => $withdrawal)
                        <tr>
                            <td>{{ $withdrawals->firstItem() + $index }}</td>
                            <td><strong>{{ $withdrawal->item->item_code }}</strong></td>
                            <td>{{ $withdrawal->item->name }}</td>
                            <td>{{ $withdrawal->taken_by }}</td>
                            <td><span class="badge bg-info">{{ $withdrawal->rusun->code }}</span></td>
                            <td>{{ $withdrawal->quantity }} {{ $withdrawal->item->unit }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($withdrawal->taken_at)) }}</td>
                            <td>Rp{{ number_format($withdrawal->subtotal, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('withdrawals.show', $withdrawal->id) }}" class="btn btn-info btn-sm" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $withdrawals->links() }}
            </div>
        @else
            <p class="text-center text-muted py-4">
                @if($search)
                    Tidak ada data pengambilan yang ditemukan dengan kata kunci "{{ $search }}".
                @else
                    Belum ada data pengambilan. Silakan buat pengambilan baru.
                @endif
            </p>
        @endif
    </div>
</div>
@endsection
