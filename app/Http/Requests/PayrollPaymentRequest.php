<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\Payroll;
use Illuminate\Foundation\Http\FormRequest;

class PayrollPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'payroll_ids' => ['required', 'array', 'min:1'],
            'payroll_ids.*' => ['exists:payrolls,id'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $account = Account::find($this->account_id);

            // 1. Validate PKR currency
            if ($account && $account->currency_code !== 'PKR') {
                $validator->errors()->add('account_id', 'Payroll can only be paid from PKR accounts');
            }

            // 2. Validate all payrolls are pending
            $pendingCount = Payroll::whereIn('id', $this->payroll_ids)
                ->where('status', 'pending')
                ->count();

            if ($pendingCount !== count($this->payroll_ids)) {
                $validator->errors()->add('payroll_ids', 'Some payrolls are already paid');
            }

            // 3. HARD BLOCK on insufficient balance
            $totalNeeded = Payroll::whereIn('id', $this->payroll_ids)->sum('net_payable');

            if ($account && $account->current_balance < $totalNeeded) {
                $validator->errors()->add('account_id', 'Insufficient balance. Please transfer funds first.');
            }
        });
    }
}
