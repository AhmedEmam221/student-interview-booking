<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\BoardMember;
use App\Models\ScheduleSlot;
use App\Models\Track;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    public function index()
    {
        $totalApplications = Application::count();
        $pendingApplications = Application::pending()->count();
        $confirmedApplications = 0 ;
        $completedApplications = 0 ;

        // Applications per track
        $applicationsPerTrack = Track::withCount(['applications as total_applications' => function ($query) {
            $query->whereIn('status', ['Pending', 'Confirmed', 'Completed']);
        }])->get()->map(function ($track) {
            return [
                'name' => $track->name,
                'count' => $track->total_applications,
            ];
        });

        // Applications per day (last 7 days)
        $applicationsPerDay = Application::where('created_at', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                ];
            });

        // Applications per board member (interview)
        $applicationsPerBoardMember = BoardMember::withCount(['interviewApplications as interview_count' => function ($query) {
            $query->whereIn('status', ['Pending', 'Confirmed', 'Completed']);
        }])->get()->map(function ($member) {
            return [
                'name' => $member->name,
                'count' => $member->interview_count,
            ];
        });

        // Referral applications per board member
        $referralPerBoardMember = BoardMember::withCount(['referralApplications as referral_count'])->get()->map(function ($member) {
            return [
                'name' => $member->name,
                'count' => $member->referral_count,
            ];
        });

        // Available/Full slots for next 7 days
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);
        
        $slots = ScheduleSlot::whereBetween('date', [$today, $nextWeek])
            ->where('is_active', true)
            ->with('boardMembers')
            ->get();

        $availableSlots = $slots->filter(function ($slot) {
            return $slot->available_slots > 0;
        })->count();

        $fullSlots = $slots->filter(function ($slot) {
            return $slot->available_slots <= 0;
        })->count();

        return view('admin.dashboard', compact(
            'totalApplications',
            'pendingApplications',
            'confirmedApplications',
            'completedApplications',
            'applicationsPerTrack',
            'applicationsPerDay',
            'applicationsPerBoardMember',
            'referralPerBoardMember',
            'availableSlots',
            'fullSlots'
        ));
    }
}