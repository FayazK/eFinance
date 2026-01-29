<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Repositories\CompanyRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyService
{
    public function __construct(
        private CompanyRepository $companyRepository
    ) {}

    public function createCompany(array $data, ?UploadedFile $logo = null): Company
    {
        DB::beginTransaction();

        try {
            if ($logo) {
                $filename = Str::uuid().'.'.$logo->getClientOriginalExtension();
                $path = $logo->storeAs('company-logos', $filename, 'public');
                $data['logo'] = $path;
            }

            $company = $this->companyRepository->create($data);

            DB::commit();

            return $company;
        } catch (Exception $e) {
            DB::rollBack();

            if (isset($data['logo'])) {
                Storage::disk('public')->delete($data['logo']);
            }

            throw $e;
        }
    }

    public function updateCompany(int $companyId, array $data, ?UploadedFile $logo = null, bool $deleteLogo = false): Company
    {
        DB::beginTransaction();

        try {
            $company = $this->companyRepository->find($companyId);

            if (! $company) {
                throw new Exception('Company not found');
            }

            // Handle logo deletion
            if ($deleteLogo && $company->logo) {
                Storage::disk('public')->delete($company->logo);
                $data['logo'] = null;
            }

            // Handle logo replacement
            if ($logo) {
                // Delete old logo
                if ($company->logo) {
                    Storage::disk('public')->delete($company->logo);
                }

                // Store new logo
                $filename = Str::uuid().'.'.$logo->getClientOriginalExtension();
                $path = $logo->storeAs('company-logos', $filename, 'public');
                $data['logo'] = $path;
            }

            $allowedFields = [
                'name',
                'logo',
                'address',
                'phone',
                'email',
                'tax_id',
                'vat_number',
            ];

            $updateData = [];
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $updateData[$field] = $data[$field];
                }
            }

            $updatedCompany = $this->companyRepository->update($companyId, $updateData);

            DB::commit();

            return $updatedCompany;
        } catch (Exception $e) {
            DB::rollBack();

            if (isset($data['logo']) && $logo) {
                Storage::disk('public')->delete($data['logo']);
            }

            throw $e;
        }
    }

    public function deleteCompany(int $companyId): bool
    {
        return $this->companyRepository->delete($companyId);
    }

    public function findCompany(int $id): ?Company
    {
        return $this->companyRepository->find($id);
    }

    public function getAllCompanies(): Collection
    {
        return $this->companyRepository->all();
    }

    public function getPaginatedCompanies(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->companyRepository->paginateCompanies(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }
}
