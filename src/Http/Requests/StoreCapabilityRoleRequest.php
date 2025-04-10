<?php

namespace LechugaNegra\AccessManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCapabilityRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Puedes agregar lógica de autorización más adelante
    }

    public function rules()
    {
        return [
            'code' => 'required|string|max:255|unique:capability_roles,code',
            'name' => 'required|string|max:255',
            'status' => 'nullable|string|in:active,inactive'
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
            'status.string' => 'The status must be a string.',
            'status.in' => 'The status must be one of the following: active, inactive.'
        ];
    }
}
