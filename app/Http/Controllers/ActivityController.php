<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Services\ActivityService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityController extends Controller
{
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    /**
     * Get activities for a specific subject
     */
    public function index(string $type, int $id): AnonymousResourceCollection
    {
        $activities = $this->activityService->getActivitiesByTypeAndId($type, $id);

        return ActivityResource::collection($activities);
    }
}
