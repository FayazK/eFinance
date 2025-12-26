<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    public function __construct(
        private ProjectRepository $projectRepository
    ) {}

    public function createProject(array $data): Project
    {
        return $this->projectRepository->create($data);
    }

    public function updateProject(int $projectId, array $data): Project
    {
        $allowedFields = [
            'name',
            'description',
            'client_id',
            'start_date',
            'completion_date',
            'status',
            'budget',
            'actual_cost',
        ];

        $updateData = [];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        return $this->projectRepository->update($projectId, $updateData);
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
