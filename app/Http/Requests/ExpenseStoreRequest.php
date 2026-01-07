<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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

            // Recurring expense fields (optional)
            'is_recurring' => ['nullable', 'boolean'],
            'recurrence_frequency' => [
                Rule::requiredIf($this->boolean('is_recurring')),
                'nullable',
                Rule::in(['monthly', 'quarterly', 'yearly']),
            ],
            'recurrence_interval' => ['nullable', 'integer', 'min:1', 'max:12'],
            'recurrence_start_date' => [
                Rule::requiredIf($this->boolean('is_recurring')),
                'nullable',
                'date',
            ],
            'recurrence_end_date' => ['nullable', 'date', 'after:recurrence_start_date'],

            // Receipt uploads (optional)
            'receipts' => ['nullable', 'array'],
            'receipts.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,webp'], // 5MB max
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
            'recurrence_frequency.required' => 'Please select a recurrence frequency for recurring expenses.',
            'recurrence_start_date.required' => 'Please select a start date for recurring expenses.',
            'receipts.*.max' => 'Each receipt file must not exceed 5MB.',
            'receipts.*.mimes' => 'Receipts must be JPG, PNG, PDF, or WEBP files.',
        ];
    }
}
