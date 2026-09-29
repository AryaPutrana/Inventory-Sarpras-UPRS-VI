<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan direktori storage ada
        if (!Storage::exists('public/items')) {
            Storage::makeDirectory('public/items');
        }

        $items = [
            [
                'item_code' => 'BRG-001',
                'name' => 'Sapu Lidi',
                'photo' => 'items/default.jpg',
                'unit_price' => 15000,
                'stock' => 25,
                'min_stock' => 10,
                'unit' => 'Pcs',
                'description' => 'Sapu lidi untuk kebersihan area rusun'
            ],
            [
                'item_code' => 'BRG-002',
                'name' => 'Pel Lantai',
                'photo' => 'items/default.jpg',
                'unit_price' => 35000,
                'stock' => 8,
                'min_stock' => 15,
                'unit' => 'Pcs',
                'description' => 'Pel lantai dengan gagang stainless'
            ],
            [
                'item_code' => 'BRG-003',
                'name' => 'Kain Lap',
                'photo' => 'items/default.jpg',
                'unit_price' => 5000,
                'stock' => 50,
                'min_stock' => 20,
                'unit' => 'Pcs',
                'description' => 'Kain lap microfiber ukuran 30x30 cm'
            ],
            [
                'item_code' => 'BRG-004',
                'name' => 'Sabun Cuci Piring',
                'photo' => 'items/default.jpg',
                'unit_price' => 12000,
                'stock' => 30,
                'min_stock' => 25,
                'unit' => 'Botol',
                'description' => 'Sabun cuci piring cair 800ml'
            ],
            [
                'item_code' => 'BRG-005',
                'name' => 'Ember Plastik',
                'photo' => 'items/default.jpg',
                'unit_price' => 25000,
                'stock' => 5,
                'min_stock' => 8,
                'unit' => 'Pcs',
                'description' => 'Ember plastik kapasitas 10 liter'
            ],
            [
                'item_code' => 'BRG-006',
                'name' => 'Pengki Sampah',
                'photo' => 'items/default.jpg',
                'unit_price' => 18000,
                'stock' => 12,
                'min_stock' => 10,
                'unit' => 'Pcs',
                'description' => 'Pengki sampah plastik dengan gagang'
            ],
            [
                'item_code' => 'BRG-007',
                'name' => 'Sikat WC',
                'photo' => 'items/default.jpg',
                'unit_price' => 22000,
                'stock' => 3,
                'min_stock' => 10,
                'unit' => 'Pcs',
                'description' => 'Sikat WC dengan tempat holder'
            ],
            [
                'item_code' => 'BRG-008',
                'name' => 'Pembersih Lantai',
                'photo' => 'items/default.jpg',
                'unit_price' => 20000,
                'stock' => 15,
                'min_stock' => 10,
                'unit' => 'Botol',
                'description' => 'Cairan pembersih lantai 1 liter'
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }

        $this->command->info('Seeder items berhasil dijalankan! 8 barang ditambahkan.');
        $this->command->info('⚠️ Perhatian: Beberapa barang memiliki stok di bawah minimum (akan muncul alert)');
    }
}
