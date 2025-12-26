<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProjectLink;
use App\Repositories\ProjectLinkRepository;
use Illuminate\Database\Eloquent\Collection;

class ProjectLinkService
{
    public function __construct(
        private ProjectLinkRepository $projectLinkRepository
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
        $allowedFields = ['title', 'url', 'description'];

        $updateData = [];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        return $this->projectLinkRepository->update($linkId, $updateData);
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
