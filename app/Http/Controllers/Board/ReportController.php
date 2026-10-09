<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InterviewSheetExport;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('track.access');
    }

    /**
     * Get the base query for the current user's track.
     */
    private function getQuery(Request $request)
    {
        $userTrack = $request->userTrack ?? auth()->user()->track;

        return [
            'userTrack' => $userTrack,
            'query' => Application::with([
                'track',
                'scheduleSlot',
                'interviewBoardMember',
                'referralBoardMember',
                'strengths',
                'weaknesses'
            ])->where('applications.track_id', $userTrack->id)
        ];
    }

    /**
     * Apply report filters.
     */
    private function applyFilters($query, Request $request)
    {
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
            $query->where('applications.status', $request->status);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $data = $this->getQuery($request);

        $userTrack = $data['userTrack'];
        $query = $data['query'];

        $query = $this->applyFilters($query, $request);

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

        $statuses = [
            'Pending',
            'Accepted',
            'Waiting',
            'Rejected'
        ];

        return view(
            'board.reports.index',
            compact(
                'applications',
                'userTrack',
                'statuses'
            )
        );
    }

    public function print(Request $request)
    {
        $data = $this->getQuery($request);

        $userTrack = $data['userTrack'];
        $query = $data['query'];

        $query = $this->applyFilters($query, $request);

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
            'board.reports.print',
            compact(
                'applications',
                'userTrack'
            )
        );
    }

    public function export(Request $request)
    {
        $data = $this->getQuery($request);

        $userTrack = $data['userTrack'];
        $query = $data['query'];

        $query = $this->applyFilters($query, $request);

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
        $data = $this->getQuery($request);

        $userTrack = $data['userTrack'];
        $query = $data['query'];

        $query = $this->applyFilters($query, $request);

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
