<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'client' => $this->whenLoaded('client', fn () => [
                'id' => $this->client->id,
                'name' => $this->client->name,
            ]),
            'address' => $this->address,
            'country' => $this->whenLoaded('country', fn () => [
                'id' => $this->country?->id,
                'name' => $this->country?->name,
                'iso2' => $this->country?->iso2,
                'emoji' => $this->country?->emoji,
            ]),
            'state' => $this->whenLoaded('state', fn () => [
                'id' => $this->state?->id,
                'name' => $this->state?->name,
            ]),
            'city' => $this->whenLoaded('city', fn () => [
                'id' => $this->city?->id,
                'name' => $this->city?->name,
            ]),
            'primary_phone' => $this->primary_phone,
            'primary_email' => $this->primary_email,
            'additional_phones' => $this->additional_phones ?? [],
            'additional_emails' => $this->additional_emails ?? [],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
