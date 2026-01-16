<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollResource extends JsonResource
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
            'employee_id' => $this->employee_id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'month' => $this->month,
            'year' => $this->year,
            'period_label' => $this->period_label,
            'base_salary' => $this->base_salary / 100, // Major units (always PKR)
            'deposit_currency' => $this->deposit_currency->value,
            'bonus' => $this->bonus / 100,
            'deductions' => $this->deductions / 100,
            'net_payable' => $this->net_payable / 100,
            'formatted_base_salary' => $this->formatted_base_salary,
            'formatted_bonus' => $this->formatted_bonus,
            'formatted_deductions' => $this->formatted_deductions,
            'formatted_net_payable' => $this->formatted_net_payable,
            'status' => $this->status,
            'is_pending' => $this->is_pending,
            'is_paid' => $this->is_paid,
            'paid_at' => $this->paid_at?->format('Y-m-d'),
            'transaction_id' => $this->transaction_id,
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
