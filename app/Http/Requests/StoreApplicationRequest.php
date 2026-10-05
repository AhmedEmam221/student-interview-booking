<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[\d\s\-\+\(\)]+$/'],
            'major' => ['required', Rule::in(['CS', 'BIS', 'Language and Translation'])],
            'academic_year' => ['required', Rule::in(['1st', '2nd', '3rd', '4th'])],
            'track_id' => ['required', 'exists:tracks,id'],
            'schedule_slot_id' => ['required', 'exists:schedule_slots,id'],
            'strengths' => ['nullable', 'array'],
            'strengths.*' => ['exists:strengths,id'],
            'weaknesses' => ['nullable', 'array'],
            'weaknesses.*' => ['exists:weaknesses,id'],
            'joined_activity_before' => ['required', 'boolean'],
            'activity_name' => ['nullable', 'required_if:joined_activity_before,true', 'string', 'max:255'],
            'hobbies' => ['nullable', 'string', 'max:1000'],
            'comments' => ['nullable', 'string', 'max:1000'],
            'referral_code' => ['nullable', 'string', 'exists:board_members,referral_code'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'full_name.required' => 'Full name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Please provide a valid phone number.',
            'major.required' => 'Major is required.',
            'major.in' => 'Please select a valid major.',
            'academic_year.required' => 'Academic year is required.',
            'academic_year.in' => 'Please select a valid academic year.',
            'track_id.required' => 'Track is required.',
            'track_id.exists' => 'Selected track is invalid.',
            'schedule_slot_id.required' => 'Time slot is required.',
            'schedule_slot_id.exists' => 'Selected time slot is invalid.',
            'joined_activity_before.required' => 'Please indicate if you joined a student activity before.',
            'activity_name.required_if' => 'Activity name is required when you have joined an activity before.',
        ];
    }
}