<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\ProjectLink;
use Illuminate\Database\Eloquent\Collection;

interface ProjectLinkRepositoryInterface
{
    public function findByProject(int $projectId): Collection;

    public function find(int $id): ?ProjectLink;

    public function create(array $data): ProjectLink;

    public function update(int $id, array $data): ProjectLink;

    public function delete(int $id): bool;
}
