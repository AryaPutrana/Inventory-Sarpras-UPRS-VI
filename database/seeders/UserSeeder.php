<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * SECURITY WARNING: Jangan gunakan seeder ini di production!
     * Buat user pertama secara manual via: php artisan tinker
     *
     * Contoh manual:
     * User::create([
     *     'name' => 'Admin Sarpras',
     *     'email' => 'admin@sarpras.com',
     *     'password' => Hash::make('YourSecurePassword')
     * ]);
     */
    public function run(): void
    {
        // Jangan create user default dengan password hardcoded
        // User pertama harus dibuat manual untuk keamanan

        // HANYA untuk development/testing:
        if (app()->environment('local', 'testing')) {
            $randomPassword = 'Dev'.now()->format('Ymd').rand(1000, 9999);

            $user = User::create([
                'name' => 'Petugas Sarpras (DEV)',
                'email' => 'petugas@sarpras.com',
                'password' => Hash::make($randomPassword),
            ]);

            // Guard: $this->command bisa null jika dipanggil programmatically
            if ($this->command) {
                $this->command->warn('═══════════════════════════════════════');
                $this->command->warn('  DEVELOPMENT USER CREATED');
                $this->command->warn('═══════════════════════════════════════');
                $this->command->info('Email: '.$user->email);
                $this->command->info('Password: '.$randomPassword);
                $this->command->warn('═══════════════════════════════════════');
                $this->command->error('SECURITY: Jangan gunakan seeder ini di production!');
                $this->command->warn('═══════════════════════════════════════');
            }
        }
    }
}
