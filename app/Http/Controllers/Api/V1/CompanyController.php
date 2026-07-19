<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for companies. Reuses the web module's CompanyService, Resource, and
 * Form Requests; every id-scoped action returns a clean 404 for a missing company. Access
 * is enforced entirely by the route-level `permission:companies.*` middleware.
 *
 * CompanyResource touches only scalar columns plus the filesystem-backed `logo_url`
 * accessor (no relations), so read paths need no eager-loading. store()/update() pass the
 * optional `logo` upload through to the service exactly like the web controller; JSON-only
 * clients simply omit it.
 */
class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyService $companyService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
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

    public function store(CompanyStoreRequest $request): JsonResponse
    {
        $company = $this->companyService->createCompany(
            $request->safe()->except(['logo']),
            $request->file('logo')
        );

        return (new CompanyResource($company))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): CompanyResource
    {
        return new CompanyResource($this->findOrFail($id));
    }

    public function update(int $id, CompanyUpdateRequest $request): JsonResponse
    {
        $this->findOrFail($id); // 404 first

        $company = $this->companyService->updateCompany(
            $id,
            $request->safe()->except(['logo', 'delete_logo']),
            $request->file('logo'),
            (bool) $request->input('delete_logo', false)
        );

        return (new CompanyResource($company))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $company = $this->findOrFail($id);

        abort_if(
            $company->invoices()->exists(),
            422,
            'Cannot delete company with existing invoices'
        );

        $this->companyService->deleteCompany($id);

        return response()->json(['message' => 'Company deleted successfully']);
    }

    /**
     * Resolve a company or abort with a 404.
     */
    private function findOrFail(int $id): Company
    {
        $company = $this->companyService->findCompany($id);

        abort_if($company === null, 404, 'Company not found');

        return $company;
    }
}
