<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyService $companyService
    ) {}

    public function index(): Response
    {
        return Inertia::render('dashboard/companies/index');
    }

    public function data(Request $request): AnonymousResourceCollection
    {
        $companies = $this->companyService->getPaginatedCompanies(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['created_at']),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return CompanyResource::collection($companies);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/companies/create');
    }

    public function show(int $id): JsonResponse
    {
        $company = $this->companyService->findCompany($id);

        if (! $company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        return response()->json(new CompanyResource($company));
    }

    public function edit(Company $company): Response
    {
        return Inertia::render('dashboard/companies/edit', [
            'company' => (new CompanyResource($company))->resolve(),
        ]);
    }

    public function store(CompanyStoreRequest $request): JsonResponse
    {
        $company = $this->companyService->createCompany(
            $request->safe()->except(['logo']),
            $request->file('logo')
        );

        return response()->json([
            'message' => 'Company created successfully',
            'data' => new CompanyResource($company),
        ], 201);
    }

    public function update(CompanyUpdateRequest $request, Company $company): JsonResponse
    {
        $updatedCompany = $this->companyService->updateCompany(
            $company->id,
            $request->safe()->except(['logo', 'delete_logo']),
            $request->file('logo'),
            (bool) $request->input('delete_logo', false)
        );

        return response()->json([
            'message' => 'Company updated successfully',
            'data' => new CompanyResource($updatedCompany),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $company = $this->companyService->findCompany($id);

        if (! $company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        // Check if company has invoices
        if ($company->invoices()->exists()) {
            return response()->json([
                'message' => 'Cannot delete company with existing invoices',
            ], 422);
        }

        $this->companyService->deleteCompany($id);

        return response()->json([
            'message' => 'Company deleted successfully',
        ]);
    }
}
