<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'source_account' => $this->whenLoaded('withdrawalTransaction', fn () => [
                'id' => $this->withdrawalTransaction->account->id,
                'name' => $this->withdrawalTransaction->account->name,
                'currency_code' => $this->withdrawalTransaction->account->currency_code,
            ]),
            'destination_account' => $this->whenLoaded('depositTransaction', fn () => [
                'id' => $this->depositTransaction->account->id,
                'name' => $this->depositTransaction->account->name,
                'currency_code' => $this->depositTransaction->account->currency_code,
            ]),
            'source_amount' => $this->withdrawalTransaction->amount / 100,
            'destination_amount' => $this->depositTransaction->amount / 100,
            'formatted_source_amount' => $this->withdrawalTransaction->formatted_amount,
            'formatted_destination_amount' => $this->depositTransaction->formatted_amount,
            'exchange_rate' => (float) $this->exchange_rate,
            'formatted_exchange_rate' => $this->formatted_exchange_rate,
            'description' => $this->withdrawalTransaction->description,
            'date' => $this->withdrawalTransaction->date?->format('Y-m-d'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
