<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Weakness;

class WeaknessSeeder extends Seeder
{
    public function run()
    {
        $weaknesses = [
            ['name' => 'Can\'t work under pressure', 'description' => 'Struggles with high-pressure situations', 'is_active' => true],
            ['name' => 'Low Problem Solving', 'description' => 'Difficulty solving complex problems', 'is_active' => true],
            ['name' => 'Aggressive', 'description' => 'Can be aggressive in communication', 'is_active' => true],
            ['name' => 'Adapt to Sudden Change', 'description' => 'Struggles with sudden changes', 'is_active' => true],
            ['name' => 'Overthinking', 'description' => 'Tends to overthink decisions', 'is_active' => true],
            ['name' => 'Difficulty saying no', 'description' => 'Finds it hard to decline requests', 'is_active' => true],
            ['name' => 'Perfectionist', 'description' => 'Strives for perfection, sometimes to a fault', 'is_active' => true],
        ];

        foreach ($weaknesses as $weakness) {
            Weakness::create($weakness);
        }
    }
}