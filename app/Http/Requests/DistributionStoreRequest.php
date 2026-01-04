<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DistributionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Period-based OR manual amount (mutually exclusive)
            'period_start' => ['required_without:manual_amount_pkr', 'nullable', 'date'],
            'period_end' => ['required_with:period_start', 'nullable', 'date', 'after:period_start'],

            // Manual amount approach
            'manual_amount_pkr' => ['required_without:period_start', 'nullable', 'integer', 'min:1'],

            // Action determines flow
            'action' => ['required', 'in:draft,process'],

            // Account required only when processing
            'account_id' => ['required_if:action,process', 'nullable', 'exists:accounts,id'],

            'notes' => ['nullable', 'string'],
        ];
    }
}
