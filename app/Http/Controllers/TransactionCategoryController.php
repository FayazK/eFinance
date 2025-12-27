<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TransactionCategoryStoreRequest;
use App\Http\Requests\TransactionCategoryUpdateRequest;
use App\Http\Resources\TransactionCategoryResource;
use App\Models\TransactionCategory;
use App\Services\TransactionCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;

class TransactionCategoryController extends Controller
{
    public function __construct(
        private readonly TransactionCategoryService $categoryService
    ) {}

    public function index(): Response
    {
        return Inertia::render('dashboard/transaction-categories/index');
    }

    public function data(): AnonymousResourceCollection
    {
        $categories = $this->categoryService->getAllCategories();

        return TransactionCategoryResource::collection($categories);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/transaction-categories/create');
    }

    public function store(TransactionCategoryStoreRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return response()->json([
            'message' => 'Category created successfully',
            'data' => new TransactionCategoryResource($category),
        ], 201);
    }

    public function update(TransactionCategoryUpdateRequest $request, TransactionCategory $category): JsonResponse
    {
        $updatedCategory = $this->categoryService->updateCategory($category->id, $request->validated());

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new TransactionCategoryResource($updatedCategory),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryService->findCategory($id);

        if (! $category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $this->categoryService->deleteCategory($id);

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }
}
