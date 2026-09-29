@extends('layouts.app')

@section('title', 'Detail Barang - SI Inventory Sarpras')
@section('page-title', 'Detail Barang')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-eye"></i> Detail Barang
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        @if($item->photo)
                            <img src="{{ asset('storage/items/' . $item->photo) }}" alt="{{ $item->name }}" class="img-fluid rounded shadow-sm">
                        @else
                            <div class="bg-light p-5 rounded">
                                <i class="bi bi-image" style="font-size: 4rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">Tidak ada foto</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">ID Barang</th>
                                <td><strong>{{ $item->item_code }}</strong></td>
                            </tr>
                            <tr>
                                <th>Nama Barang</th>
                                <td>{{ $item->name }}</td>
                            </tr>
                            <tr>
                                <th>Harga Satuan</th>
                                <td><strong class="text-success">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <th>Stok</th>
                                <td>
                                    @if($item->stock > 10)
                                        <span class="badge bg-success fs-6">{{ $item->stock }} {{ $item->unit }}</span>
                                    @elseif($item->stock > 0)
                                        <span class="badge bg-warning fs-6">{{ $item->stock }} {{ $item->unit }}</span>
                                    @else
                                        <span class="badge bg-danger fs-6">{{ $item->stock }} {{ $item->unit }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Input</th>
                                <td>{{ date('d F Y', strtotime($item->created_at)) }}</td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $item->description ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('items.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <div>
                        <a href="{{ route('items.edit', $item->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @if($item->stock > 0)
                            <a href="{{ route('withdrawals.create', ['item' => $item->id]) }}" class="btn btn-primary">
                                <i class="bi bi-box-arrow-right"></i> Pengambilan Barang
                            </a>
                        @else
                            <button class="btn btn-primary" disabled>
                                <i class="bi bi-box-arrow-right"></i> Stok Habis
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
