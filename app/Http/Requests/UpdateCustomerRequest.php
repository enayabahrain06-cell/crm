<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')?->id ?? null;

        return [
            'type' => 'sometimes|required|in:individual,corporate',
            'name' => 'sometimes|required|string|max:100',
            'email' => 'nullable|email|max:100|unique:customers,email,' . $customerId,
            'country_code' => 'nullable|string|size:2',
            'mobile_number' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|size:2',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive,blacklisted',
        ];
    }
}

