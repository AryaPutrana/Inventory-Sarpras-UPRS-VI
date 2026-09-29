<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default user for Petugas Sarpras
        User::create([
            'name' => 'Petugas Sarpras',
            'email' => 'petugas@sarpras.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
