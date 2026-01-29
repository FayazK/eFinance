<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\InvoiceTemplate;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invoice = $this->route('id') ? \App\Models\Invoice::find($this->route('id')) : null;

        if ($invoice && $invoice->status !== 'draft') {
            return false;
        }

        return true;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'template' => ['nullable', 'string', Rule::enum(InvoiceTemplate::class)],
            'client_id' => ['sometimes', 'integer', 'exists:clients,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'issue_date' => ['sometimes', 'date'],
            'due_date' => ['sometimes', 'date', 'after_or_equal:issue_date'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'terms' => ['nullable', 'string', 'max:5000'],
            'client_notes' => ['nullable', 'string', 'max:5000'],

            // Line items (optional on update)
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.description' => ['required_with:items', 'string', 'max:500'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0.01'],
        ];
    }
}
