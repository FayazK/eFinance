<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProjectLinkStoreRequest;
use App\Http\Requests\ProjectLinkUpdateRequest;
use App\Http\Resources\ProjectLinkResource;
use App\Models\Project;
use App\Models\ProjectLink;
use App\Services\ProjectLinkService;
use Illuminate\Http\JsonResponse;

class ProjectLinkController extends Controller
{
    public function __construct(
        private readonly ProjectLinkService $projectLinkService
    ) {}

    public function index(Project $project): JsonResponse
    {
        $links = $this->projectLinkService->getProjectLinks($project->id);

        return response()->json([
            'data' => ProjectLinkResource::collection($links),
        ]);
    }

    public function store(ProjectLinkStoreRequest $request, Project $project): JsonResponse
    {
        $linkData = array_merge($request->validated(), ['project_id' => $project->id]);
        $link = $this->projectLinkService->createLink($linkData);

        return response()->json([
            'message' => 'Link created successfully',
            'data' => new ProjectLinkResource($link),
        ], 201);
    }

    public function update(ProjectLinkUpdateRequest $request, Project $project, ProjectLink $link): JsonResponse
    {
        // Verify link belongs to this project
        if ($link->project_id !== $project->id) {
            return response()->json(['message' => 'Link not found'], 404);
        }

        $updatedLink = $this->projectLinkService->updateLink($link->id, $request->validated());

        return response()->json([
            'message' => 'Link updated successfully',
            'data' => new ProjectLinkResource($updatedLink),
        ]);
    }

    public function destroy(Project $project, ProjectLink $link): JsonResponse
    {
        // Verify link belongs to this project
        if ($link->project_id !== $project->id) {
            return response()->json(['message' => 'Link not found'], 404);
        }

        $this->projectLinkService->deleteLink($link->id);

        return response()->json([
            'message' => 'Link deleted successfully',
        ]);
    }
}
