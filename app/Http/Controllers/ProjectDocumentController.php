<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProjectDocumentUploadRequest;
use App\Models\Project;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProjectDocumentController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService
    ) {}

    public function store(ProjectDocumentUploadRequest $request, Project $project): JsonResponse
    {
        $document = $request->file('document');

        $media = $this->mediaService->addMedia($project, $document, 'documents');

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

    public function destroy(Project $project, Media $media): JsonResponse
    {
        // Verify media belongs to this project
        if ($media->model_id !== $project->id || $media->model_type !== Project::class) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        $media->delete();

        return response()->json([
            'message' => 'Document deleted successfully',
        ]);
    }
}
