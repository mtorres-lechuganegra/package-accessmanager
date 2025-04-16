<?php

namespace LechugaNegra\AccessManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCapabilityRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('capability_roles', 'name')->ignore($id)],
            'status' => ['required', 'string', 'in:active', 'inactive'],
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
