<?php

namespace App\Http\Requests;

use App\Models\ContactLead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'digits:10'],
            'requirement' => ['required', 'string', Rule::in(ContactLead::REQUIREMENTS)],
            'quantity' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'requirement.in' => 'Please select a valid requirement.',
            'phone.required' => 'Please enter your 10-digit mobile number.',
            'phone.digits' => 'Phone number must be exactly 10 digits. Letters and special characters are not allowed.',
        ];
    }
}
