<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminStoreScheduleSlotRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'track_id' => 'required|exists:tracks,id',
            'max_interviews' => 'required|integer|min:1|max:10',
            'board_member_ids' => 'nullable|array',
            'board_member_ids.*' => 'exists:board_members,id',
            'is_active' => 'boolean',
        ];
    }
}