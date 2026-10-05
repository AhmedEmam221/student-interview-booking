<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_number',
        'full_name',
        'email',
        'phone',
        'major',
        'academic_year',
        'track_id',
        'schedule_slot_id',
        'interview_board_member_id',
        'referral_board_member_id',
        'joined_activity_before',
        'activity_name',
        'hobbies',
        'comments',
        'status',
    ];

    protected $casts = [
        'joined_activity_before' => 'boolean',
        'schedule_slot_id' => 'integer',
        'interview_board_member_id' => 'integer',
        'referral_board_member_id' => 'integer',
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function scheduleSlot()
    {
        return $this->belongsTo(ScheduleSlot::class);
    }

    public function interviewBoardMember()
    {
        return $this->belongsTo(BoardMember::class, 'interview_board_member_id');
    }

    public function referralBoardMember()
    {
        return $this->belongsTo(BoardMember::class, 'referral_board_member_id');
    }

    public function strengths()
    {
        return $this->belongsToMany(Strength::class, 'application_strength');
    }

    public function weaknesses()
    {
        return $this->belongsToMany(Weakness::class, 'application_weakness');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'Accepted');
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'Waiting');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }
}