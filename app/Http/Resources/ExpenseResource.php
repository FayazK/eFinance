<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expense_date' => $this->expense_date?->format('Y-m-d'),
            'vendor' => $this->vendor,
            'description' => $this->description,
            'amount' => $this->amount,
            'currency_code' => $this->currency_code,
            'exchange_rate' => $this->exchange_rate,
            'reporting_amount_pkr' => $this->reporting_amount_pkr,
            'formatted_amount' => $this->formatted_amount,
            'formatted_reporting_amount' => $this->formatted_reporting_amount,
            'status' => $this->status,
            'is_recurring' => $this->is_recurring,
            'is_active' => $this->is_active,
            'recurrence_frequency' => $this->recurrence_frequency,
            'recurrence_interval' => $this->recurrence_interval,
            'next_occurrence_date' => $this->next_occurrence_date?->format('Y-m-d'),
            'account' => $this->whenLoaded('account', fn () => [
                'id' => $this->account->id,
                'name' => $this->account->name,
                'currency_code' => $this->account->currency_code,
            ]),
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'color' => $this->category->color,
            ]),
            'transaction' => $this->whenLoaded('transaction', fn () => [
                'id' => $this->transaction->id,
            ]),
            'receipts' => $this->whenLoaded('media', fn () => $this->getMedia('receipts')->map(fn ($media) => [
                'id' => $media->id,
                'name' => $media->file_name,
                'url' => $media->getUrl(),
                'mime_type' => $media->mime_type,
                'size' => $media->size,
            ])),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
