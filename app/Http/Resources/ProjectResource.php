<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'client' => $this->whenLoaded('client', fn () => [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'email' => $this->client->email,
                'company' => $this->client->company,
                'country' => $this->client->country ? [
                    'id' => $this->client->country->id,
                    'name' => $this->client->country->name,
                    'iso2' => $this->client->country->iso2,
                ] : null,
                'currency' => $this->client->currency ? [
                    'id' => $this->client->currency->id,
                    'code' => $this->client->currency->code,
                    'symbol' => $this->client->currency->symbol,
                ] : null,
            ]),
            'client_id' => $this->client_id,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'completion_date' => $this->completion_date?->format('Y-m-d'),
            'status' => $this->status,
            'budget' => $this->budget,
            'actual_cost' => $this->actual_cost,
            'documents_count' => $this->media_count ?? 0,
            'links' => $this->whenLoaded('links', fn () => $this->links->map(fn ($link) => [
                'id' => $link->id,
                'title' => $link->title,
                'url' => $link->url,
                'description' => $link->description,
                'created_at' => $link->created_at?->format('Y-m-d H:i:s'),
            ])
            ),
            'documents' => $this->whenLoaded('media', fn () => $this->getMedia('documents')->map(fn ($media) => [
                'id' => $media->id,
                'name' => $media->file_name,
                'size' => $media->size,
                'mime_type' => $media->mime_type,
                'url' => $media->getUrl(),
                'created_at' => $media->created_at?->format('Y-m-d H:i:s'),
            ])
            ),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
