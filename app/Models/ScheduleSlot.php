<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'track_id',
        'max_interviews',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'max_interviews' => 'integer',
        'is_active' => 'boolean',
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function boardMembers()
    {
        return $this->belongsToMany(BoardMember::class, 'slot_board_member');
    }

    public function getAvailableSlotsAttribute()
    {
        $assignedCount = $this->applications()
            ->whereIn('status', ['Pending', 'Confirmed', 'Completed'])
            ->count();
        
        return max(0, $this->max_interviews - $assignedCount);
    }

    public function isFull()
    {
        return $this->available_slots <= 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForTrack($query, $trackId)
    {
        return $query->where('track_id', $trackId);
    }

    public function scopeAvailable($query)
    {
        return $query->whereHas('boardMembers')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereRaw('(SELECT COUNT(*) FROM applications WHERE schedule_slot_id = schedule_slots.id AND status IN ("Pending", "Confirmed", "Completed")) < max_interviews');
            });
    }
}