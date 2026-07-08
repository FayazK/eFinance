<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProjectLink;
use App\Repositories\Contracts\ProjectLinkRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProjectLinkService
{
    public function __construct(
        private ProjectLinkRepositoryInterface $projectLinkRepository
    ) {}

    public function getProjectLinks(int $projectId): Collection
    {
        return $this->projectLinkRepository->findByProject($projectId);
    }

    public function createLink(array $data): ProjectLink
    {
        return $this->projectLinkRepository->create($data);
    }

    public function updateLink(int $linkId, array $data): ProjectLink
    {
        return $this->projectLinkRepository->update($linkId, $data);
    }

    public function deleteLink(int $linkId): bool
    {
        return $this->projectLinkRepository->delete($linkId);
    }

    public function findLink(int $id): ?ProjectLink
    {
        return $this->projectLinkRepository->find($id);
    }
}
