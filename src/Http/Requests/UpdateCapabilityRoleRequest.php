<?php

namespace LechugaNegra\AccessManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCapabilityRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'status.required' => 'The status is required.',
            'status.string' => 'The status must be a string.',
            'status.in'  => 'The status must be "active" or "inactive".'
        ];
    }
}
