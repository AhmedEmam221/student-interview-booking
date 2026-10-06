<?php

namespace App\Http\Controllers\HighBoard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreBoardMemberRequest;
use App\Models\BoardMember;
use App\Models\Track;
use Illuminate\Http\Request;

class BoardMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('track.access');
    }

    public function index()
    {
        $user = auth()->user();
        $userTrack = $user->track;

        if (!$userTrack) {
            abort(403, 'You must be assigned to a track.');
        }

        $subTrackIds = $userTrack->children()
            ->active()
            ->pluck('id')
            ->toArray();

        $allTrackIds = array_merge(
            [$userTrack->id],
            $subTrackIds
        );

        $boardMembers = BoardMember::whereHas('tracks', function ($q) use ($allTrackIds) {
                $q->whereIn('tracks.id', $allTrackIds);
            })
            ->with(['tracks' => function ($q) use ($allTrackIds) {
                $q->whereIn('tracks.id', $allTrackIds);
            }])
            ->withCount([
                'interviewApplications as interview_count' => function ($query) use ($allTrackIds) {
                    $query->whereIn('applications.track_id', $allTrackIds)
                        ->whereIn('applications.status', [
                            'Pending',
                            'Accepted',
                            'Waiting',
                            'Rejected'
                        ]);
                }
            ])
            ->withCount([
                'referralApplications as referral_count' => function ($query) use ($allTrackIds) {
                    $query->whereIn('applications.track_id', $allTrackIds);
                }
            ])
            ->orderBy('board_members.name', 'asc')
            ->paginate(15);

        return view(
            'highboard.board-members.index',
            compact('boardMembers')
        );
    }

    public function create()
    {
        $user = auth()->user();
        $userTrack = $user->track;

        if (!$userTrack) {
            abort(403, 'You must be assigned to a track.');
        }

        $subTrackIds = $userTrack->children()
            ->active()
            ->pluck('id')
            ->toArray();

        $allTrackIds = array_merge(
            [$userTrack->id],
            $subTrackIds
        );

        $tracks = Track::whereIn('tracks.id', $allTrackIds)
            ->orderBy('tracks.name', 'asc')
            ->get();

        return view(
            'highboard.board-members.create',
            compact('tracks', 'userTrack')
        );
    }

    public function store(AdminStoreBoardMemberRequest $request)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        if (!$userTrack) {
            abort(403, 'You must be assigned to a track.');
        }

        $subTrackIds = $userTrack->children()
            ->active()
            ->pluck('id')
            ->toArray();

        $allTrackIds = array_merge(
            [$userTrack->id],
            $subTrackIds
        );

        $data = $request->validated();

        $trackIds = $data['track_ids'] ?? [];

        unset($data['track_ids']);

        // Make sure all track IDs are integers
        $trackIds = array_map('intval', $trackIds);

        // Verify track IDs are accessible
        foreach ($trackIds as $trackId) {
            if (!in_array($trackId, $allTrackIds, true)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'track_ids' => 'You cannot assign board members to this track.'
                    ]);
            }
        }

        $data['referral_code'] = BoardMember::generateReferralCode();

        $boardMember = BoardMember::create($data);

        $boardMember->tracks()->sync($trackIds);

        return redirect()
            ->route('highboard.board-members.index')
            ->with('success', 'Board member created successfully.');
    }

    public function show(BoardMember $boardMember)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        if (!$userTrack) {
            abort(403, 'You must be assigned to a track.');
        }

        $subTrackIds = $userTrack->children()
            ->active()
            ->pluck('id')
            ->toArray();

        $allTrackIds = array_merge(
            [$userTrack->id],
            $subTrackIds
        );

        // Verify board member belongs to at least one accessible track
        $boardMemberTracks = $boardMember->tracks
            ->pluck('id')
            ->toArray();

        $hasAccess = collect($boardMemberTracks)
            ->intersect($allTrackIds)
            ->isNotEmpty();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this board member.');
        }

        // Load only accessible tracks
        $boardMember->load([
            'tracks' => function ($q) use ($allTrackIds) {
                $q->whereIn('tracks.id', $allTrackIds);
            }
        ]);

        // Interview applications count
        $boardMember->loadCount([
            'interviewApplications as interview_count' => function ($query) use ($allTrackIds) {
                $query->whereIn('applications.track_id', $allTrackIds)
                    ->whereIn('applications.status', [
                        'Pending',
                        'Accepted',
                        'Waiting',
                        'Rejected'
                    ]);
            }
        ]);

        // Referral applications count
        $boardMember->loadCount([
            'referralApplications as referral_count' => function ($query) use ($allTrackIds) {
                $query->whereIn('applications.track_id', $allTrackIds);
            }
        ]);

        // Recent interviews
        $recentInterviews = $boardMember
            ->interviewApplications()
            ->with([
                'track',
                'scheduleSlot'
            ])
            ->whereIn('applications.track_id', $allTrackIds)
            ->whereIn('applications.status', [
                'Pending',
                'Accepted',
                'Waiting',
                'Rejected'
            ])
            ->latest('applications.created_at')
            ->limit(10)
            ->get();

        // Recent referrals
        $recentReferrals = $boardMember
            ->referralApplications()
            ->with([
                'track',
                'scheduleSlot'
            ])
            ->whereIn('applications.track_id', $allTrackIds)
            ->latest('applications.created_at')
            ->limit(10)
            ->get();

        return view(
            'highboard.board-members.show',
            compact(
                'boardMember',
                'recentInterviews',
                'recentReferrals'
            )
        );
    }

    public function edit(BoardMember $boardMember)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        if (!$userTrack) {
            abort(403, 'You must be assigned to a track.');
        }

        $subTrackIds = $userTrack->children()
            ->active()
            ->pluck('id')
            ->toArray();

        $allTrackIds = array_merge(
            [$userTrack->id],
            $subTrackIds
        );

        // Verify board member belongs to at least one accessible track
        $boardMemberTracks = $boardMember->tracks
            ->pluck('id')
            ->toArray();

        $hasAccess = collect($boardMemberTracks)
            ->intersect($allTrackIds)
            ->isNotEmpty();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this board member.');
        }

        $tracks = Track::whereIn('tracks.id', $allTrackIds)
            ->orderBy('tracks.name', 'asc')
            ->get();

        return view(
            'highboard.board-members.edit',
            compact('boardMember', 'tracks')
        );
    }

    public function update(
        AdminStoreBoardMemberRequest $request,
        BoardMember $boardMember
    ) {
        $user = auth()->user();
        $userTrack = $user->track;

        if (!$userTrack) {
            abort(403, 'You must be assigned to a track.');
        }

        $subTrackIds = $userTrack->children()
            ->active()
            ->pluck('id')
            ->toArray();

        $allTrackIds = array_merge(
            [$userTrack->id],
            $subTrackIds
        );

        // Verify board member belongs to at least one accessible track
        $boardMemberTracks = $boardMember->tracks
            ->pluck('id')
            ->toArray();

        $hasAccess = collect($boardMemberTracks)
            ->intersect($allTrackIds)
            ->isNotEmpty();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this board member.');
        }

        $data = $request->validated();

        $trackIds = $data['track_ids'] ?? [];

        unset($data['track_ids']);

        // Make sure all track IDs are integers
        $trackIds = array_map('intval', $trackIds);

        // Verify track IDs are accessible
        foreach ($trackIds as $trackId) {
            if (!in_array($trackId, $allTrackIds, true)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'track_ids' => 'You cannot assign board members to this track.'
                    ]);
            }
        }

        $boardMember->update($data);

        $boardMember->tracks()->sync($trackIds);

        return redirect()
            ->route('highboard.board-members.index')
            ->with('success', 'Board member updated successfully.');
    }

    public function destroy(BoardMember $boardMember)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        if (!$userTrack) {
            abort(403, 'You must be assigned to a track.');
        }

        $subTrackIds = $userTrack->children()
            ->active()
            ->pluck('id')
            ->toArray();

        $allTrackIds = array_merge(
            [$userTrack->id],
            $subTrackIds
        );

        // Verify board member belongs to at least one accessible track
        $boardMemberTracks = $boardMember->tracks
            ->pluck('id')
            ->toArray();

        $hasAccess = collect($boardMemberTracks)
            ->intersect($allTrackIds)
            ->isNotEmpty();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this board member.');
        }

        // Don't allow deleting a board member who has applications
        // associated with any accessible track.
        $hasInterviewApplications = $boardMember
            ->interviewApplications()
            ->whereIn('applications.track_id', $allTrackIds)
            ->exists();

        $hasReferralApplications = $boardMember
            ->referralApplications()
            ->whereIn('applications.track_id', $allTrackIds)
            ->exists();

        if ($hasInterviewApplications || $hasReferralApplications) {
            return back()->withErrors([
                'error' => 'Cannot delete board member with associated applications in your tracks. Consider deactivating instead.'
            ]);
        }

        $boardMember->delete();

        return redirect()
            ->route('highboard.board-members.index')
            ->with('success', 'Board member deleted successfully.');
    }
}