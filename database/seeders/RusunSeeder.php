<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rusun;

class RusunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rusunData = [
            ['code' => 'RABEK', 'name' => 'Rawa Bebek'],
            ['code' => 'UMEN', 'name' => 'Umen'],
            ['code' => 'TIPAR', 'name' => 'Tipar'],
            ['code' => 'ALBO', 'name' => 'Albo'],
            ['code' => 'CBT', 'name' => 'CBT'],
            ['code' => 'KM2', 'name' => 'KM2'],
        ];

        foreach ($rusunData as $rusun) {
            Rusun::create($rusun);
        }
    }
}
