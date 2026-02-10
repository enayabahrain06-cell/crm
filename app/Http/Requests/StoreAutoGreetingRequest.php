<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutoGreetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'trigger_type' => 'required|in:birthday,fixed_date',
            'trigger_date' => 'nullable|required_if:trigger_type,fixed_date|date',
            'nationality_filter' => 'nullable|string|size:2',
            'channel' => 'required|in:email',
            'template_subject' => 'required|string|max:200',
            'template_body' => 'required|string',
            'active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'trigger_date.required_if' => 'Trigger date is required for fixed date greetings.',
        ];
    }
}

