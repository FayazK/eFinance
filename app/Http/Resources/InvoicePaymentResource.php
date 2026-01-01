<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoicePaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'account' => $this->whenLoaded('account', fn () => [
                'id' => $this->account->id,
                'name' => $this->account->name,
                'currency_code' => $this->account->currency_code,
            ]),
            'payment_amount' => $this->payment_amount_in_major_units,
            'amount_received' => $this->amount_received_in_major_units,
            'fee_amount' => $this->fee_amount_in_major_units,
            'formatted_payment_amount' => $this->formatted_payment_amount,
            'formatted_amount_received' => $this->formatted_amount_received,
            'formatted_fee' => $this->formatted_fee,
            'has_fee' => $this->has_fee,
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
