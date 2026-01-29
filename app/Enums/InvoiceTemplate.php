<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoiceTemplate: string
{
    case Modern = 'modern';
    case Classic = 'classic';
    case Minimal = 'minimal';
    case Corporate = 'corporate';
    case Creative = 'creative';

    /**
     * Get human-readable label for the template
     */
    public function label(): string
    {
        return match ($this) {
            self::Modern => 'Modern',
            self::Classic => 'Classic',
            self::Minimal => 'Minimal',
            self::Corporate => 'Corporate',
            self::Creative => 'Creative',
        };
    }

    /**
     * Get description for the template
     */
    public function description(): string
    {
        return match ($this) {
            self::Modern => 'Gradient header with card-based items and rounded corners',
            self::Classic => 'Serif fonts with centered header and traditional grid table',
            self::Minimal => 'Maximum whitespace with thin lines and monochrome design',
            self::Corporate => 'Navy blue header with structured sections and formal layout',
            self::Creative => 'Diagonal color strip with asymmetric vibrant design',
        };
    }

    /**
     * Get all templates as array for dropdowns
     *
     * @return array<int, array{value: string, label: string, description: string}>
     */
    public static function toArray(): array
    {
        return array_map(
            fn (self $template) => [
                'value' => $template->value,
                'label' => $template->label(),
                'description' => $template->description(),
            ],
            self::cases()
        );
    }
}
