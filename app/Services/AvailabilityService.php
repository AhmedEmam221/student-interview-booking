<?php

namespace App\Services;

use App\Models\ScheduleSlot;
use App\Models\Track;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Get available slots for a specific date and track
     */
    public function getAvailableSlotsForDateTrack($date, $trackId)
    {
        return ScheduleSlot::with(['track', 'boardMembers'])
            ->where('date', $date)
            ->where('track_id', $trackId)
            ->where('is_active', true)
            ->whereHas('boardMembers')
            ->get()
            ->filter(function ($slot) {
                return $slot->available_slots > 0;
            })
            ->values();
    }

    /**
     * Get available slots for a date range and track (only slots with available capacity)
     */
    public function getAvailableSlotsForDateRange($startDate, $endDate, $trackId = null)
    {
        $query = ScheduleSlot::with(['track', 'boardMembers'])
            ->whereBetween('date', [$startDate, $endDate])
            ->where('is_active', true)
            ->whereHas('boardMembers');

        if ($trackId) {
            $query->where('track_id', $trackId);
        }

        return $query->get()
            ->filter(function ($slot) {
                return $slot->available_slots > 0;
            })
            ->groupBy('date')
            ->map(function ($slots) {
                return $slots->sortBy('start_time')->values();
            });
    }

    /**
     * Get ALL slots for a date range and track (for weekly schedule view - shows all slots including fully booked)
     */
    public function getAllSlotsForDateRange($startDate, $endDate, $trackId = null)
    {
        $query = ScheduleSlot::with([
            'track',
            'boardMembers',
            'applications' => function ($query) {
                $query->whereIn('status', ['Pending', 'Accepted', 'Waiting', 'Rejected'])
                      ->with(['interviewBoardMember', 'referralBoardMember']);
            }
        ])
            ->whereBetween('date', [
                $startDate,
                $endDate
            ])
            ->where('is_active', true)
            ->whereHas('boardMembers');

        if ($trackId !== null) {
            $query->where('track_id', $trackId);
        }

        return $query->get()
            ->groupBy(function ($slot) {
                return \Carbon\Carbon::parse($slot->date)->format('Y-m-d');
            })
            ->map(function ($slots) {
                return $slots
                    ->sortBy('start_time')
                    ->values();
            });
    }

    /**
     * Get weekly schedule with ALL slots (including fully booked) with applications
     */
    public function getAllSlotsForWeeklySchedule($weekStart, $trackId = null)
    {
        $weekEnd = Carbon::parse($weekStart)->endOfWeek();
        
        $slots = $this->getAllSlotsForDateRange($weekStart, $weekEnd, $trackId);
        
        $schedule = [];
        $currentDate = Carbon::parse($weekStart);
        
        while ($currentDate <= $weekEnd) {
            $dateKey = $currentDate->format('Y-m-d');
            $schedule[$dateKey] = $slots->get($dateKey, collect())->values();
            $currentDate->addDay();
        }
        
        return $schedule;
    }

    /**
     * Get all active tracks with their available slots count for a date
     */
    public function getTracksWithAvailability($date)
    {
        return Track::active()
            ->withCount(['scheduleSlots as available_slots_count' => function ($query) use ($date) {
                $query->where('date', $date)
                    ->where('is_active', true)
                    ->whereHas('boardMembers')
                    ->whereRaw('(SELECT COUNT(*) FROM applications WHERE schedule_slot_id = schedule_slots.id AND status IN ("Pending", "Confirmed", "Completed")) < max_interviews');
            }])
            ->get()
            ->map(function ($track) {
                return [
                    'id' => $track->id,
                    'name' => $track->name,
                    'available_slots' => $track->available_slots_count,
                ];
            });
    }

    /**
     * Check if a specific slot has availability
     */
    public function isSlotAvailable($slotId)
    {
        $slot = ScheduleSlot::with('boardMembers')->find($slotId);
        
        if (!$slot || !$slot->is_active || $slot->boardMembers->isEmpty()) {
            return false;
        }
        
        return $slot->available_slots > 0;
    }

    /**
     * Get time slot options (9:00 AM to 4:00 PM, 30 min intervals)
     */
    public function getTimeSlotOptions()
    {
        $slots = [];
        $start = Carbon::createFromTime(9, 0, 0);
        $end = Carbon::createFromTime(16, 0, 0);
        
        while ($start <= $end) {
            $slots[] = $start->format('H:i');
            $start->addMinutes(30);
        }
        
        return $slots;
    }

    public function getWeeklySchedule($weekStart, $trackId){

    }
}