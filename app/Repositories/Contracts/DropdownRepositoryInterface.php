<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface DropdownRepositoryInterface
{
    public function get(string $type, array $params = []): array;
}
