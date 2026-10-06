<?php

namespace App\Http\Controllers\HighBoard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreScheduleSlotRequest;
use App\Models\BoardMember;
use App\Models\ScheduleSlot;
use App\Models\Track;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
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

        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        $weekStart = $request->get('week_start', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $trackId = $request->get('track_id');

        // If track_id is specified, verify it's in accessible tracks
        if ($trackId && !in_array($trackId, $allTrackIds)) {
            $trackId = null;
        }

        $schedule = $this->availabilityService->getWeeklySchedule($weekStart, $trackId);
        
        $tracks = Track::whereIn('id', $allTrackIds)->orderBy('name')->get();
        $boardMembers = BoardMember::whereHas('tracks', function ($q) use ($allTrackIds) {
                $q->whereIn('tracks.id', $allTrackIds);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $timeSlots = $this->availabilityService->getTimeSlotOptions();

        // Generate week dates
        $weekDates = [];
        $current = Carbon::parse($weekStart);
        for ($i = 0; $i < 7; $i++) {
            $weekDates[] = $current->copy();
            $current->addDay();
        }

        return view('highboard.schedule.index', compact(
            'schedule',
            'tracks',
            'boardMembers',
            'timeSlots',
            'weekDates',
            'weekStart',
            'trackId'
        ));
    }

    public function create()
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        $tracks = Track::whereIn('id', $allTrackIds)->orderBy('name')->get();
        $boardMembers = BoardMember::whereHas('tracks', function ($q) use ($allTrackIds) {
                $q->whereIn('tracks.id', $allTrackIds);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $timeSlots = $this->availabilityService->getTimeSlotOptions();

        return view('highboard.schedule.create', compact('tracks', 'boardMembers', 'timeSlots', 'userTrack'));
    }

    public function store(AdminStoreScheduleSlotRequest $request)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        $data = $request->validated();
        $boardMemberIds = $data['board_member_ids'] ?? [];
        unset($data['board_member_ids']);

        // Verify track belongs to accessible tracks
        if (!in_array($data['track_id'], array_merge([$user->track_id], $user->track->children()->active()->pluck('id')->toArray()))) {
            return back()->withErrors(['track_id' => 'You cannot create slots for this track.']);
        }

        $slot = ScheduleSlot::create($data);
        $slot->boardMembers()->sync($boardMemberIds);

        return redirect()->route('highboard.schedule.index', ['week_start' => $slot->date->startOfWeek()->format('Y-m-d')])
            ->with('success', 'Schedule slot created successfully.');
    }

    public function show(ScheduleSlot $scheduleSlot)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        if (!in_array($scheduleSlot->track_id, $allTrackIds)) {
            abort(403, 'Unauthorized access to this schedule slot.');
        }

        $scheduleSlot->load(['track', 'boardMembers', 'applications']);
        return view('highboard.schedule.show', compact('scheduleSlot'));
    }

    public function edit(ScheduleSlot $scheduleSlot)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        if (!in_array($scheduleSlot->track_id, $allTrackIds)) {
            abort(403, 'Unauthorized access to this schedule slot.');
        }

        $tracks = Track::whereIn('id', $allTrackIds)->orderBy('name')->get();
        $boardMembers = BoardMember::whereHas('tracks', function ($q) use ($allTrackIds) {
                $q->whereIn('tracks.id', $allTrackIds);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $timeSlots = $this->availabilityService->getTimeSlotOptions();

        return view('highboard.schedule.edit', compact('scheduleSlot', 'tracks', 'boardMembers', 'timeSlots'));
    }

    public function update(AdminStoreScheduleSlotRequest $request, ScheduleSlot $scheduleSlot)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        if (!in_array($scheduleSlot->track_id, $allTrackIds)) {
            abort(403, 'Unauthorized access to this schedule slot.');
        }

        $data = $request->validated();
        $boardMemberIds = $data['board_member_ids'] ?? [];
        unset($data['board_member_ids']);

        // Verify track belongs to accessible tracks
        if (!in_array($data['track_id'], $allTrackIds)) {
            return back()->withErrors(['track_id' => 'You cannot create slots for this track.']);
        }

        $scheduleSlot->update($data);
        $scheduleSlot->boardMembers()->sync($boardMemberIds);

        return redirect()->route('highboard.schedule.index', ['week_start' => $scheduleSlot->date->startOfWeek()->format('Y-m-d')])
            ->with('success', 'Schedule slot updated successfully.');
    }

    public function destroy(ScheduleSlot $scheduleSlot)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        if (!in_array($scheduleSlot->track_id, $allTrackIds)) {
            abort(403, 'Unauthorized access to this schedule slot.');
        }

        if ($scheduleSlot->applications()->whereIn('status', ['Pending', 'Accepted', 'Waiting', 'Rejected'])->exists()) {
            return back()->withErrors(['error' => 'Cannot delete slot with confirmed applications.']);
        }
        
        $weekStart = $scheduleSlot->date->startOfWeek()->format('Y-m-d');
        $scheduleSlot->delete();
        
        return redirect()->route('highboard.schedule.index', ['week_start' => $weekStart])
            ->with('success', 'Schedule slot deleted successfully.');
    }

    public function bulkCreate(Request $request)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        $request->validate([
            'week_start' => 'required|date',
            'track_id' => 'required|exists:tracks,id',
            'time_slots' => 'required|array',
            'time_slots.*.start_time' => 'required|date_format:H:i',
            'time_slots.*.end_time' => 'required|date_format:H:i|after:time_slots.*.start_time',
            'time_slots.*.max_interviews' => 'required|integer|min:1|max:10',
            'time_slots.*.board_member_ids' => 'nullable|array',
            'time_slots.*.board_member_ids.*' => 'exists:board_members,id',
            'days' => 'required|array',
            'days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        // Verify track_id is in accessible tracks
        if (!in_array($request->track_id, $allTrackIds)) {
            return back()->withErrors(['track_id' => 'You cannot create slots for this track.']);
        }

        $weekStart = Carbon::parse($request->week_start);
        $trackId = $request->track_id;
        $selectedDays = $request->days;
        $timeSlots = $request->time_slots;

        $created = 0;
        $skipped = 0;

        foreach ($selectedDays as $dayName) {
            $date = $weekStart->copy()->next($dayName);
            
            foreach ($timeSlots as $slotData) {
                $exists = ScheduleSlot::where('date', $date->format('Y-m-d'))
                    ->where('start_time', $slotData['start_time'])
                    ->where('track_id', $trackId)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $slot = ScheduleSlot::create([
                    'date' => $date->format('Y-m-d'),
                    'start_time' => $slotData['start_time'],
                    'end_time' => $slotData['end_time'],
                    'track_id' => $trackId,
                    'max_interviews' => $slotData['max_interviews'],
                    'is_active' => true,
                ]);

                if (!empty($slotData['board_member_ids'])) {
                    // Verify board members belong to accessible tracks
                    $validBoardMemberIds = BoardMember::whereHas('tracks', function ($q) use ($allTrackIds) {
                        $q->whereIn('tracks.id', $allTrackIds);
                    })->whereIn('id', $slotData['board_member_ids'])->pluck('id')->toArray();
                    
                    $slot->boardMembers()->sync($validBoardMemberIds);
                }

                $created++;
            }
        }

        return redirect()->route('highboard.schedule.index', ['week_start' => $weekStart->format('Y-m-d')])
            ->with('success', "Created {$created} schedule slots. Skipped {$skipped} existing slots.");
    }

    public function copyWeek(Request $request)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
        $allTrackIds = array_merge([$userTrack->id], $subTrackIds);

        $request->validate([
            'source_week_start' => 'required|date',
            'target_week_start' => 'required|date|after:source_week_start',
            'track_id' => 'required|exists:tracks,id',
        ]);

        // Verify track_id is in accessible tracks
        if (!in_array($request->track_id, $allTrackIds)) {
            return back()->withErrors(['track_id' => 'You cannot copy slots for this track.']);
        }

        $sourceStart = Carbon::parse($request->source_week_start);
        $targetStart = Carbon::parse($request->target_week_start);
        $trackId = $request->track_id;

        $sourceSlots = ScheduleSlot::where('track_id', $trackId)
            ->whereBetween('date', [$sourceStart->format('Y-m-d'), $sourceStart->copy()->endOfWeek()->format('Y-m-d')])
            ->with('boardMembers')
            ->get();

        $created = 0;
        
        foreach ($sourceSlots as $sourceSlot) {
            $dayDiff = $sourceStart->diffInDays($sourceSlot->date);
            $targetDate = $targetStart->copy()->addDays($dayDiff);

            $exists = ScheduleSlot::where('date', $targetDate->format('Y-m-d'))
                ->where('start_time', $sourceSlot->start_time)
                ->where('track_id', $trackId)
                ->exists();

            if ($exists) {
                continue;
            }

            $slot = ScheduleSlot::create([
                'date' => $targetDate->format('Y-m-d'),
                'start_time' => $sourceSlot->start_time,
                'end_time' => $sourceSlot->end_time,
                'track_id' => $trackId,
                'max_interviews' => $sourceSlot->max_interviews,
                'is_active' => $sourceSlot->is_active,
            ]);

            $boardMemberIds = $sourceSlot->boardMembers->pluck('id')->toArray();
            if (!empty($boardMemberIds)) {
                // Verify board members belong to accessible tracks
                $validBoardMemberIds = BoardMember::whereHas('tracks', function ($q) use ($allTrackIds) {
                    $q->whereIn('tracks.id', $allTrackIds);
                })->whereIn('id', $boardMemberIds)->pluck('id')->toArray();
                
                $slot->boardMembers()->sync($validBoardMemberIds);
            }

            $created++;
        }

        return redirect()->route('highboard.schedule.index', ['week_start' => $targetStart->format('Y-m-d')])
            ->with('success', "Copied {$created} schedule slots from source week.");
    }
}