<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $expense = Expense::find($this->route('id'));

        if (! $expense) {
            return false;
        }

        // Only draft expenses can be edited
        return $expense->status === 'draft';
    }

    public function rules(): array
    {
        return [
            // Core fields
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'category_id' => ['nullable', 'integer', 'exists:transaction_categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency_code' => ['required', 'string', Rule::in(['USD', 'PKR', 'EUR', 'GBP', 'AED'])],
            'vendor' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'expense_date' => ['required', 'date'],

            // Conditional: Exchange rate required for non-PKR currencies
            'exchange_rate' => [
                Rule::requiredIf(fn () => $this->input('currency_code') !== 'PKR'),
                'nullable',
                'numeric',
                'min:0.01',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => 'Please select an account.',
            'account_id.exists' => 'The selected account does not exist.',
            'amount.required' => 'Please enter an amount.',
            'amount.min' => 'Amount must be greater than 0.',
            'currency_code.required' => 'Please select a currency.',
            'currency_code.in' => 'The selected currency is not supported.',
            'exchange_rate.required' => 'Exchange rate is required for foreign currency expenses.',
            'expense_date.required' => 'Please select an expense date.',
        ];
    }
}
