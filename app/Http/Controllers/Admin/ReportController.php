<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Track;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InterviewSheetExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tracks = Track::active()->orderBy('name')->get();
        
        $query = Application::with([
            'track', 
            'scheduleSlot', 
            'interviewBoardMember', 
            'referralBoardMember', 
            'strengths', 
            'weaknesses'
        ]);

        // Filters
        if ($request->filled('track_id')) {
            $query->where('track_id', $request->track_id);
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

        if ($request->filled('time_from')) {
            $query->whereHas('scheduleSlot', function ($q) use ($request) {
                $q->where('start_time', '>=', $request->time_from);
            });
        }

        if ($request->filled('time_to')) {
            $query->whereHas('scheduleSlot', function ($q) use ($request) {
                $q->where('start_time', '<=', $request->time_to);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query
            ->join('schedule_slots', 'applications.schedule_slot_id', '=', 'schedule_slots.id')
            ->orderBy('schedule_slots.date', 'asc')
            ->orderBy('schedule_slots.start_time', 'asc')
            ->select('applications.*')
            ->get();

        $statuses = ['Pending', 'Accepted', 'Waiting', 'Rejected'];

        return view('admin.reports.index', compact('applications', 'tracks', 'statuses'));
    }

    public function print(Request $request)
    {
        $tracks = Track::active()->orderBy('name')->get();
        
        $query = Application::with([
            'track', 
            'scheduleSlot', 
            'interviewBoardMember', 
            'referralBoardMember', 
            'strengths', 
            'weaknesses'
        ]);

        // Filters
        if ($request->filled('track_id')) {
            $query->where('track_id', $request->track_id);
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

        if ($request->filled('time_from')) {
            $query->whereHas('scheduleSlot', function ($q) use ($request) {
                $q->where('start_time', '>=', $request->time_from);
            });
        }

        if ($request->filled('time_to')) {
            $query->whereHas('scheduleSlot', function ($q) use ($request) {
                $q->where('start_time', '<=', $request->time_to);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query
            ->join('schedule_slots', 'applications.schedule_slot_id', '=', 'schedule_slots.id')
            ->orderBy('schedule_slots.date', 'asc')
            ->orderBy('schedule_slots.start_time', 'asc')
            ->select('applications.*')
            ->get();

        return view('admin.reports.print', compact('applications'));
    }

    public function export(Request $request)
    {
        $query = Application::with([
            'track', 
            'scheduleSlot', 
            'interviewBoardMember', 
            'referralBoardMember', 
            'strengths', 
            'weaknesses'
        ]);

        // Filters
        if ($request->filled('track_id')) {
            $query->where('track_id', $request->track_id);
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

        if ($request->filled('time_from')) {
            $query->whereHas('scheduleSlot', function ($q) use ($request) {
                $q->where('start_time', '>=', $request->time_from);
            });
        }

        if ($request->filled('time_to')) {
            $query->whereHas('scheduleSlot', function ($q) use ($request) {
                $q->where('start_time', '<=', $request->time_to);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return Excel::download(new InterviewSheetExport($query), 'interview-sheet-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportCsv(Request $request)
    {
        $query = Application::with([
            'track', 
            'scheduleSlot', 
            'interviewBoardMember', 
            'referralBoardMember', 
            'strengths', 
            'weaknesses'
        ]);

        // Filters
        if ($request->filled('track_id')) {
            $query->where('track_id', $request->track_id);
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

        if ($request->filled('time_from')) {
            $query->whereHas('scheduleSlot', function ($q) use ($request) {
                $q->where('start_time', '>=', $request->time_from);
            });
        }

        if ($request->filled('time_to')) {
            $query->whereHas('scheduleSlot', function ($q) use ($request) {
                $q->where('start_time', '<=', $request->time_to);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return Excel::download(new InterviewSheetExport($query), 'interview-sheet-' . now()->format('Y-m-d') . '.csv');
    }
}