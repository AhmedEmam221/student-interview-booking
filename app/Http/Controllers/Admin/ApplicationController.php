<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\BoardMember;
use App\Models\ScheduleSlot;
use App\Models\Track;
use App\Services\BookingService;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request)
    {
        $query = Application::with(['track', 'scheduleSlot', 'interviewBoardMember', 'referralBoardMember', 'strengths', 'weaknesses']);

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
        
        $tracks = Track::active()->orderBy('name')->get();
        $statuses = ['Pending', 'Accepted', 'Waiting', 'Rejected'];

        return view('admin.applications.index', compact('applications', 'tracks', 'statuses'));
    }

    public function show(Application $application)
    {
        $application->load(['track', 'scheduleSlot', 'interviewBoardMember', 'referralBoardMember', 'strengths', 'weaknesses']);
        return view('admin.applications.show', compact('application'));
    }

    public function edit(Application $application)
    {
        $application->load(['track', 'scheduleSlot', 'interviewBoardMember', 'referralBoardMember', 'strengths', 'weaknesses']);
        $tracks = Track::active()->orderBy('name')->get();
        $boardMembers = BoardMember::active()->orderBy('name')->get();
        $slots = ScheduleSlot::where('track_id', $application->track_id)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->where('is_active', true)
            ->with('boardMembers')
            ->get();
        $statuses = ['Pending', 'Accepted', 'Waiting', 'Rejected'];

        return view('admin.applications.edit', compact('application', 'tracks', 'boardMembers', 'slots', 'statuses'));
    }

    public function update(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|in:Pending,Accepted,Waiting,Rejected',
            'interview_board_member_id' => 'nullable|exists:board_members,id',
            'schedule_slot_id' => 'nullable|exists:schedule_slots,id',
        ]);

        // If changing slot, verify availability
        if ($request->filled('schedule_slot_id') && $request->schedule_slot_id != $application->schedule_slot_id) {
            $newSlot = ScheduleSlot::with('boardMembers')->find($request->schedule_slot_id);
            if (!$newSlot || !$newSlot->is_active || $newSlot->boardMembers->isEmpty()) {
                return back()->withErrors(['schedule_slot_id' => 'Selected slot is not available.']);
            }
            
            if ($newSlot->isFull()) {
                return back()->withErrors(['schedule_slot_id' => 'Selected slot is full.']);
            }
        }

        $application->update($request->only(['status', 'interview_board_member_id', 'schedule_slot_id']));

        return redirect()->route('admin.applications.index')
            ->with('success', 'Application updated successfully.');
    }

    public function destroy(Application $application)
    {
        // Soft delete - just cancel
        $application->update(['status' => 'Cancelled']);
        return redirect()->route('admin.applications.index')
            ->with('success', 'Application cancelled successfully.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
            'status' => 'required|in:Pending,Accepted,Waiting,Rejected',
        ]);

        Application::whereIn('id', $request->application_ids)
            ->update(['status' => $request->status]);

        return back()->with('success', 'Applications updated successfully.');
    }
}