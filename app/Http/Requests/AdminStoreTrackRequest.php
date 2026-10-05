<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminStoreTrackRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $trackId = $this->route('track') ? $this->route('track')->id : null;
        
        return [
            'name' => 'required|string|max:255|unique:tracks,name' . ($trackId ? ',' . $trackId : ''),
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:tracks,id',
            'is_active' => 'boolean',
        ];
    }
}