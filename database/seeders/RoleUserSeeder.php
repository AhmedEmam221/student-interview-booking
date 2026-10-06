<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | High Board Accounts
        |--------------------------------------------------------------------------
        */

        User::create([
            'name' => 'High Board Technical',
            'email' => 'TechnicalHighBoard@hultprizesams.tech',
            'password' => Hash::make('TechHighBoard@HultPrizeSams2027'),
            'role' => 'highboard',
            'track_id' => 1,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'High Board Operations',
            'email' => 'OperationHighBoard@hultprizesams.tech',
            'password' => Hash::make('Ops@HultPrizeSams2027'),
            'role' => 'highboard',
            'track_id' => 2,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Marketing Board',
            'email' => 'Marketing@hultprizesams.tech',
            'password' => Hash::make('Marketing@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 16,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Media Board',
            'email' => 'Media@hultprizesams.tech',
            'password' => Hash::make('Media@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 13,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Events Board',
            'email' => 'Events@hultprizesams.tech',
            'password' => Hash::make('Events@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 14,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'HR Board',
            'email' => 'Hr@hultprizesams.tech',
            'password' => Hash::make('Hr@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 15,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Competition Board',
            'email' => 'Competition@hultprizesams.tech',
            'password' => Hash::make('Competition@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 17,
            'is_active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Technical Track Accounts
        |--------------------------------------------------------------------------
        */

        User::create([
            'name' => 'C++ Board',
            'email' => 'Cplusplus@HultPrizeSams2027',
            'password' => Hash::make('Cplusplus@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 8,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'UI/UX Board',
            'email' => 'UIUX@HultPrizeSams2027',
            'password' => Hash::make('UIUX@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 6,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Flutter Board',
            'email' => 'Flutter@HultPrizeSams2027',
            'password' => Hash::make('Flutter@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 11,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Front-End Board',
            'email' => 'FrontEnd@HultPrizeSams2027',
            'password' => Hash::make('FrontEnd@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 3,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Back-End Board',
            'email' => 'BackEnd@HultPrizeSams2027',
            'password' => Hash::make('BackEnd@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 4,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Network Board',
            'email' => 'Network@HultPrizeSams2027',
            'password' => Hash::make('Network@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 12,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Database Board',
            'email' => 'Database@HultPrizeSams2027',
            'password' => Hash::make('Database@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 10,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Python Board',
            'email' => 'Python@HultPrizeSams2027',
            'password' => Hash::make('Python@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 7,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Cybersecurity Board',
            'email' => 'CyberSecurity@HultPrizeSams2027',
            'password' => Hash::make('CyberSec@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 5,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Software Testing Board',
            'email' => 'SoftwareTesting@HultPrizeSams2027',
            'password' => Hash::make('SoftwareTesting@HultPrizeSams2027'),
            'role' => 'board',
            'track_id' => 9,
            'is_active' => true,
        ]);
    }
}