<?php

namespace LechugaNegra\AccessManager\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCapabilityRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Puedes agregar lógica de autorización más adelante
    }

    public function rules()
    {
        return [
            'code' => ['required', 'string', 'max:255', Rule::unique('capability_roles', 'code')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'permissions.*' => ['integer', Rule::exists('capability_permissions', 'id')],
            'status' => ['nullable', 'string', 'in:active,inactive']
        ];
    }

    public function prepareForValidation()
    {
        if (!$this->has('status')) {
            $this->merge(['status' => 'inactive']);
        }
    }

    public function messages()
    {
        return [
            'code.required' => 'The code is required.',
            'code.string' => 'The code must be a string.',
            'code.max' => 'The code must not exceed 255 characters.',
            'code.unique' => 'This code is already in use, please choose another one.',
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'permissions.array' => 'The permissions must be an array of IDs.',
            'permissions.*.integer' => 'Each role must be a valid integer.',
            'permissions.*.exists' => 'One or more selected permissions do not exist.',
            'status.string' => 'The status must be a string.',
            'status.in' => 'The status must be one of the following: active, inactive.'
        ];
    }
}
