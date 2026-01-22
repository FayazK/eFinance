<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceDueDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invoice = Invoice::find($this->route('id'));

        if (! $invoice) {
            return false;
        }

        // Only unpaid/non-void invoices can have their due date edited
        return ! in_array($invoice->status, ['paid', 'void']);
    }

    public function rules(): array
    {
        return [
            'due_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'due_date.required' => 'Please select a due date.',
            'due_date.date' => 'Please provide a valid date.',
        ];
    }
}
