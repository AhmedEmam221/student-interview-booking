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
            [
                'name' => 'Front-End',
                'description' => 'Web interfaces, responsive design, HTML, CSS, JavaScript, and modern front-end frameworks.',
                'parent_id' => $technical->id
            ],
            [
                'name' => 'Back-End',
                'description' => 'Server-side development, APIs, databases, authentication, and backend frameworks.',
                'parent_id' => $technical->id
            ],
            [
                'name' => 'Cybersecurity',
                'description' => 'Network security, ethical hacking, vulnerability assessment, and cybersecurity practices.',
                'parent_id' => $technical->id
            ],
            [
                'name' => 'UI/UX',
                'description' => 'User interface design, user experience, wireframing, prototyping, and usability principles.',
                'parent_id' => $technical->id
            ],
            [
                'name' => 'Python',
                'description' => 'Python programming, automation, scripting, data analysis, and application development.',
                'parent_id' => $technical->id
            ],
            [
                'name' => 'C++',
                'description' => 'C++ programming, object-oriented programming, algorithms, data structures, and software development.',
                'parent_id' => $technical->id
            ],
            [
                'name' => 'Software Testing',
                'description' => 'Software quality assurance, manual and automated testing, test cases, debugging, and quality control.',
                'parent_id' => $technical->id
            ],
            [
                'name' => 'Database',
                'description' => 'Database design, SQL, data modeling, database management, optimization, and data integrity.',
                'parent_id' => $technical->id
            ],
            [
                'name' => 'Flutter',
                'description' => 'Cross-platform mobile application development using Flutter and Dart for Android and iOS.',
                'parent_id' => $technical->id
            ],
            [
                'name' => 'Network',
                'description' => 'Computer networks, network infrastructure, protocols, configuration, troubleshooting, and network administration.',
                'parent_id' => $technical->id
            ],
        ];
        
        // Sub-tracks for Non-Technical
        $nonTechnicalSubTracks = [
            [
                'name' => 'Media and Photography',
                'description' => 'Photography, videography, content creation, visual storytelling, and media production.',
                'parent_id' => $nonTechnical->id
            ],
            [
                'name' => 'Events Management',
                'description' => 'Event planning, organization, logistics, coordination, budgeting, and event execution.',
                'parent_id' => $nonTechnical->id
            ],
            [
                'name' => 'HR',
                'description' => 'Human resources, recruitment, talent acquisition, employee relations, and people management.',
                'parent_id' => $nonTechnical->id
            ],
            [
                'name' => 'Marketing',
                'description' => 'Digital marketing, social media, branding, market research, content strategy, and campaign management.',
                'parent_id' => $nonTechnical->id
            ],
            [
                'name' => 'Competition',
                'description' => 'Competition planning, participant coordination, challenge design, judging, and competition management.',
                'parent_id' => $nonTechnical->id
            ],
        ];

        foreach ($technicalSubTracks as $track) {
            Track::create(array_merge($track, ['is_active' => true]));
        }

        foreach ($nonTechnicalSubTracks as $track) {
            Track::create(array_merge($track, ['is_active' => true]));
        }
    }
}
