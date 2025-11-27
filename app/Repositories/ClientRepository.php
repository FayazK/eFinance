<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository
{
    public function find(int $id): ?Client
    {
        return Client::with(['country', 'city', 'currency'])->find($id);
    }

    public function findByEmail(string $email): ?Client
    {
        return Client::with(['country', 'city', 'currency'])
            ->where('email', $email)
            ->first();
    }

    public function create(array $data): Client
    {
        return Client::create($data);
    }

    public function update(int $id, array $data): Client
    {
        $client = Client::findOrFail($id);
        $client->update($data);

        return $client->fresh(['country', 'city', 'currency']);
    }

    public function delete(int $id): bool
    {
        $client = Client::findOrFail($id);

        return $client->delete();
    }

    public function existsByEmail(string $email, ?int $excludeId = null): bool
    {
        $query = Client::where('email', $email);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function all(): Collection
    {
        return Client::with(['country', 'city', 'currency'])->get();
    }

    public function paginateClients(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Client::query()->with(['country', 'city', 'currency']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($filters) {
            foreach ($filters as $column => $value) {
                if ($value !== null && $value !== '') {
                    if (in_array($column, ['country_id', 'city_id', 'currency_id'])) {
                        $query->where($column, $value);
                    } elseif ($column === 'created_at' && is_array($value) && count($value) === 2) {
                        $query->whereBetween('created_at', $value);
                    }
                }
            }
        }

        $allowedSortColumns = ['name', 'email', 'company', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }
}
