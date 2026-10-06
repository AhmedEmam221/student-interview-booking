<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'track_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isHighBoard()
    {
        return $this->role === 'highboard';
    }

    public function isBoard()
    {
        return $this->role === 'board';
    }

    public function isHighBoardOrAdmin()
    {
        return in_array($this->role, ['admin', 'highboard']);
    }

    public function canAccessTrack($trackId)
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->track_id) {
            return false;
        }

        $userTrack = Track::find($this->track_id);
        
        if (!$userTrack) {
            return false;
        }

        // Admin and HighBoard can access their main track and all subtracks
        if ($this->isAdmin() || $this->isHighBoard()) {
            if ($this->track_id == $trackId) {
                return true;
            }
            
            // Check if the requested track is a subtrack of user's track
            $subTrack = Track::find($trackId);
            if ($subTrack && $subTrack->parent_id == $this->track_id) {
                return true;
            }
        }

        // Board members can only access their specific track
        if ($this->isBoard()) {
            return $this->track_id == $trackId;
        }

        return false;
    }

    public function getAccessibleTrackIds()
    {
        if ($this->isAdmin()) {
            return Track::active()->pluck('id')->toArray();
        }

        if (!$this->track_id) {
            return [];
        }

        $userTrack = Track::find($this->track_id);
        
        if (!$userTrack) {
            return [];
        }

        if ($this->isHighBoard()) {
            // HighBoard can access main track and all its subtracks
            $subTrackIds = $userTrack->children()->active()->pluck('id')->toArray();
            return array_merge([$this->track_id], $subTrackIds);
        }

        if ($this->isBoard()) {
            // Board can only access their specific track
            return [$this->track_id];
        }

        return [];
    }
}