<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'status' => $this->status,
            'template' => $this->template?->value ?? 'modern',

            // Company info
            'company_id' => $this->company_id,
            'company' => $this->whenLoaded('company', fn () => $this->company ? [
                'id' => $this->company->id,
                'name' => $this->company->name,
                'logo_url' => $this->company->logo_url,
            ] : null),

            // Client info
            'client_id' => $this->client_id,
            'client' => $this->whenLoaded('client', fn () => [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'email' => $this->client->email,
                'company' => $this->client->company,
            ]),

            // Project info
            'project_id' => $this->project_id,
            'project' => $this->whenLoaded('project', fn () => $this->project ? [
                'id' => $this->project->id,
                'name' => $this->project->name,
            ] : null),

            // Monetary values (in major units for editing)
            'currency_code' => $this->currency_code,
            'subtotal' => $this->subtotal_in_major_units,
            'tax_amount' => $this->tax_amount_in_major_units,
            'total_amount' => $this->total_in_major_units,
            'amount_paid' => $this->amount_paid_in_major_units,
            'balance_due' => $this->balance_in_major_units,

            // Formatted for display
            'formatted_subtotal' => $this->formatted_subtotal,
            'formatted_tax_amount' => $this->formatted_tax_amount,
            'formatted_total' => $this->formatted_total,
            'formatted_amount_paid' => $this->formatted_amount_paid,
            'formatted_balance' => $this->formatted_balance,

            // Dates
            'issue_date' => $this->issue_date?->format('Y-m-d'),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'paid_at' => $this->paid_at?->format('Y-m-d'),
            'sent_at' => $this->sent_at?->format('Y-m-d'),
            'voided_at' => $this->voided_at?->format('Y-m-d'),

            // Line items
            'items' => $this->whenLoaded('items', fn () => InvoiceItemResource::collection($this->items)->resolve()
            ),

            // Payments
            'payments' => $this->whenLoaded('payments', fn () => InvoicePaymentResource::collection($this->payments)->resolve()
            ),

            // Computed attributes
            'is_overdue' => $this->is_overdue,
            'is_payable' => $this->is_payable,

            // Metadata
            'notes' => $this->notes,
            'terms' => $this->terms,
            'client_notes' => $this->client_notes,
            'void_reason' => $this->void_reason,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
