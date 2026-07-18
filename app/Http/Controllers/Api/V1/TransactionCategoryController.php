<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionCategoryStoreRequest;
use App\Http\Requests\TransactionCategoryUpdateRequest;
use App\Http\Resources\TransactionCategoryResource;
use App\Models\TransactionCategory;
use App\Services\TransactionCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for transaction categories. Reuses the web module's
 * TransactionCategoryService, Resource, and Form Requests; the id-scoped actions
 * return a clean 404 for a missing category. Access is enforced entirely by the
 * route-level `permission:transaction_categories.*` middleware (mirroring the web
 * flow) — note the permission key uses an underscore while the URL uses a hyphen.
 *
 * This is a small taxonomy module: full CRUD without a show route. `index`
 * returns every category unpaginated (a data-only `{ "data": [...] }` envelope,
 * no links/meta), so clients get the whole list in one call.
 */
class TransactionCategoryController extends Controller
{
    public function __construct(
        private readonly TransactionCategoryService $categoryService
    ) {}

    /**
     * Full, unpaginated list of categories. TransactionCategoryResource exposes no
     * relations, so there is no lazy-load hazard here.
     */
    public function index(): AnonymousResourceCollection
    {
        return TransactionCategoryResource::collection($this->categoryService->getAllCategories());
    }

    public function store(TransactionCategoryStoreRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return (new TransactionCategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    public function update(int $id, TransactionCategoryUpdateRequest $request): JsonResponse
    {
        $this->findOrFail($id); // 404 first

        $category = $this->categoryService->updateCategory($id, $request->validated());

        return (new TransactionCategoryResource($category))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findOrFail($id);

        $this->categoryService->deleteCategory($id);

        return response()->json(['message' => 'Transaction category deleted successfully']);
    }

    /**
     * Resolve a category or abort with a 404.
     */
    private function findOrFail(int $id): TransactionCategory
    {
        $category = $this->categoryService->findCategory($id);

        abort_if($category === null, 404, 'Transaction category not found');

        return $category;
    }
}
