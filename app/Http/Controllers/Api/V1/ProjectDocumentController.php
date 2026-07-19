<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectDocumentUploadRequest;
use App\Models\Project;
use App\Services\MediaService;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * JSON REST surface for a project's documents (Spatie Media Library), nested under
 * /api/v1/projects/{id}/documents. Reuses the web module's MediaService and
 * ProjectDocumentUploadRequest (MIME/size validated). Access is enforced by the route-level
 * `permission:projects.update` middleware.
 *
 * destroy() re-checks that the media belongs to this project (model_id/model_type) before
 * deleting, mirroring the web controller's ownership guard.
 */
class ProjectDocumentController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly MediaService $mediaService,
    ) {}

    public function store(int $id, ProjectDocumentUploadRequest $request): JsonResponse
    {
        $project = $this->findProjectOrFail($id);

        $media = $this->mediaService->addMedia($project, $request->file('document'), 'documents');

        return response()->json([
            'message' => 'Document uploaded successfully',
            'data' => [
                'id' => $media->id,
                'name' => $media->file_name,
                'size' => $media->size,
                'mime_type' => $media->mime_type,
                'url' => $media->getUrl(),
                'created_at' => $media->created_at?->format('Y-m-d H:i:s'),
            ],
        ], 201);
    }

    public function destroy(int $id, int $media): JsonResponse
    {
        $project = $this->findProjectOrFail($id);

        $mediaItem = Media::query()->find($media);

        abort_if(
            $mediaItem === null
                || $mediaItem->model_id !== $project->id
                || $mediaItem->model_type !== Project::class,
            404,
            'Document not found'
        );

        $mediaItem->delete();

        return response()->json(['message' => 'Document deleted successfully']);
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
}
