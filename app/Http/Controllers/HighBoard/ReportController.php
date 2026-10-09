<?php

namespace App\Http\Controllers\HighBoard;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Track;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InterviewSheetExport;

class ReportController extends Controller
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

        $subTrackIds = $userTrack->children()
            ->active()
            ->pluck('id')
            ->toArray();

        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        $query = Application::with([
            'track',
            'scheduleSlot',
            'interviewBoardMember',
            'referralBoardMember',
            'strengths',
            'weaknesses'
        ])->whereIn('applications.track_id', $allTrackIds);

        // Filters
        if ($request->filled('track_id')) {
            $query->where(
                'applications.track_id',
                $request->track_id
            );
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
            $query->where(
                'applications.status',
                $request->status
            );
        }

        $applications = $query
            ->leftJoin(
                'schedule_slots',
                'applications.schedule_slot_id',
                '=',
                'schedule_slots.id'
            )
            ->orderByRaw('schedule_slots.date IS NULL ASC')
            ->orderBy('schedule_slots.date', 'asc')
            ->orderByRaw('schedule_slots.start_time IS NULL ASC')
            ->orderBy('schedule_slots.start_time', 'asc')
            ->select('applications.*')
            ->get();
            
        $tracks = Track::whereIn('id', $allTrackIds)
            ->orderBy('name')
            ->get();

        $statuses = [
            'Pending',
            'Accepted',
            'Waiting',
            'Rejected'
        ];

        return view(
            'highboard.reports.index',
            compact(
                'applications',
                'tracks',
                'statuses',
                'userTrack'
            )
        );
    }

    public function print(Request $request)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()
            ->active()
            ->pluck('id')
            ->toArray();

        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        $query = Application::with([
            'track',
            'scheduleSlot',
            'interviewBoardMember',
            'referralBoardMember',
            'strengths',
            'weaknesses'
        ])->whereIn('applications.track_id', $allTrackIds);

        // Filters
        if ($request->filled('track_id')) {
            $query->where(
                'applications.track_id',
                $request->track_id
            );
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
            $query->where(
                'applications.status',
                $request->status
            );
        }

        $applications = $query
            ->join(
                'schedule_slots',
                'applications.schedule_slot_id',
                '=',
                'schedule_slots.id'
            )
            ->orderBy('schedule_slots.date', 'asc')
            ->orderBy('schedule_slots.start_time', 'asc')
            ->select('applications.*')
            ->get();

        return view(
            'highboard.reports.print',
            compact('applications', 'userTrack')
        );
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()
            ->active()
            ->pluck('id')
            ->toArray();

        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        $query = Application::with([
            'track',
            'scheduleSlot',
            'interviewBoardMember',
            'referralBoardMember',
            'strengths',
            'weaknesses'
        ])->whereIn('applications.track_id', $allTrackIds);

        // Filters
        if ($request->filled('track_id')) {
            $query->where(
                'applications.track_id',
                $request->track_id
            );
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
            $query->where(
                'applications.status',
                $request->status
            );
        }

        return Excel::download(
            new InterviewSheetExport($query),
            'interview-sheet-' .
            $userTrack->name .
            '-' .
            now()->format('Y-m-d') .
            '.xlsx'
        );
    }

    public function exportCsv(Request $request)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()
            ->active()
            ->pluck('id')
            ->toArray();

        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        $query = Application::with([
            'track',
            'scheduleSlot',
            'interviewBoardMember',
            'referralBoardMember',
            'strengths',
            'weaknesses'
        ])->whereIn('applications.track_id', $allTrackIds);

        // Filters
        if ($request->filled('track_id')) {
            $query->where(
                'applications.track_id',
                $request->track_id
            );
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
            $query->where(
                'applications.status',
                $request->status
            );
        }

        return Excel::download(
            new InterviewSheetExport($query),
            'interview-sheet-' .
            $userTrack->name .
            '-' .
            now()->format('Y-m-d') .
            '.csv'
        );
    }
}