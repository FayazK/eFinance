<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectLinkStoreRequest;
use App\Http\Requests\ProjectLinkUpdateRequest;
use App\Http\Resources\ProjectLinkResource;
use App\Models\Project;
use App\Services\ProjectLinkService;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for a project's links (nested under /api/v1/projects/{id}/links). Reuses
 * the web module's ProjectLinkService, Resource, and Form Requests. Access is enforced by the
 * route-level `permission:projects.*` middleware (read for index, update for mutations).
 *
 * The parent project is resolved to a 404 before any link work, and update/destroy re-check
 * that the link belongs to the project (a 404 otherwise), mirroring the web controller's
 * ownership guard. ProjectLinkResource is flat, so create()/update() outputs serialize without
 * any eager loading.
 */
class ProjectLinkController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly ProjectLinkService $projectLinkService,
    ) {}

    public function index(int $id): AnonymousResourceCollection
    {
        $this->findProjectOrFail($id);

        return ProjectLinkResource::collection(
            $this->projectLinkService->getProjectLinks($id)
        );
    }

    public function store(int $id, ProjectLinkStoreRequest $request): JsonResponse
    {
        $this->findProjectOrFail($id);

        $link = $this->projectLinkService->createLink(
            $request->validated() + ['project_id' => $id]
        );

        return (new ProjectLinkResource($link))
            ->response()
            ->setStatusCode(201);
    }

    public function update(int $id, int $link, ProjectLinkUpdateRequest $request): JsonResponse
    {
        $this->findProjectOrFail($id);
        $this->findLinkForProject($id, $link);

        $updated = $this->projectLinkService->updateLink($link, $request->validated());

        return (new ProjectLinkResource($updated))->response();
    }

    public function destroy(int $id, int $link): JsonResponse
    {
        $this->findProjectOrFail($id);
        $this->findLinkForProject($id, $link);

        $this->projectLinkService->deleteLink($link);

        return response()->json(['message' => 'Link deleted successfully']);
    }

    /**
     * Resolve a project or abort with a 404.
     */
    private function findProjectOrFail(int $id): Project
    {
        $project = $this->projectService->findProject($id);

        abort_if($project === null, 404, 'Project not found');

        return $project;
    }

    /**
     * Ensure the link exists and belongs to the given project, or abort with a 404.
     */
    private function findLinkForProject(int $projectId, int $linkId): void
    {
        $link = $this->projectLinkService->findLink($linkId);

        abort_if($link === null || $link->project_id !== $projectId, 404, 'Link not found');
    }
}
