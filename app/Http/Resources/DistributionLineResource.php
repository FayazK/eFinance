<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistributionLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'distribution_id' => $this->distribution_id,
            'shareholder_id' => $this->shareholder_id,
            'shareholder' => new ShareholderResource($this->whenLoaded('shareholder')),
            'equity_percentage_snapshot' => $this->equity_percentage_snapshot,
            'formatted_equity' => $this->formatted_equity,
            'allocated_amount_pkr' => $this->allocated_amount_pkr / 100, // Major units
            'formatted_allocated_amount' => $this->formatted_allocated_amount,
            'transaction_id' => $this->transaction_id,
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
