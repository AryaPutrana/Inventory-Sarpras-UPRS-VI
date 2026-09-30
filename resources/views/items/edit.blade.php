@extends('layouts.app')

@section('title', 'Edit Barang - SI Inventory Sarpras')
@section('page-title', 'Edit Barang')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil"></i> Form Edit Barang
            </div>
            <div class="card-body">
                <form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="item_code" class="form-label">ID Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('item_code') is-invalid @enderror" 
                               id="item_code" name="item_code" value="{{ old('item_code', $item->item_code) }}" 
                               placeholder="Contoh: BRG-0001" required>
                        @error('item_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $item->name) }}" 
                               placeholder="Contoh: Cat Tembok 5 Kg" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="photo" class="form-label">Foto Barang</label>
                        @if($item->photo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/items/' . rawurlencode($item->photo)) }}" alt="{{ $item->name }}" class="img-thumbnail" style="max-width: 200px;">
                                <p class="text-muted small">Foto saat ini</p>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                               id="photo" name="photo" accept="image/jpeg,image/jpg,image/png,image/webp">
                        <small class="text-muted">Format: JPG, JPEG, PNG, WEBP (Max: 2MB). Kosongkan jika tidak ingin mengubah foto.</small>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="unit_price" class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control @error('unit_price') is-invalid @enderror" 
                                   id="unit_price" name="unit_price" value="{{ old('unit_price', $item->unit_price) }}" 
                                   placeholder="Harga per unit" min="0" step="0.01" required>
                        </div>
                        @error('unit_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="stock_display" class="form-label">Jumlah / Stok Saat Ini</label>
                        <input type="number" class="form-control bg-light" 
                               id="stock_display" value="{{ $item->stock }}" readonly>
                        <small class="text-muted">Stok saat ini: <strong>{{ $item->stock }}</strong> {{ $item->unit }}</small>
                    </div>

                    <div class="mb-3">
                        <label for="add_stock" class="form-label">
                            <i class="bi bi-plus-circle"></i> Tambah Stok
                        </label>
                        <input type="number" class="form-control @error('add_stock') is-invalid @enderror" 
                               id="add_stock" name="add_stock" value="{{ old('add_stock', 0) }}" 
                               placeholder="Jumlah yang ingin ditambahkan" min="0">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Kosongkan atau isi 0 jika tidak menambah stok. 
                            Contoh: isi 10 untuk menambah 10 unit ({{ $item->stock }} + 10 = {{ $item->stock + 10 }})
                        </small>
                        @error('add_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="min_stock" class="form-label">Minimum Stok</label>
                        <input type="number" class="form-control @error('min_stock') is-invalid @enderror" 
                               id="min_stock" name="min_stock" value="{{ old('min_stock', $item->min_stock) }}" 
                               placeholder="0" min="0">
                        <small class="text-muted">Sistem akan memberikan peringatan jika stok <= minimum stok ini</small>
                        @error('min_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="unit" class="form-label">Satuan <span class="text-danger">*</span></label>
                        <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit" required>
                            <option value="">-- Pilih Satuan --</option>
                            <option value="pcs" {{ old('unit', $item->unit) == 'pcs' ? 'selected' : '' }}>pcs</option>
                            <option value="unit" {{ old('unit', $item->unit) == 'unit' ? 'selected' : '' }}>unit</option>
                            <option value="box" {{ old('unit', $item->unit) == 'box' ? 'selected' : '' }}>box</option>
                            <option value="botol" {{ old('unit', $item->unit) == 'botol' ? 'selected' : '' }}>botol</option>
                            <option value="kaleng" {{ old('unit', $item->unit) == 'kaleng' ? 'selected' : '' }}>kaleng</option>
                            <option value="meter" {{ old('unit', $item->unit) == 'meter' ? 'selected' : '' }}>meter</option>
                            <option value="liter" {{ old('unit', $item->unit) == 'liter' ? 'selected' : '' }}>liter</option>
                            <option value="kg" {{ old('unit', $item->unit) == 'kg' ? 'selected' : '' }}>kg</option>
                            <option value="roll" {{ old('unit', $item->unit) == 'roll' ? 'selected' : '' }}>roll</option>
                        </select>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Keterangan</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Keterangan tambahan (opsional)">{{ old('description', $item->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('items.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
