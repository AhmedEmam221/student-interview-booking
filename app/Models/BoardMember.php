<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BoardMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'referral_code',
        'bio',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'board_member_track');
    }

    public function scheduleSlots()
    {
        return $this->belongsToMany(ScheduleSlot::class, 'slot_board_member');
    }

    public function interviewApplications()
    {
        return $this->hasMany(Application::class, 'interview_board_member_id');
    }

    public function referralApplications()
    {
        return $this->hasMany(Application::class, 'referral_board_member_id');
    }

    public static function generateReferralCode()
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (self::where('referral_code', $code)->exists());
        
        return $code;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getInterviewCountAttribute()
    {
        return $this->interviewApplications()->whereIn('status', ['Confirmed', 'Completed'])->count();
    }

    public function getReferralCountAttribute()
    {
        return $this->referralApplications()->count();
    }
}