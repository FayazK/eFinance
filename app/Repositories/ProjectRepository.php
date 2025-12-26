<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository
{
    public function find(int $id): ?Project
    {
        return Project::with(['client.country', 'client.currency', 'links'])
            ->withCount('media')
            ->find($id);
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function update(int $id, array $data): Project
    {
        $project = Project::findOrFail($id);
        $project->update($data);

        return $project->fresh(['client.country', 'client.currency', 'links']);
    }

    public function delete(int $id): bool
    {
        $project = Project::findOrFail($id);

        return $project->delete(); // Soft delete
    }

    public function all(): Collection
    {
        return Project::with(['client.country', 'client.currency'])
            ->withCount('media')
            ->get();
    }

    public function paginateProjects(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Project::query()
            ->with(['client.country', 'client.currency'])
            ->withCount('media');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($filters) {
            foreach ($filters as $column => $value) {
                if ($value !== null && $value !== '') {
                    if ($column === 'client_id') {
                        $query->where('client_id', $value);
                    } elseif ($column === 'status') {
                        $query->where('status', $value);
                    } elseif ($column === 'created_at' && is_array($value) && count($value) === 2) {
                        $query->whereBetween('created_at', $value);
                    }
                }
            }
        }

        $allowedSortColumns = ['name', 'status', 'start_date', 'completion_date', 'budget', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }
}
