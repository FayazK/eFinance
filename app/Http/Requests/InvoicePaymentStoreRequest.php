<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\Invoice;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InvoicePaymentStoreRequest extends FormRequest
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
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'payment_amount' => ['required', 'numeric', 'min:0.01'],
            'amount_received' => ['required', 'numeric', 'min:0.01', 'lte:payment_amount'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount_received.lte' => 'Amount received cannot exceed payment amount',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $invoice = Invoice::find($this->route('id'));
            $account = Account::find($this->account_id);

            if ($invoice && $account) {
                // Validate currency match
                if ($invoice->currency_code !== $account->currency_code) {
                    $validator->errors()->add(
                        'account_id',
                        "Account currency ({$account->currency_code}) must match invoice currency ({$invoice->currency_code})"
                    );
                }

                // Validate payment doesn't exceed balance
                $paymentAmount = (int) ($this->payment_amount * 100);
                if ($paymentAmount > $invoice->balance_due) {
                    $validator->errors()->add(
                        'payment_amount',
                        'Payment amount exceeds invoice balance'
                    );
                }
            }
        });
    }
}
