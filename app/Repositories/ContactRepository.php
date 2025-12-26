<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ContactRepository
{
    public function find(int $id): ?Contact
    {
        return Contact::with(['client', 'country'])->find($id);
    }

    public function findByEmail(string $email): ?Contact
    {
        return Contact::with(['client', 'country'])
            ->where('primary_email', $email)
            ->first();
    }

    public function create(array $data): Contact
    {
        return Contact::create($data);
    }

    public function update(int $id, array $data): Contact
    {
        $contact = Contact::findOrFail($id);
        $contact->update($data);

        return $contact->fresh(['client', 'country']);
    }

    public function delete(int $id): bool
    {
        $contact = Contact::findOrFail($id);

        return $contact->delete();
    }

    public function existsByEmail(string $email, ?int $excludeId = null): bool
    {
        $query = Contact::where('primary_email', $email);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function all(): Collection
    {
        return Contact::with(['client', 'country'])->get();
    }

    public function getByClient(int $clientId): Collection
    {
        return Contact::with(['country'])
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function paginateContacts(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Contact::query()->with(['client', 'country']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('primary_email', 'like', "%{$search}%")
                    ->orWhere('primary_phone', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($filters) {
            foreach ($filters as $column => $value) {
                if ($value !== null && $value !== '') {
                    if (in_array($column, ['client_id', 'country_id'])) {
                        $query->where($column, $value);
                    } elseif ($column === 'created_at' && is_array($value) && count($value) === 2) {
                        $query->whereBetween('created_at', $value);
                    }
                }
            }
        }

        $allowedSortColumns = ['first_name', 'last_name', 'primary_email', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }
}
