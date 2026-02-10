<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAutoGreetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:100',
            'trigger_type' => 'sometimes|in:birthday,fixed_date',
            'trigger_date' => 'nullable|date',
            'nationality_filter' => 'nullable|string|size:2',
            'channel' => 'sometimes|in:email',
            'template_subject' => 'sometimes|string|max:200',
            'template_body' => 'sometimes|string',
            'active' => 'nullable|boolean',
        ];
    }
}

