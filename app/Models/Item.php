<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

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

    // Relationship: Item has many withdrawals
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    // Relationship: Item has many incoming stocks
    public function incomingStocks()
    {
        return $this->hasMany(IncomingStock::class);
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
