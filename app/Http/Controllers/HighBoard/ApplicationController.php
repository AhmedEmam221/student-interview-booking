<?php

namespace App\Http\Controllers\HighBoard;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\BoardMember;
use App\Models\ScheduleSlot;
use App\Models\Track;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('track.access');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $userTrack = $request->userTrack ?? $user->track;

        if (!$userTrack || $userTrack->parent_id !== null) {
            abort(403, 'High Board must be assigned to a main track.');
        }

        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        $query = Application::with(['track', 'scheduleSlot', 'interviewBoardMember', 'referralBoardMember', 'strengths', 'weaknesses'])
            ->whereIn('track_id', $allTrackIds);

        // Filters
        if ($request->filled('track_id')) {
            $query->where('track_id', $request->track_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereHas('scheduleSlot', function ($q) use ($request) {
                $q->where('date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('scheduleSlot', function ($q) use ($request) {
                $q->where('date', '<=', $request->date_to);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('application_number', 'like', "%{$search}%");
            });
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        $tracks = Track::whereIn('id', $allTrackIds)->orderBy('name')->get();
        $statuses = ['Pending', 'Accepted', 'Waiting', 'Rejected'];

        return view('highboard.applications.index', compact('applications', 'tracks', 'statuses', 'userTrack'));
    }

    public function show(Application $application)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        // Verify the application belongs to this user's track or subtracks
        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        if (!in_array($application->track_id, $allTrackIds)) {
            abort(403, 'Unauthorized access to this application.');
        }

        $application->load(['track', 'scheduleSlot', 'interviewBoardMember', 'referralBoardMember', 'strengths', 'weaknesses']);
        return view('highboard.applications.show', compact('application'));
    }

    public function edit(Application $application)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        if (!in_array($application->track_id, $allTrackIds)) {
            abort(403, 'Unauthorized access to this application.');
        }

        $application->load(['track', 'scheduleSlot', 'interviewBoardMember', 'referralBoardMember', 'strengths', 'weaknesses']);
        
        // Show slots for all accessible tracks
        $slots = ScheduleSlot::whereIn('track_id', $allTrackIds)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->where('is_active', true)
            ->with('boardMembers')
            ->get();
            
        $statuses = ['Pending', 'Accepted', 'Waiting', 'Rejected'];

        return view('highboard.applications.edit', compact('application', 'slots', 'statuses', 'userTrack'));
    }

    public function update(Request $request, Application $application)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        if (!in_array($application->track_id, $allTrackIds)) {
            abort(403, 'Unauthorized access to this application.');
        }

        $request->validate([
            'status' => 'required|in:Pending,Accepted,Waiting,Rejected',
            'interview_board_member_id' => 'nullable|exists:board_members,id',
            'schedule_slot_id' => 'nullable|exists:schedule_slots,id',
        ]);

        // If changing slot, verify it belongs to accessible tracks
        if ($request->filled('schedule_slot_id') && $request->schedule_slot_id != $application->schedule_slot_id) {
            $newSlot = ScheduleSlot::with('boardMembers')->find($request->schedule_slot_id);
            if (!$newSlot || !$newSlot->is_active || $newSlot->boardMembers->isEmpty()) {
                return back()->withErrors(['schedule_slot_id' => 'Selected slot is not available.']);
            }
            
            if (!in_array($newSlot->track_id, $allTrackIds)) {
                return back()->withErrors(['schedule_slot_id' => 'Selected slot does not belong to your tracks.']);
            }
            
            if ($newSlot->isFull()) {
                return back()->withErrors(['schedule_slot_id' => 'Selected slot is full.']);
            }
        }

        $application->update($request->only(['status', 'interview_board_member_id', 'schedule_slot_id']));

        return redirect()->route('highboard.applications.index')
            ->with('success', 'Application updated successfully.');
    }
}