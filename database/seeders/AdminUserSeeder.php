<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@hultprizesams.tech',
            'password' => Hash::make('HultPrize@2027'),
            'is_admin' => true,
        ]);
    }
}
