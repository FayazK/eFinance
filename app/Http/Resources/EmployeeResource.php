<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'name' => $this->name,
            'designation' => $this->designation,
            'email' => $this->email,
            'joining_date' => $this->joining_date?->format('Y-m-d'),
            'base_salary' => $this->salary_in_major_units, // For editing (always PKR)
            'deposit_currency' => $this->deposit_currency->value,
            'formatted_salary' => $this->formatted_salary, // For display
            'iban' => $this->iban,
            'bank_name' => $this->bank_name,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'termination_date' => $this->termination_date?->format('Y-m-d'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'payrolls' => PayrollResource::collection($this->whenLoaded('payrolls')),
        ];
    }
}
