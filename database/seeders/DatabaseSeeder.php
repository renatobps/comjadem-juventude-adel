<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@conjadem.local');
        $password = env('ADMIN_PASSWORD', 'admin123456');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrador',
                'password' => Hash::make($password),
                'is_admin' => true,
            ]
        );
    }
}
