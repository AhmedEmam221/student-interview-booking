<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Track;

class TrackSeeder extends Seeder
{
    public function run()
    {
        // Main tracks
        $technical = Track::create([
            'name' => 'Technical',
            'description' => 'Technical track for CS, Engineering, and related fields',
            'is_active' => true,
        ]);

        $nonTechnical = Track::create([
            'name' => 'Non-Technical',
            'description' => 'Non-technical track for Business, Arts, and Humanities',
            'is_active' => true,
        ]);

        // Sub-tracks for Technical
        $technicalSubTracks = [
            ['name' => 'Software Development', 'description' => 'Web, Mobile, Desktop development', 'parent_id' => $technical->id],
            ['name' => 'Data Science & AI', 'description' => 'Machine Learning, Data Analysis, AI', 'parent_id' => $technical->id],
            ['name' => 'Cybersecurity', 'description' => 'Network Security, Ethical Hacking', 'parent_id' => $technical->id],
            ['name' => 'DevOps & Cloud', 'description' => 'CI/CD, Cloud Infrastructure, Automation', 'parent_id' => $technical->id],
        ];

        // Sub-tracks for Non-Technical
        $nonTechnicalSubTracks = [
            ['name' => 'Business & Management', 'description' => 'Business Administration, Project Management', 'parent_id' => $nonTechnical->id],
            ['name' => 'Marketing & Sales', 'description' => 'Digital Marketing, Sales, Brand Management', 'parent_id' => $nonTechnical->id],
            ['name' => 'HR & Recruitment', 'description' => 'Human Resources, Talent Acquisition', 'parent_id' => $nonTechnical->id],
            ['name' => 'Design & Creative', 'description' => 'UI/UX, Graphic Design, Creative Arts', 'parent_id' => $nonTechnical->id],
        ];

        foreach ($technicalSubTracks as $track) {
            Track::create(array_merge($track, ['is_active' => true]));
        }

        foreach ($nonTechnicalSubTracks as $track) {
            Track::create(array_merge($track, ['is_active' => true]));
        }
    }
}