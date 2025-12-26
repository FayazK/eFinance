<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ProjectLink;
use Illuminate\Database\Eloquent\Collection;

class ProjectLinkRepository
{
    public function findByProject(int $projectId): Collection
    {
        return ProjectLink::where('project_id', $projectId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find(int $id): ?ProjectLink
    {
        return ProjectLink::find($id);
    }

    public function create(array $data): ProjectLink
    {
        return ProjectLink::create($data);
    }

    public function update(int $id, array $data): ProjectLink
    {
        $link = ProjectLink::findOrFail($id);
        $link->update($data);

        return $link->fresh();
    }

    public function delete(int $id): bool
    {
        $link = ProjectLink::findOrFail($id);

        return $link->delete();
    }
}
