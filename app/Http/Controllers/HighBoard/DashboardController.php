<?php

namespace App\Http\Controllers\HighBoard;

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

        if (!$userTrack || $userTrack->parent_id !== null) {
            abort(403, 'High Board must be assigned to a main track.');
        }

        // Get all subtrack IDs
        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        // Total applications for all tracks
        $totalApplications = Application::whereIn('track_id', $allTrackIds)->count();
        $pendingApplications = Application::whereIn('track_id', $allTrackIds)->where('status', 'Pending')->count();
        $acceptedApplications = Application::whereIn('track_id', $allTrackIds)->where('status', 'Accepted')->count();
        $waitingApplications = Application::whereIn('track_id', $allTrackIds)->where('status', 'Waiting')->count();
        $rejectedApplications = Application::whereIn('track_id', $allTrackIds)->where('status', 'Rejected')->count();

        // Applications per track
        $applicationsPerTrack = Track::whereIn('id', $allTrackIds)
            ->withCount(['applications as total_applications' => function ($query) {
                $query->whereIn('status', ['Pending', 'Accepted', 'Waiting', 'Rejected']);
            }])
            ->get()
            ->map(function ($track) {
                return [
                    'name' => $track->name,
                    'count' => $track->total_applications,
                ];
            });

        // Applications per day (last 7 days)
        $applicationsPerDay = Application::whereIn('track_id', $allTrackIds)
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

        // Board members in this track and subtracks
        $boardMembers = BoardMember::whereHas('tracks', function ($q) use ($allTrackIds) {
                $q->whereIn('tracks.id', $allTrackIds);
            })
            ->where('is_active', true)
            ->withCount(['interviewApplications as interview_count' => function ($query) use ($allTrackIds) {
                $query->whereIn('track_id', $allTrackIds)
                      ->whereIn('status', ['Pending', 'Accepted', 'Waiting', 'Rejected']);
            }])
            ->withCount(['referralApplications as referral_count' => function ($query) use ($allTrackIds) {
                $query->whereIn('track_id', $allTrackIds);
            }])
            ->get();

        // Available/Full slots for next 7 days
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);
        
        $slots = ScheduleSlot::whereIn('track_id', $allTrackIds)
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

        return view('highboard.dashboard', compact(
            'userTrack',
            'subTrackIds',
            'allTrackIds',
            'totalApplications',
            'pendingApplications',
            'acceptedApplications',
            'waitingApplications',
            'rejectedApplications',
            'applicationsPerTrack',
            'applicationsPerDay',
            'boardMembers',
            'availableSlots',
            'fullSlots'
        ));
    }
}