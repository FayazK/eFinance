<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceVoidRequest extends FormRequest
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
            'void_reason' => ['required', 'string', 'min:1', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'void_reason.required' => 'A void reason is required.',
            'void_reason.min' => 'A void reason is required.',
            'void_reason.max' => 'The void reason cannot exceed 1000 characters.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $invoice = Invoice::with('payments.account')->find($this->route('id'));

            if (! $invoice) {
                return;
            }

            if ($invoice->status === 'void') {
                $validator->errors()->add('void_reason', 'This invoice is already voided.');

                return;
            }

            // Check each payment account has sufficient balance for reversal
            foreach ($invoice->payments as $payment) {
                if ($payment->is_voided) {
                    continue; // Skip already voided payments
                }

                $account = $payment->account;
                if (! $account) {
                    continue;
                }

                // Calculate the net amount that was credited to the account
                // Income (credit) - Fee (debit) = net added to account
                // To reverse, we need to debit income and credit fee back
                // Net reversal amount = income - fee = amount_received
                $netReversalAmount = $payment->amount_received;

                // Check if account has sufficient balance
                if ($account->current_balance < $netReversalAmount) {
                    $accountName = $account->name;
                    $formattedBalance = $account->formatted_balance;
                    $formattedRequired = number_format($netReversalAmount / 100, 2);

                    $validator->errors()->add(
                        'void_reason',
                        "Insufficient balance in account '{$accountName}'. Current balance: {$formattedBalance}, Required: {$account->currency_code} {$formattedRequired}"
                    );
                }
            }
        });
    }
}
