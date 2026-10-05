<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use App\Models\BoardMember;
use App\Models\ScheduleSlot;
use App\Models\Strength;
use App\Models\Track;
use App\Models\Weakness;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RegistrationController extends Controller
{
    protected $availabilityService;
    protected $bookingService;

    public function __construct(AvailabilityService $availabilityService, BookingService $bookingService)
    {
        $this->availabilityService = $availabilityService;
        $this->bookingService = $bookingService;
    }

    /**
     * Show the recruitment landing page with two buttons
     */
    public function recruitment()
    {
        return view('public.recruitment');
    }

    /**
     * Show the registration form for a specific main track (technical or non-technical)
     */
    public function createForTrack($trackSlug, Request $request)
    {
        $referralBoardMember = null;
        
        if ($request->has('ref')) {
            $referralBoardMember = BoardMember::where('referral_code', $request->ref)
                ->where('is_active', true)
                ->first();
        }

        // Find the main track by slug
        $mainTrack = Track::mainTracks()
            ->active()
            ->whereRaw('LOWER(name) = ?', [strtolower($trackSlug)])
            ->with(['children' => function ($q) {
                $q->active()->orderBy('name');
            }])
            ->firstOrFail();

        $strengths = Strength::active()->orderBy('name')->get();
        $weaknesses = Weakness::active()->orderBy('name')->get();
        
        $majors = ['CS', 'BIS', 'Language and Translation'];
        $academicYears = ['1st', '2nd', '3rd', '4th'];

        return view('public.register', compact(
            'mainTrack',
            'strengths', 
            'weaknesses', 
            'majors', 
            'academicYears',
            'referralBoardMember'
        ));
    }

    /**
     * Show the registration form (legacy - redirects to recruitment)
     */
    public function create(Request $request)
    {
        return redirect()->route('recruitment');
    }

    /**
     * Get available slots for a track and date (AJAX)
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'track_id' => 'required|exists:tracks,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $slots = $this->availabilityService->getAvailableSlotsForDateTrack(
            $request->date,
            $request->track_id
        );

        return response()->json([
            'slots' => $slots->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time->format('H:i'),
                    'end_time' => $slot->end_time->format('H:i'),
                    'available_count' => $slot->available_slots,
                    'max_interviews' => $slot->max_interviews,
                ];
            })->values(),
        ]);
    }

    /**
     * Store the application
     */
    public function store(StoreApplicationRequest $request)
    {
        // Check for duplicate
        $slot = ScheduleSlot::find($request->schedule_slot_id);
        if ($this->bookingService->checkDuplicate(
            $request->email,
            $request->phone,
            $request->track_id,
            $slot->date
        )) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'An application with this email or phone already exists for this track and date.']);
        }

        // Get referral board member if provided
        $referralBoardMemberId = null;
        if ($request->filled('referral_code')) {
            $referralBoardMember = BoardMember::where('referral_code', $request->referral_code)
                ->where('is_active', true)
                ->first();
            if ($referralBoardMember) {
                $referralBoardMemberId = $referralBoardMember->id;
            }
        }

        try {
            $application = $this->bookingService->createApplication([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'major' => $request->major,
                'academic_year' => $request->academic_year,
                'track_id' => $request->track_id,
                'schedule_slot_id' => $request->schedule_slot_id,
                'strengths' => $request->strengths ?? [],
                'weaknesses' => $request->weaknesses ?? [],
                'joined_activity_before' => $request->boolean('joined_activity_before'),
                'activity_name' => $request->activity_name,
                'hobbies' => $request->hobbies,
                'comments' => $request->comments,
                'referral_board_member_id' => $referralBoardMemberId,
            ]);

            return redirect()->route('registration.success', $application->id)
                ->with('success', 'Your application has been submitted successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while processing your application. Please try again.']);
        }
    }

    /**
     * Show success page
     */
    public function success($id)
    {
        $application = \App\Models\Application::with([
            'track', 
            'scheduleSlot', 
            'interviewBoardMember', 
            'referralBoardMember', 
            'strengths', 
            'weaknesses'
        ])->findOrFail($id);

        return view('public.success', compact('application'));
    }
}