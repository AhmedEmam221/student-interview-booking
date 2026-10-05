<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function scheduleSlots()
    {
        return $this->hasMany(ScheduleSlot::class);
    }

    public function boardMembers()
    {
        return $this->belongsToMany(BoardMember::class, 'board_member_track');
    }

    public function parent()
    {
        return $this->belongsTo(Track::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Track::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMainTracks($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSubTracks($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function getFullNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->name . ' / ' . $this->name;
        }
        return $this->name;
    }

    public function isMainTrack()
    {
        return is_null($this->parent_id);
    }
}