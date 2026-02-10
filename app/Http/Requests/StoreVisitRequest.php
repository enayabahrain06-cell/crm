<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'outlet_id' => 'required|exists:outlets,id',
            'visited_at' => 'nullable|date',
            'visit_type' => 'nullable|string|max:50',
            'bill_amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'items_json' => 'nullable|array',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer is required.',
            'outlet_id.required' => 'Outlet is required.',
        ];
    }
}

