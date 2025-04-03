<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'content' => 'required|string',
            'task_id' => 'required|integer|exists:tasks,id',
            'parent_id' => 'nullable|integer|exists:comments,id',
            'mentioned_users' => 'nullable|array',
            'mentioned_users.*' => 'integer|exists:users,id'
        ];
        

        
        return $rules;
    }
}
