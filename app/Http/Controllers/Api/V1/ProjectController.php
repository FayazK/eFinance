<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for projects. Reuses the web module's ProjectService, Resource, and Form
 * Requests; every id-scoped action returns a clean 404 for a missing project. Access is
 * enforced entirely by the route-level `permission:projects.*` middleware.
 *
 * ProjectRepository::create() returns a bare model, so store() must eager-load the relations
 * ProjectResource touches before serializing. update() is self-hydrating (the repository does
 * a fresh([...])->loadCount('media')), so it needs no extra load. Nested links and documents
 * live in their own controllers, mirroring the web module.
 */
class ProjectController extends Controller
{
    /**
     * Relations ProjectResource reads that must be present after a bare create().
     */
    private const array STORE_RELATIONS = ['client.country', 'client.currency'];

    public function __construct(
        private readonly ProjectService $projectService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = $this->projectService->getPaginatedProjects(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['client_id', 'status', 'created_at']),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return ProjectResource::collection($projects);
    }

    public function store(ProjectStoreRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject($request->validated());
        $project->load(self::STORE_RELATIONS);

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): ProjectResource
    {
        $project = $this->findOrFail($id);
        $project->load(['client.country', 'client.currency', 'links', 'media'])
            ->loadCount('media');

        return new ProjectResource($project);
    }

    public function update(int $id, ProjectUpdateRequest $request): JsonResponse
    {
        $this->findOrFail($id); // 404 first

        $project = $this->projectService->updateProject($id, $request->validated());

        return (new ProjectResource($project))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findOrFail($id);

        $this->projectService->deleteProject($id);

        return response()->json(['message' => 'Project deleted successfully']);
    }

    /**
     * Resolve a project or abort with a 404.
     */
    private function findOrFail(int $id): Project
    {
        $project = $this->projectService->findProject($id);

        abort_if($project === null, 404, 'Project not found');

        return $project;
    }
}
