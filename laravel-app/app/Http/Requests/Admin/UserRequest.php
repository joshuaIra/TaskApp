<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    private const ALLOWED_DEPARTMENTS = [
        'ICT',
        'Big Data',
        'SMRP',
        'DSS',
        'DES',
        'Census',
        'Finance',
        'DDG Office',
        'DG office',
        'HCS',
        'HR',
        'Procurement',
        'SPIU Office',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'department' => ['required', Rule::in(self::ALLOWED_DEPARTMENTS)],
            'role' => ['required', Rule::in(['admin','manager','member'])],
            'is_active' => ['sometimes','boolean'],
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            // update
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }
}
