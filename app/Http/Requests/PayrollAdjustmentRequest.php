<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\CurrencyHelper;
use App\Models\Payroll;
use Illuminate\Foundation\Http\FormRequest;

class PayrollAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bonus' => ['sometimes', 'numeric', 'min:0'],
            'deductions' => ['sometimes', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Get payroll ID from route parameter
            $payrollId = $this->route('id');
            $payroll = Payroll::find($payrollId);

            if (! $payroll) {
                return;
            }

            if ($payroll->status !== 'pending') {
                $validator->errors()->add('status', 'Cannot edit paid payroll');

                return;
            }

            // Deductions cannot exceed base salary plus bonus (all compared in minor units).
            // base_salary/bonus/deductions are stored in minor units; request bonus/deductions
            // arrive in major units, so convert them with CurrencyHelper::toMinor first.
            $bonusMinor = $this->has('bonus')
                ? CurrencyHelper::toMinor((float) $this->input('bonus'))
                : $payroll->bonus;

            $deductionsMinor = $this->has('deductions')
                ? CurrencyHelper::toMinor((float) $this->input('deductions'))
                : $payroll->deductions;

            if ($deductionsMinor > $payroll->base_salary + $bonusMinor) {
                $validator->errors()->add('deductions', 'Deductions cannot exceed base salary plus bonus.');
            }
        });
    }
}
