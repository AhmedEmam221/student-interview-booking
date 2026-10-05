<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Strength;

class StrengthSeeder extends Seeder
{
    public function run()
    {
        $strengths = [
            ['name' => 'Time Management', 'description' => 'Ability to manage time effectively', 'is_active' => true],
            ['name' => 'Public Speaking', 'description' => 'Comfortable speaking in front of audiences', 'is_active' => true],
            ['name' => 'Team Work', 'description' => 'Works well in team environments', 'is_active' => true],
            ['name' => 'Leadership', 'description' => 'Demonstrates leadership qualities', 'is_active' => true],
            ['name' => 'Self-Confidence', 'description' => 'Confident in abilities and decisions', 'is_active' => true],
            ['name' => 'Flexibility', 'description' => 'Adaptable to changing situations', 'is_active' => true],
            ['name' => 'Creativity', 'description' => 'Thinks creatively and innovatively', 'is_active' => true],
        ];

        foreach ($strengths as $strength) {
            Strength::create($strength);
        }
    }
}