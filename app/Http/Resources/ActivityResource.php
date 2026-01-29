<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $properties = $this->properties ?? collect();

        return [
            'id' => $this->id,
            'log_name' => $this->log_name,
            'description' => $this->description,
            'event' => $this->event,
            'subject_type' => class_basename($this->subject_type),
            'subject_id' => $this->subject_id,
            'causer' => $this->whenLoaded('causer', fn () => $this->causer ? [
                'id' => $this->causer->id,
                'name' => $this->causer->name,
                'avatar_url' => $this->causer->avatar_url ?? null,
            ] : null),
            'properties' => [
                'old' => $properties->get('old'),
                'attributes' => $properties->get('attributes'),
                'custom' => $properties->except(['old', 'attributes'])->toArray(),
            ],
            'changes' => $this->getFormattedChanges($properties),
            'created_at' => $this->created_at?->toISOString(),
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }

    /**
     * Get formatted changes for display
     */
    private function getFormattedChanges($properties): array
    {
        $changes = [];
        $old = $properties->get('old', []);
        $new = $properties->get('attributes', []);

        foreach ($new as $key => $newValue) {
            $oldValue = $old[$key] ?? null;

            // Skip if values are the same
            if ($oldValue === $newValue) {
                continue;
            }

            $changes[] = [
                'field' => $this->formatFieldName($key),
                'old' => $this->formatValue($key, $oldValue),
                'new' => $this->formatValue($key, $newValue),
            ];
        }

        return $changes;
    }

    /**
     * Format field name for display
     */
    private function formatFieldName(string $field): string
    {
        return ucwords(str_replace('_', ' ', $field));
    }

    /**
     * Format value for display
     */
    private function formatValue(string $field, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        // Format amounts (stored in minor units)
        if (str_contains($field, 'amount') || str_contains($field, 'balance') || str_contains($field, 'payable')) {
            return number_format($value / 100, 2);
        }

        // Format dates
        if (str_contains($field, '_at') || str_contains($field, '_date')) {
            if (is_string($value)) {
                return $value;
            }
        }

        return $value;
    }
}
