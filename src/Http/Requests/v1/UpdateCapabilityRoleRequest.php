<?php

namespace LechugaNegra\AccessManager\Http\Requests\v1;

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
            'permissions.*' => ['integer', Rule::exists('capability_permissions', 'id')],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'permissions.array' => 'The permissions must be an array of IDs.',
            'permissions.*.integer' => 'Each role must be a valid integer.',
            'permissions.*.exists' => 'One or more selected permissions do not exist.',
            'status.required' => 'The status is required.',
            'status.string' => 'The status must be a string.',
            'status.in'  => 'The status must be "active" or "inactive".'
        ];
    }
}
