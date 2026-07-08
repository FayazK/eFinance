<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository
    ) {}

    public function createProject(array $data): Project
    {
        return $this->projectRepository->create($data);
    }

    public function updateProject(int $projectId, array $data): Project
    {
        return $this->projectRepository->update($projectId, $data);
    }

    public function deleteProject(int $projectId): bool
    {
        return $this->projectRepository->delete($projectId);
    }

    public function findProject(int $id): ?Project
    {
        return $this->projectRepository->find($id);
    }

    public function getAllProjects(): Collection
    {
        return $this->projectRepository->all();
    }

    public function getPaginatedProjects(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->projectRepository->paginateProjects(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }
}
