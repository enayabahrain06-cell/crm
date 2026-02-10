<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $campaignId = $this->route('campaign')?->id ?? null;

        return [
            'name' => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'channel' => 'sometimes|in:email',
            'subject' => 'sometimes|string|max:200',
            'body' => 'sometimes|string',
            'segment_definition_json' => 'nullable|array',
            'scheduled_at' => 'nullable|date',
            'status' => 'sometimes|in:draft,scheduled,sending,completed,cancelled',
        ];
    }
}

