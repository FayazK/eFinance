<?php

declare(strict_types=1);

namespace App\Http\Requests;

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

            if ($payroll && $payroll->status !== 'pending') {
                $validator->errors()->add('status', 'Cannot edit paid payroll');
            }
        });
    }
}
