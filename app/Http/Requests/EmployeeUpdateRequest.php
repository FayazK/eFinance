<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')->id ?? $this->route('id');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'designation' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('employees', 'email')->ignore($employeeId)],
            'joining_date' => ['sometimes', 'date'],
            'base_salary' => ['sometimes', 'numeric', 'min:0.01'],
            'deposit_currency' => ['sometimes', 'in:PKR,USD'],
            'iban' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:active,terminated'],
            'termination_date' => ['nullable', 'date', 'required_if:status,terminated'],
        ];
    }
}
