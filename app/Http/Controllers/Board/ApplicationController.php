<?php

namespace App\Http\Controllers\Board;

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
        $userTrack = $request->userTrack ?? auth()->user()->track;

        $query = Application::with(['track', 'scheduleSlot', 'interviewBoardMember', 'referralBoardMember', 'strengths', 'weaknesses'])
            ->where('track_id', $userTrack->id);

        // Filters
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
        
        $statuses = ['Pending', 'Accepted', 'Waiting', 'Rejected'];

        return view('board.applications.index', compact('applications', 'statuses', 'userTrack'));
    }

    public function show(Application $application)
    {
        $userTrack = auth()->user()->track;
        
        // Verify the application belongs to this user's track
        if ($application->track_id != $userTrack->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        $application->load(['track', 'scheduleSlot', 'interviewBoardMember', 'referralBoardMember', 'strengths', 'weaknesses']);
        return view('board.applications.show', compact('application'));
    }

    public function edit(Application $application)
    {
        $userTrack = auth()->user()->track;
        
        // Verify the application belongs to this user's track
        if ($application->track_id != $userTrack->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        $application->load(['track', 'scheduleSlot', 'interviewBoardMember', 'referralBoardMember', 'strengths', 'weaknesses']);
        
        // Only show slots for this track
        $slots = ScheduleSlot::where('track_id', $userTrack->id)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->where('is_active', true)
            ->with('boardMembers')
            ->get();
            
        $statuses = ['Pending', 'Accepted', 'Waiting', 'Rejected'];

        return view('board.applications.edit', compact('application', 'slots', 'statuses', 'userTrack'));
    }

    public function update(Request $request, Application $application)
    {
        $userTrack = auth()->user()->track;
        
        // Verify the application belongs to this user's track
        if ($application->track_id != $userTrack->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        $request->validate([
            'status' => 'required|in:Pending,Accepted,Waiting,Rejected',
            'interview_board_member_id' => 'nullable|exists:board_members,id',
            'schedule_slot_id' => 'nullable|exists:schedule_slots,id',
        ]);

        // If changing slot, verify it belongs to this track
        if ($request->filled('schedule_slot_id') && $request->schedule_slot_id != $application->schedule_slot_id) {
            $newSlot = ScheduleSlot::with('boardMembers')->find($request->schedule_slot_id);
            if (!$newSlot || !$newSlot->is_active || $newSlot->boardMembers->isEmpty()) {
                return back()->withErrors(['schedule_slot_id' => 'Selected slot is not available.']);
            }
            
            if ($newSlot->track_id != $userTrack->id) {
                return back()->withErrors(['schedule_slot_id' => 'Selected slot does not belong to your track.']);
            }
            
            if ($newSlot->isFull()) {
                return back()->withErrors(['schedule_slot_id' => 'Selected slot is full.']);
            }
        }

        $application->update($request->only(['status', 'interview_board_member_id', 'schedule_slot_id']));

        return redirect()->route('board.applications.index')
            ->with('success', 'Application updated successfully.');
    }
}