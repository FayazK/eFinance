<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransferStoreRequest extends FormRequest
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
            'source_account_id' => ['required', 'integer', 'exists:accounts,id', 'different:destination_account_id'],
            'destination_account_id' => ['required', 'integer', 'exists:accounts,id'],
            'source_amount' => ['required', 'numeric', 'min:0.01'],
            'destination_amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:5000'],
            'date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'source_account_id.different' => 'Source and destination accounts must be different',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate implicit fee for same-currency transfers
            $sourceAccount = \App\Models\Account::find($this->source_account_id);
            $destAccount = \App\Models\Account::find($this->destination_account_id);

            if ($sourceAccount && $destAccount) {
                // For same-currency transfers, source_amount must be >= destination_amount
                if ($sourceAccount->currency_code === $destAccount->currency_code) {
                    if ($this->source_amount < $this->destination_amount) {
                        $validator->errors()->add(
                            'source_amount',
                            'For same-currency transfers, amount sent must be greater than or equal to amount received'
                        );
                    }
                }
            }
        });
    }
}
