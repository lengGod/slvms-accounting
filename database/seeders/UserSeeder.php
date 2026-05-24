<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Sarana',
            'email' => 'admin@slv.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Accounting Sarana',
            'email' => 'accounting@slv.com',
            'password' => Hash::make('password'),
            'role' => 'accounting',
        ]);
    }
}
