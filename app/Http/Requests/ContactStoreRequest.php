<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Contact;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ContactStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'address' => ['nullable', 'string', 'max:1000'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'primary_phone' => ['nullable', 'string', 'max:50'],
            'primary_email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.Contact::class.',primary_email',
            ],
            'additional_phones' => ['nullable', 'array'],
            'additional_phones.*' => ['string', 'max:50'],
            'additional_emails' => ['nullable', 'array'],
            'additional_emails.*' => ['email', 'max:255'],
        ];
    }
}
