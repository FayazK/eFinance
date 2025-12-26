<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use App\Repositories\ContactRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ContactService
{
    public function __construct(
        private ContactRepository $contactRepository
    ) {}

    public function createContact(array $data): Contact
    {
        // Ensure JSON fields are properly formatted
        $data['additional_phones'] = $data['additional_phones'] ?? [];
        $data['additional_emails'] = $data['additional_emails'] ?? [];

        return $this->contactRepository->create($data);
    }

    public function updateContact(int $contactId, array $data): Contact
    {
        $allowedFields = [
            'first_name',
            'last_name',
            'client_id',
            'address',
            'city',
            'state',
            'country_id',
            'primary_phone',
            'primary_email',
            'additional_phones',
            'additional_emails',
        ];

        $updateData = [];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        // Ensure JSON fields are properly formatted
        if (array_key_exists('additional_phones', $updateData)) {
            $updateData['additional_phones'] = $updateData['additional_phones'] ?? [];
        }
        if (array_key_exists('additional_emails', $updateData)) {
            $updateData['additional_emails'] = $updateData['additional_emails'] ?? [];
        }

        return $this->contactRepository->update($contactId, $updateData);
    }

    public function deleteContact(int $contactId): bool
    {
        return $this->contactRepository->delete($contactId);
    }

    public function findContact(int $id): ?Contact
    {
        return $this->contactRepository->find($id);
    }

    public function findContactByEmail(string $email): ?Contact
    {
        return $this->contactRepository->findByEmail($email);
    }

    public function getAllContacts(): Collection
    {
        return $this->contactRepository->all();
    }

    public function getContactsByClient(int $clientId): Collection
    {
        return $this->contactRepository->getByClient($clientId);
    }

    public function getPaginatedContacts(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->contactRepository->paginateContacts(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }

    public function isEmailTaken(string $email, ?int $excludeId = null): bool
    {
        return $this->contactRepository->existsByEmail($email, $excludeId);
    }
}
