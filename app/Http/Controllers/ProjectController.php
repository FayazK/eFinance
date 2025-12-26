<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService
    ) {}

    public function index(): Response
    {
        return Inertia::render('dashboard/projects/index');
    }

    public function data(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
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

    public function create(): Response
    {
        return Inertia::render('dashboard/projects/create');
    }

    public function show(Project $project): Response
    {
        $project->load(['client.country', 'client.currency', 'links', 'media']);

        return Inertia::render('dashboard/projects/show', [
            'project' => new ProjectResource($project),
        ]);
    }

    public function edit(Project $project): Response
    {
        return Inertia::render('dashboard/projects/edit', [
            'project' => $project->load(['client']),
        ]);
    }

    public function store(ProjectStoreRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject($request->validated());

        return response()->json([
            'message' => 'Project created successfully',
            'data' => new ProjectResource($project->load(['client.country', 'client.currency'])),
        ], 201);
    }

    public function update(ProjectUpdateRequest $request, Project $project): JsonResponse
    {
        $updatedProject = $this->projectService->updateProject($project->id, $request->validated());

        return response()->json([
            'message' => 'Project updated successfully',
            'data' => new ProjectResource($updatedProject),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $project = $this->projectService->findProject($id);

        if (! $project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $this->projectService->deleteProject($id);

        return response()->json([
            'message' => 'Project deleted successfully',
        ]);
    }
}
