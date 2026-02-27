<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->is_active;
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'priority' => ['required', Rule::in(['low','medium','high'])],
            'status' => ['required', Rule::in(['pending','in_progress','completed'])],
            'due_at' => ['nullable','date'],
            'assignees' => ['array'],
            'assignees.*' => ['exists:users,id'],
        ];

        return $rules;
    }
}
