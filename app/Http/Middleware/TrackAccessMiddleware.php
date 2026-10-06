<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Track;

class TrackAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin has access to everything
        if ($user->role === 'admin') {
            return $next($request);
        }

        // If user doesn't have a track assigned, deny access
        if (!$user->track_id) {
            abort(403, 'No track assigned to your account.');
        }

        $userTrack = Track::find($user->track_id);
        
        if (!$userTrack) {
            abort(403, 'Assigned track not found.');
        }

        // Share user's track info with views
        view()->share('userTrack', $userTrack);
        view()->share('userRole', $user->role);

        return $next($request);
    }
}