<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Payroll;
use Illuminate\Foundation\Http\FormRequest;

class PayrollGenerateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if payroll already exists for this period
            $exists = Payroll::where('month', $this->month)
                ->where('year', $this->year)
                ->exists();

            if ($exists) {
                $validator->errors()->add('month', 'Payroll for this period already exists');
            }
        });
    }
}
