# CARA GANTI PASSWORD USER SECARA MANUAL

## Via Tinker (Recommended)

```bash
php artisan tinker
```

Lalu jalankan:

```php
// Cari user berdasarkan email
$user = App\Models\User::where('email', 'ary27061@gmail.com')->first();

// Ganti password
$user->password = Hash::make('password_baru_anda');
$user->save();

echo "Password berhasil diubah!";
exit;
```

## One-Liner (Cepat)

```bash
php artisan tinker --execute="
\$user = App\Models\User::where('email', 'ary27061@gmail.com')->first();
\$user->password = Hash::make('password_baru');
\$user->save();
echo 'Password berhasil diubah untuk: ' . \$user->email;
"
```

## Ganti Password Semua User

```bash
php artisan tinker --execute="
App\Models\User::all()->each(function(\$user) {
    \$user->password = Hash::make('default123');
    \$user->save();
    echo 'Updated: ' . \$user->email . PHP_EOL;
});
"
```

## Buat User Baru

```bash
php artisan tinker --execute="
\$user = App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('admin123'),
]);
echo 'User baru berhasil dibuat: ' . \$user->email;
"
```

## Tips

- Password akan otomatis di-hash dengan bcrypt
- User tidak perlu tahu password di-hash seperti apa
- Simpan password default di KREDENSIAL_LOGIN.txt (sudah ada)

---

**CATATAN**: Fitur "Lupa Password" sudah dihapus. 
Ganti password hanya bisa dilakukan manual via code.
