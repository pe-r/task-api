<?php

namespace App\RequestValidators;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', Rule::in(['todo', 'in_progress', 'done'])],
            'user_id' => ['integer'],
            'project' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tomorrow']
        ];
    }
}
