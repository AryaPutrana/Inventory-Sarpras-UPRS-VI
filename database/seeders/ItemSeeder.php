<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan direktori storage ada
        if (! Storage::exists('public/items')) {
            Storage::makeDirectory('public/items');
        }

        $items = [
            [
                'item_code' => 'BRG-001',
                'name' => 'Sapu Lidi',
                'photo' => 'default.jpg',
                'unit_price' => 15000,
                'stock' => 25,
                'min_stock' => 10,
                'unit' => 'pcs',
                'description' => 'Sapu lidi untuk kebersihan area rusun',
            ],
            [
                'item_code' => 'BRG-002',
                'name' => 'Pel Lantai',
                'photo' => 'default.jpg',
                'unit_price' => 35000,
                'stock' => 8,
                'min_stock' => 15,
                'unit' => 'pcs',
                'description' => 'Pel lantai dengan gagang stainless',
            ],
            [
                'item_code' => 'BRG-003',
                'name' => 'Kain Lap',
                'photo' => 'default.jpg',
                'unit_price' => 5000,
                'stock' => 50,
                'min_stock' => 20,
                'unit' => 'pcs',
                'description' => 'Kain lap microfiber ukuran 30x30 cm',
            ],
            [
                'item_code' => 'BRG-004',
                'name' => 'Sabun Cuci Piring',
                'photo' => 'default.jpg',
                'unit_price' => 12000,
                'stock' => 30,
                'min_stock' => 25,
                'unit' => 'botol',
                'description' => 'Sabun cuci piring cair 800ml',
            ],
            [
                'item_code' => 'BRG-005',
                'name' => 'Ember Plastik',
                'photo' => 'default.jpg',
                'unit_price' => 25000,
                'stock' => 5,
                'min_stock' => 8,
                'unit' => 'pcs',
                'description' => 'Ember plastik kapasitas 10 liter',
            ],
            [
                'item_code' => 'BRG-006',
                'name' => 'Pengki Sampah',
                'photo' => 'default.jpg',
                'unit_price' => 18000,
                'stock' => 12,
                'min_stock' => 10,
                'unit' => 'pcs',
                'description' => 'Pengki sampah plastik dengan gagang',
            ],
            [
                'item_code' => 'BRG-007',
                'name' => 'Sikat WC',
                'photo' => 'default.jpg',
                'unit_price' => 22000,
                'stock' => 3,
                'min_stock' => 10,
                'unit' => 'pcs',
                'description' => 'Sikat WC dengan tempat holder',
            ],
            [
                'item_code' => 'BRG-008',
                'name' => 'Pembersih Lantai',
                'photo' => 'default.jpg',
                'unit_price' => 20000,
                'stock' => 15,
                'min_stock' => 10,
                'unit' => 'botol',
                'description' => 'Cairan pembersih lantai 1 liter',
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }

        $this->command->info('Seeder items berhasil dijalankan! 8 barang ditambahkan.');
        $this->command->info('⚠️ Perhatian: Beberapa barang memiliki stok di bawah minimum (akan muncul alert)');
    }
}
