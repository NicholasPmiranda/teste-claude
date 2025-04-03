<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'file' => 'required|file|max:10240', // 10MB max
            'task_id' => 'required|integer|exists:tasks,id'
        ];
        

        return $rules;
    }
}
