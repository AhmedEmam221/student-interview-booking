<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreBoardMemberRequest;
use App\Models\BoardMember;
use App\Models\Track;
use Illuminate\Http\Request;

class BoardMemberController extends Controller
{
    public function index()
    {
        $boardMembers = BoardMember::with(['tracks', 'scheduleSlots'])
            ->withCount(['interviewApplications as interview_count', 'referralApplications as referral_count'])
            ->orderBy('name')
            ->paginate(15);
        
        return view('admin.board-members.index', compact('boardMembers'));
    }

    public function create()
    {
        $tracks = Track::active()->orderBy('name')->get();
        return view('admin.board-members.create', compact('tracks'));
    }

    public function store(AdminStoreBoardMemberRequest $request)
    {
        $data = $request->validated();
        $trackIds = $data['track_ids'] ?? [];
        unset($data['track_ids']);

        $data['referral_code'] = BoardMember::generateReferralCode();

        $boardMember = BoardMember::create($data);
        $boardMember->tracks()->sync($trackIds);

        return redirect()->route('admin.board-members.index')
            ->with('success', 'Board member created successfully.');
    }

    public function show(BoardMember $boardMember)
    {
        $boardMember->load(['tracks', 'scheduleSlots.track']);
        $boardMember->loadCount(['interviewApplications as interview_count', 'referralApplications as referral_count']);
        
        $recentInterviews = $boardMember->interviewApplications()
            ->with(['track', 'scheduleSlot'])
            ->whereIn('status', ['Pending', 'Confirmed', 'Completed'])
            ->latest()
            ->limit(10)
            ->get();

        $recentReferrals = $boardMember->referralApplications()
            ->with(['track', 'scheduleSlot'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.board-members.show', compact('boardMember', 'recentInterviews', 'recentReferrals'));
    }

    public function edit(BoardMember $boardMember)
    {
        $tracks = Track::active()->orderBy('name')->get();
        return view('admin.board-members.edit', compact('boardMember', 'tracks'));
    }

    public function update(AdminStoreBoardMemberRequest $request, BoardMember $boardMember)
    {
        $data = $request->validated();
        $trackIds = $data['track_ids'] ?? [];
        unset($data['track_ids']);

        $boardMember->update($data);
        $boardMember->tracks()->sync($trackIds);

        return redirect()->route('admin.board-members.index')
            ->with('success', 'Board member updated successfully.');
    }

    public function destroy(BoardMember $boardMember)
    {
        if ($boardMember->interviewApplications()->exists() || $boardMember->referralApplications()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete board member with associated applications. Consider deactivating instead.']);
        }
        
        $boardMember->delete();
        return redirect()->route('admin.board-members.index')
            ->with('success', 'Board member deleted successfully.');
    }
}