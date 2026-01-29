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
            'pkr_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'usd_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0.0001', 'max:99999'],
            'payroll_ids' => ['required', 'array', 'min:1'],
            'payroll_ids.*' => ['exists:payrolls,id'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $payrolls = Payroll::whereIn('id', $this->payroll_ids ?? [])->get();

            if ($payrolls->isEmpty()) {
                return;
            }

            // Group payrolls by currency
            $pkrPayrolls = $payrolls->filter(fn ($p) => ($p->deposit_currency?->value ?? 'PKR') === 'PKR');
            $usdPayrolls = $payrolls->filter(fn ($p) => ($p->deposit_currency?->value ?? 'PKR') === 'USD');

            // 1. Validate all payrolls are pending
            $pendingCount = $payrolls->where('status', 'pending')->count();
            if ($pendingCount !== count($this->payroll_ids)) {
                $validator->errors()->add('payroll_ids', 'Some payrolls are already paid');

                return;
            }

            // 2. Validate PKR account if PKR payrolls selected
            if ($pkrPayrolls->isNotEmpty()) {
                if (empty($this->pkr_account_id)) {
                    $validator->errors()->add('pkr_account_id', 'PKR account is required for PKR payrolls.');
                } else {
                    $pkrAccount = Account::find($this->pkr_account_id);
                    if ($pkrAccount && $pkrAccount->currency_code !== 'PKR') {
                        $validator->errors()->add('pkr_account_id', 'Must be a PKR account.');
                    }

                    // Check balance for PKR
                    $pkrTotal = $pkrPayrolls->sum('net_payable');
                    if ($pkrAccount && $pkrAccount->current_balance < $pkrTotal) {
                        $validator->errors()->add('pkr_account_id', 'Insufficient PKR balance.');
                    }
                }
            }

            // 3. Validate USD account + exchange rate if USD payrolls selected
            if ($usdPayrolls->isNotEmpty()) {
                if (empty($this->usd_account_id)) {
                    $validator->errors()->add('usd_account_id', 'USD account is required for USD payrolls.');
                } else {
                    $usdAccount = Account::find($this->usd_account_id);
                    if ($usdAccount && $usdAccount->currency_code !== 'USD') {
                        $validator->errors()->add('usd_account_id', 'Must be a USD account.');
                    }

                    // Require exchange rate for USD
                    if (empty($this->exchange_rate)) {
                        $validator->errors()->add('exchange_rate', 'Exchange rate is required for USD payrolls.');
                    } else {
                        // Calculate USD amount needed: PKR net_payable ÷ rate = USD
                        $usdTotalPkr = $usdPayrolls->sum('net_payable');
                        $usdNeeded = (int) round($usdTotalPkr / $this->exchange_rate);

                        if ($usdAccount && $usdAccount->current_balance < $usdNeeded) {
                            $validator->errors()->add('usd_account_id', 'Insufficient USD balance.');
                        }
                    }
                }
            }
        });
    }
}
