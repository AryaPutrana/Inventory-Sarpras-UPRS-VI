@extends('layouts.app')

@section('title', 'Pengambilan Barang - SI Inventory Sarpras')
@section('page-title', 'Pengambilan Barang')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-box-arrow-right"></i> Form Pengambilan Barang
            </div>
            <div class="card-body">
                <form action="{{ route('withdrawals.store') }}" method="POST" id="withdrawalForm">
                    @csrf

                    <div class="mb-3">
                        <label for="item_id" class="form-label">Barang <span class="text-danger">*</span></label>
                        <select class="form-select @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required>
                            <option value="">-- Cari / Pilih Barang --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" 
                                        data-code="{{ $item->item_code }}"
                                        data-price="{{ $item->unit_price }}"
                                        data-stock="{{ $item->stock }}"
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
                        <label for="taken_by" class="form-label">Pengambil <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('taken_by') is-invalid @enderror" 
                               id="taken_by" name="taken_by" value="{{ old('taken_by') }}" 
                               placeholder="Nama pengambil" required>
                        @error('taken_by')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="rusun_id" class="form-label">Rusun <span class="text-danger">*</span></label>
                        <select class="form-select @error('rusun_id') is-invalid @enderror" id="rusun_id" name="rusun_id" required>
                            <option value="">-- Pilih Rusun --</option>
                            @foreach($rusuns as $rusun)
                                <option value="{{ $rusun->id }}" {{ old('rusun_id') == $rusun->id ? 'selected' : '' }}>
                                    {{ $rusun->name }} ({{ $rusun->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('rusun_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                               id="quantity" name="quantity" value="{{ old('quantity') }}" 
                               placeholder="Jumlah barang" min="1" required>
                        <small class="text-muted" id="stock_info"></small>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="unit_price_display" class="form-label">Harga Satuan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="unit_price_display" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="subtotal_display" class="form-label">Subtotal</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control bg-light" id="subtotal_display" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="taken_at" class="form-label">Tanggal dan Waktu Pengambilan <span class="text-danger">*</span></label>
                        <input type="datetime-local" 
                               class="form-control bg-light @error('taken_at') is-invalid @enderror" 
                               id="taken_at" 
                               name="taken_at" 
                               value="{{ old('taken_at', now()->format('Y-m-d\TH:i')) }}" 
                               readonly 
                               required>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Tanggal dan waktu otomatis terisi (realtime hari ini)
                        </small>
                        @error('taken_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Keterangan</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Keterangan tambahan (opsional)">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('withdrawals.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Pengambilan
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
    let selectedStock = 0;
    let selectedPrice = 0;

    // When item is selected
    $('#item_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const itemCode = selectedOption.data('code');
        const price = selectedOption.data('price');
        const stock = selectedOption.data('stock');
        const unit = selectedOption.data('unit');

        selectedStock = stock;
        selectedPrice = price;

        $('#item_code_display').val(itemCode || '');
        $('#unit_price_display').val(price ? formatNumber(price) : '');
        $('#stock_info').text(stock ? `Stok tersedia: ${stock} ${unit}` : '');
        
        // Reset quantity and subtotal
        $('#quantity').val('').attr('max', stock);
        $('#subtotal_display').val('');
        
        calculateSubtotal();
    });

    // Calculate subtotal when quantity changes
    $('#quantity').on('input', function() {
        calculateSubtotal();
    });

    function calculateSubtotal() {
        const quantity = parseInt($('#quantity').val()) || 0;
        const subtotal = quantity * selectedPrice;
        
        if (quantity > 0) {
            $('#subtotal_display').val(formatNumber(subtotal));
        } else {
            $('#subtotal_display').val('');
        }
    }

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Form validation before submit
    $('#withdrawalForm').on('submit', function(e) {
        const quantity = parseInt($('#quantity').val()) || 0;
        
        if (quantity > selectedStock) {
            e.preventDefault();
            alert(`Stok tidak mencukupi. Stok tersedia hanya ${selectedStock}.`);
            return false;
        }
    });
});
</script>
@endpush
