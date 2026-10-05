<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreTrackRequest;
use App\Models\Track;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function index()
    {
        $tracks = Track::withCount(['applications', 'children'])
            ->with('parent')
            ->orderBy('parent_id', 'asc')
            ->orderBy('name')
            ->paginate(15);
        return view('admin.tracks.index', compact('tracks'));
    }

    public function create()
    {
        $mainTracks = Track::mainTracks()->active()->orderBy('name')->get();
        return view('admin.tracks.create', compact('mainTracks'));
    }

    public function store(AdminStoreTrackRequest $request)
    {
        Track::create($request->validated());
        return redirect()->route('admin.tracks.index')
            ->with('success', 'Track created successfully.');
    }

    public function show(Track $track)
    {
        $track->loadCount(['applications', 'children']);
        $track->load('parent');
        return view('admin.tracks.show', compact('track'));
    }

    public function edit(Track $track)
    {
        $mainTracks = Track::mainTracks()->active()->orderBy('name')->get();
        return view('admin.tracks.edit', compact('track', 'mainTracks'));
    }

    public function update(AdminStoreTrackRequest $request, Track $track)
    {
        $track->update($request->validated());
        return redirect()->route('admin.tracks.index')
            ->with('success', 'Track updated successfully.');
    }

    public function destroy(Track $track)
    {
        if ($track->applications()->exists() || $track->scheduleSlots()->exists() || $track->children()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete track with associated applications, schedule slots, or sub-tracks.']);
        }
        
        $track->delete();
        return redirect()->route('admin.tracks.index')
            ->with('success', 'Track deleted successfully.');
    }
}