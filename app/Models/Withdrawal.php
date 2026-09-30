<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    /** Batas aman jumlah pengambilan per transaksi (mencegah overflow INT). */
    public const MAX_QUANTITY = 1000000;

    /** Batas maksimum kolom DECIMAL(15,2) MySQL: 9999999999999.99 */
    public const MAX_SUBTOTAL = 9999999999999.99;

    protected $fillable = [
        'item_id',
        'taken_by',
        'rusun_id',
        'quantity',
        'unit_price',
        'subtotal',
        'taken_at',
        'description'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'taken_at' => 'datetime',
    ];

    // Relationship: Withdrawal belongs to Item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Relationship: Withdrawal belongs to Rusun
    public function rusun()
    {
        return $this->belongsTo(Rusun::class);
    }
}
