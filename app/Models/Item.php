<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    /**
     * Placeholder resmi untuk barang tanpa foto / foto hilang.
     * Disimpan di public/ agar ikut ter-deploy dan selalu tersedia.
     */
    public const DEFAULT_PHOTO_URL = 'images/items/default.jpg';

    /** Harga satuan maksimal: Rp 1.000.000.000 (batas yang disetujui). */
    public const MAX_UNIT_PRICE = 1000000000;

    /** Batas kolom INT MySQL (signed) untuk kolom stock / min_stock. */
    public const MAX_STOCK = 2147483647;

    /** Batas aman Penambahan stok per permintaan, mencegah overflow INT. */
    public const MAX_ADD_STOCK = 1000000;

    protected $fillable = [
        'item_code',
        'name',
        'photo',
        'unit_price',
        'stock',
        'min_stock',
        'unit',
        'description'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Apakah file foto barang benar-benar tersedia di storage.
     */
    public function hasPhoto(): bool
    {
        return $this->photo && Storage::disk('public')->exists('items/' . $this->photo);
    }

    /**
     * URL foto barang.
     *
     * Foto hasil upload disimpan di disk "public" (storage/app/public/items/...).
     * Bila kolom photo kosong atau file-nya benar-benar hilang di storage,
     * kembalikan placeholder resmi di public/ supaya tidak ada gambar rusak.
     */
    public function photoUrl(): string
    {
        if ($this->hasPhoto()) {
            return asset('storage/items/' . rawurlencode($this->photo));
        }

        return asset(self::DEFAULT_PHOTO_URL);
    }

    // Relationship: Item has many withdrawals
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    // Check if stock is low
    public function isLowStock()
    {
        return $this->stock <= $this->min_stock && $this->min_stock > 0;
    }

    // Get stock status
    public function getStockStatus()
    {
        if ($this->stock == 0) {
            return 'empty';
        } elseif ($this->isLowStock()) {
            return 'low';
        } elseif ($this->stock > $this->min_stock * 2) {
            return 'good';
        } else {
            return 'normal';
        }
    }
}
