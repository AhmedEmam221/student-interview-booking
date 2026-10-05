<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminStoreBoardMemberRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:board_members,email',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'track_ids' => 'nullable|array',
            'track_ids.*' => 'exists:tracks,id',
            'is_active' => 'boolean',
        ];
    }
}