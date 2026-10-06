<?php

namespace App\Http\Controllers\Board;

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
        $this->middleware('track.access');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $userTrack = $request->userTrack ?? $user->track;

        if (!$userTrack) {
            abort(403, 'No track assigned to your account.');
        }

        // Total applications for this track
        $totalApplications = Application::where('track_id', $userTrack->id)->count();
        $pendingApplications = Application::where('track_id', $userTrack->id)->where('status', 'Pending')->count();
        $acceptedApplications = Application::where('track_id', $userTrack->id)->where('status', 'Accepted')->count();
        $waitingApplications = Application::where('track_id', $userTrack->id)->where('status', 'Waiting')->count();
        $rejectedApplications = Application::where('track_id', $userTrack->id)->where('status', 'Rejected')->count();

        // Applications per day (last 7 days)
        $applicationsPerDay = Application::where('track_id', $userTrack->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
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

        // Board members in this track
        $boardMembers = BoardMember::whereHas('tracks', function ($q) use ($userTrack) {
                $q->where('tracks.id', $userTrack->id);
            })
            ->where('is_active', true)
            ->withCount(['interviewApplications as interview_count' => function ($q) use ($userTrack) {
                $q->where('track_id', $userTrack->id)
                  ->whereIn('status', ['Pending', 'Accepted', 'Waiting', 'Rejected']);
            }])
            ->withCount(['referralApplications as referral_count' => function ($q) use ($userTrack) {
                $q->where('track_id', $userTrack->id);
            }])
            ->get();

        // Available/Full slots for next 7 days
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);
        
        $slots = ScheduleSlot::where('track_id', $userTrack->id)
            ->whereBetween('date', [$today, $nextWeek])
            ->where('is_active', true)
            ->with('boardMembers')
            ->get();

        $availableSlots = $slots->filter(function ($slot) {
            return $slot->available_slots > 0;
        })->count();

        $fullSlots = $slots->filter(function ($slot) {
            return $slot->available_slots <= 0;
        })->count();

        return view('board.dashboard', compact(
            'userTrack',
            'totalApplications',
            'pendingApplications',
            'acceptedApplications',
            'waitingApplications',
            'rejectedApplications',
            'applicationsPerDay',
            'boardMembers',
            'availableSlots',
            'fullSlots'
        ));
    }
}