<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:individual,corporate',
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'country_code' => 'required_with:mobile_number|string|size:2',
            'mobile_number' => 'required_with:country_code|string|max:20',
            'nationality' => 'nullable|string|size:2',
            'gender' => 'nullable|in:male,female,other,unknown',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'company_name' => 'nullable|required_if:type,corporate|string|max:100',
            'position' => 'nullable|required_if:type,corporate|string|max:100',
            'status' => 'nullable|in:active,inactive,blacklisted',
        ];
    }

    public function messages(): array
    {
        return [
            'country_code.required_with' => 'Country code is required when mobile number is provided.',
            'mobile_number.required_with' => 'Mobile number is required when country code is provided.',
            'company_name.required_if' => 'Company name is required for corporate customers.',
            'position.required_if' => 'Position is required for corporate customers.',
        ];
    }
}

