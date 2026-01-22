<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseVoidRequest extends FormRequest
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
            $expense = Expense::with('account')->find($this->route('id'));

            if (! $expense) {
                return;
            }

            if ($expense->status === 'voided') {
                $validator->errors()->add('void_reason', 'This expense is already voided.');

                return;
            }

            if ($expense->status !== 'processed') {
                $validator->errors()->add('void_reason', 'Only processed expenses can be voided.');

                return;
            }

            $account = $expense->account;
            if (! $account) {
                $validator->errors()->add('void_reason', 'The expense account could not be found.');
            }
        });
    }
}
