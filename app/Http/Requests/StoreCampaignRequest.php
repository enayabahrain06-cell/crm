<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'channel' => 'required|in:email',
            'subject' => 'required|string|max:200',
            'body' => 'required|string',
            'segment_definition_json' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
            'status' => 'nullable|in:draft,scheduled',
        ];
    }
}

