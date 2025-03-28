<?php

namespace App\RequestValidators;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['todo', 'in_progress', 'done'])],
            'user_id' => ['nullable', 'integer'],
            'project' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tomorrow']
        ];
    }
}
