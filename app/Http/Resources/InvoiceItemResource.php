<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'unit_price' => $this->unit_price_in_major_units,
            'amount' => $this->amount_in_major_units,
            'formatted_unit_price' => $this->formatted_unit_price,
            'formatted_amount' => $this->formatted_amount,
            'sort_order' => $this->sort_order,
        ];
    }
}
