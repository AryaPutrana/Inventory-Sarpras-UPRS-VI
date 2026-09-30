@extends('layouts.app')

@section('title', 'Detail Pengambilan - SI Inventory Sarpras')
@section('page-title', 'Detail Pengambilan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Detail Pengambilan Barang
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="35%">ID Barang</th>
                        <td><strong>{{ $withdrawal->item->item_code }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <td>{{ $withdrawal->item->name }}</td>
                    </tr>
                    <tr>
                        <th>Pengambil</th>
                        <td><strong>{{ $withdrawal->taken_by }}</strong></td>
                    </tr>
                    <tr>
                        <th>Rusun</th>
                        <td><span class="badge bg-info fs-6">{{ $withdrawal->rusun->name }} ({{ $withdrawal->rusun->code }})</span></td>
                    </tr>
                    <tr>
                        <th>Jumlah</th>
                        <td><strong>{{ $withdrawal->quantity }} {{ $withdrawal->item->unit }}</strong></td>
                    </tr>
                    <tr>
                        <th>Harga Satuan</th>
                        <td>Rp{{ number_format($withdrawal->unit_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Subtotal</th>
                        <td><strong class="text-success">Rp{{ number_format($withdrawal->subtotal, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tanggal & Waktu Pengambilan</th>
                        <td>{{ \Carbon\Carbon::parse($withdrawal->taken_at)->translatedFormat('d F Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $withdrawal->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Dicatat pada</th>
                        <td>{{ \Carbon\Carbon::parse($withdrawal->created_at)->translatedFormat('d F Y H:i') }}</td>
                    </tr>
                </table>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('withdrawals.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('items.show', $withdrawal->item->id) }}" class="btn btn-info">
                        <i class="bi bi-box-seam"></i> Lihat Detail Barang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
