<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoardMember;
use App\Models\Track;

class BoardMemberSeeder extends Seeder
{
    public function run()
    {
        $technicalTrack = Track::where('name', 'Technical')->first();
        $nonTechnicalTrack = Track::where('name', 'Non-Technical')->first();

        $members = [
            [
                'name' => 'Ahmed Mohamed',
                'email' => 'ahmed.mohamed@example.com',
                'phone' => '+201000000001',
                'bio' => 'Senior Software Engineer with 10+ years experience',
                'is_active' => true,
                'tracks' => $technicalTrack ? [$technicalTrack->id] : [],
            ],
            [
                'name' => 'Sara Ali',
                'email' => 'sara.ali@example.com',
                'phone' => '+201000000002',
                'bio' => 'HR Manager specializing in technical recruitment',
                'is_active' => true,
                'tracks' => $technicalTrack ? [$technicalTrack->id] : [],
            ],
            [
                'name' => 'Mohamed Hassan',
                'email' => 'mohamed.hassan@example.com',
                'phone' => '+201000000003',
                'bio' => 'Business Development Manager',
                'is_active' => true,
                'tracks' => $nonTechnicalTrack ? [$nonTechnicalTrack->id] : [],
            ],
            [
                'name' => 'Fatima Omar',
                'email' => 'fatima.omar@example.com',
                'phone' => '+201000000004',
                'bio' => 'Marketing Director with startup experience',
                'is_active' => true,
                'tracks' => $nonTechnicalTrack ? [$nonTechnicalTrack->id] : [],
            ],
        ];

        foreach ($members as $member) {
            $trackIds = $member['tracks'];
            unset($member['tracks']);
            
            $member['referral_code'] = BoardMember::generateReferralCode();
            $boardMember = BoardMember::create($member);
            $boardMember->tracks()->sync($trackIds);
        }
    }
}