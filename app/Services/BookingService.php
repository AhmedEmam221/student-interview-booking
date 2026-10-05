<?php

namespace App\Services;

use App\Models\Application;
use App\Models\BoardMember;
use App\Models\ScheduleSlot;
use App\Models\Track;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    /**
     * Create a new application and book a slot
     */
    public function createApplication(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Lock the schedule slot for update to prevent race conditions
            $slot = ScheduleSlot::where('id', $data['schedule_slot_id'])
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            // Verify slot belongs to the correct track
            if ($slot->track_id != $data['track_id']) {
                throw ValidationException::withMessages([
                    'schedule_slot_id' => 'The selected time slot does not belong to the selected track.',
                ]);
            }

            // Check availability again with lock
            $assignedCount = Application::where('schedule_slot_id', $slot->id)
                ->whereIn('status', ['Pending', 'Confirmed', 'Completed'])
                ->count();

            if ($assignedCount >= $slot->max_interviews) {
                throw ValidationException::withMessages([
                    'schedule_slot_id' => 'This time slot is no longer available.',
                ]);
            }

            // Get available board members for this slot
            $availableBoardMembers = $slot->boardMembers()
                ->where('board_members.is_active', true)
                ->get();

            if ($availableBoardMembers->isEmpty()) {
                throw ValidationException::withMessages([
                    'schedule_slot_id' => 'No board members available for this slot.',
                ]);
            }

            // Assign a board member (round-robin or first available)
            $assignedBoardMember = $this->assignBoardMember($slot, $availableBoardMembers);

            // Generate application number
            $applicationNumber = $this->generateApplicationNumber();

            // Create the application
            $application = Application::create([
                'application_number' => $applicationNumber,
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'major' => $data['major'],
                'academic_year' => $data['academic_year'],
                'track_id' => $data['track_id'],
                'schedule_slot_id' => $slot->id,
                'interview_board_member_id' => $assignedBoardMember->id,
                'referral_board_member_id' => $data['referral_board_member_id'] ?? null,
                'joined_activity_before' => $data['joined_activity_before'] ?? false,
                'activity_name' => $data['activity_name'] ?? null,
                'hobbies' => $data['hobbies'] ?? null,
                'comments' => $data['comments'] ?? null,
                'status' => 'Pending',
            ]);

            // Attach strengths
            if (!empty($data['strengths'])) {
                $application->strengths()->attach($data['strengths']);
            }

            // Attach weaknesses
            if (!empty($data['weaknesses'])) {
                $application->weaknesses()->attach($data['weaknesses']);
            }

            return $application->load(['track', 'scheduleSlot', 'interviewBoardMember', 'referralBoardMember', 'strengths', 'weaknesses']);
        });
    }

    /**
     * Assign a board member to a slot using round-robin based on current interview count
     */
    protected function assignBoardMember(ScheduleSlot $slot, $boardMembers)
    {
        // Get board members with their current interview count for this slot
        $boardMembersWithCounts = $boardMembers->map(function ($member) use ($slot) {
            $count = $member->interviewApplications()
                ->where('schedule_slot_id', $slot->id)
                ->whereIn('status', ['Pending', 'Confirmed', 'Completed'])
                ->count();
            
            return [
                'member' => $member,
                'count' => $count,
            ];
        });

        // Sort by interview count (ascending) to distribute evenly
        $boardMembersWithCounts = $boardMembersWithCounts->sortBy('count');

        // Return the board member with the least interviews
        return $boardMembersWithCounts->first()['member'];
    }

    /**
     * Generate unique application number
     */
    protected function generateApplicationNumber()
    {
        $year = now()->format('Y');
        $prefix = "INT-{$year}-";
        
        $lastApplication = Application::where('application_number', 'like', $prefix . '%')
            ->orderBy('application_number', 'desc')
            ->first();

        if ($lastApplication) {
            $lastNumber = (int) str_replace($prefix, '', $lastApplication->application_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Cancel an application and free up the slot
     */
    public function cancelApplication(Application $application)
    {
        return DB::transaction(function () use ($application) {
            if (!in_array($application->status, ['Pending', 'Confirmed'])) {
                throw ValidationException::withMessages([
                    'status' => 'Only pending or confirmed applications can be cancelled.',
                ]);
            }

            $application->update(['status' => 'Cancelled']);
            
            return $application;
        });
    }

    /**
     * Update application status
     */
    public function updateStatus(Application $application, string $status)
    {
        $validStatuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled', 'No Show'];
        
        if (!in_array($status, $validStatuses)) {
            throw ValidationException::withMessages([
                'status' => 'Invalid status.',
            ]);
        }

        return DB::transaction(function () use ($application, $status) {
            $application->update(['status' => $status]);
            return $application->fresh();
        });
    }

    /**
     * Check for duplicate application (same email/phone for same track/date)
     */
    public function checkDuplicate($email, $phone, $trackId, $date)
    {
        return Application::where(function ($query) use ($email, $phone) {
                $query->where('email', $email)
                    ->orWhere('phone', $phone);
            })
            ->where('track_id', $trackId)
            ->whereHas('scheduleSlot', function ($query) use ($date) {
                $query->where('date', $date);
            })
            ->whereIn('status', ['Pending', 'Confirmed', 'Completed'])
            ->exists();
    }
}