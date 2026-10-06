<?php

namespace App\Http\Controllers\HighBoard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreTrackRequest;
use App\Models\Track;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function __construct()
    {
        $this->middleware('track.access');
    }

    public function index()
    {
        $user = auth()->user();
        $userTrack = $user->track;

        // HighBoard can only see their main track and subtracks
        $tracks = Track::where(function ($q) use ($userTrack) {
                $q->where('id', $userTrack->id)
                  ->orWhere('parent_id', $userTrack->id);
            })
            ->withCount('applications')
            ->orderBy('name')
            ->paginate(15);
        
        return view('highboard.tracks.index', compact('tracks', 'userTrack'));
    }

    public function create()
    {
        $user = auth()->user();
        $userTrack = $user->track;
        
        return view('highboard.tracks.create', compact('userTrack'));
    }

    public function store(AdminStoreTrackRequest $request)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        // HighBoard can only create subtracks under their main track
        $data = $request->validated();
        $data['parent_id'] = $userTrack->id;

        Track::create($data);
        return redirect()->route('highboard.tracks.index')
            ->with('success', 'Subtrack created successfully.');
    }

    public function show(Track $track)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        if ($track->parent_id != $userTrack->id && $track->id != $userTrack->id) {
            abort(403, 'Unauthorized access to this track.');
        }

        $track->loadCount('applications');
        return view('highboard.tracks.show', compact('track'));
    }

    public function edit(Track $track)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        if ($track->parent_id != $userTrack->id && $track->id != $userTrack->id) {
            abort(403, 'Unauthorized access to this track.');
        }

        return view('highboard.tracks.edit', compact('track'));
    }

    public function update(AdminStoreTrackRequest $request, Track $track)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        if ($track->parent_id != $userTrack->id && $track->id != $userTrack->id) {
            abort(403, 'Unauthorized access to this track.');
        }

        $data = $request->validated();
        
        // HighBoard cannot change the parent_id
        unset($data['parent_id']);
        
        $track->update($data);
        return redirect()->route('highboard.tracks.index')
            ->with('success', 'Track updated successfully.');
    }

    public function destroy(Track $track)
    {
        $user = auth()->user();
        $userTrack = $user->track;

        if ($track->parent_id != $userTrack->id) {
            abort(403, 'Unauthorized access to this track.');
        }

        if ($track->applications()->exists() || $track->scheduleSlots()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete track with associated applications or schedule slots.']);
        }
        
        $track->delete();
        return redirect()->route('highboard.tracks.index')
            ->with('success', 'Subtrack deleted successfully.');
    }
}