<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'project_id' => 'required|integer|exists:projects,id',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'estimated_hours' => 'nullable|numeric|min:0',
            'tags' => 'nullable|array'
        ];
        

        
        return $rules;
    }
}
