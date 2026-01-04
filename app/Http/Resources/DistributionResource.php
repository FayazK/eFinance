<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistributionResource extends JsonResource
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
            'distribution_number' => $this->distribution_number,
            'status' => $this->status,
            'is_draft' => $this->status === 'draft',
            'is_processed' => $this->status === 'processed',
            'period_start' => $this->period_start?->format('Y-m-d'),
            'period_end' => $this->period_end?->format('Y-m-d'),
            'period_label' => $this->period_label,
            'total_revenue_pkr' => $this->total_revenue_pkr / 100, // Major units
            'total_expenses_pkr' => $this->total_expenses_pkr / 100,
            'calculated_net_profit_pkr' => $this->calculated_net_profit_pkr / 100,
            'adjusted_net_profit_pkr' => $this->adjusted_net_profit_pkr ? $this->adjusted_net_profit_pkr / 100 : null,
            'final_net_profit' => $this->final_net_profit / 100,
            'distributed_amount_pkr' => $this->distributed_amount_pkr / 100,
            'formatted_revenue' => $this->formatted_revenue,
            'formatted_expenses' => $this->formatted_expenses,
            'formatted_net_profit' => $this->formatted_net_profit,
            'is_manually_adjusted' => $this->is_manually_adjusted,
            'adjustment_reason' => $this->adjustment_reason,
            'processed_at' => $this->processed_at?->format('Y-m-d H:i:s'),
            'notes' => $this->notes,
            'lines' => DistributionLineResource::collection($this->whenLoaded('lines')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
