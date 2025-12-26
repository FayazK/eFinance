<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'start_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'in:Planning,Active,Completed,Cancelled'],
            'budget' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'actual_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'completion_date.after_or_equal' => 'The completion date must be on or after the start date.',
        ];
    }
}
