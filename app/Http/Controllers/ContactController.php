<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactStoreRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function __construct(
        private readonly ContactService $contactService
    ) {}

    public function index(): Response
    {
        return Inertia::render('dashboard/contacts/index');
    }

    public function data(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $contacts = $this->contactService->getPaginatedContacts(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['client_id', 'country_id', 'created_at']),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return ContactResource::collection($contacts);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/contacts/create');
    }

    public function show(int $id): JsonResponse
    {
        $contact = $this->contactService->findContact($id);

        if (! $contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        return response()->json(new ContactResource($contact));
    }

    public function edit(Contact $contact): Response
    {
        return Inertia::render('dashboard/contacts/edit', [
            'contact' => $contact->load(['client', 'country', 'state', 'city']),
        ]);
    }

    public function store(ContactStoreRequest $request): JsonResponse
    {
        $contact = $this->contactService->createContact($request->validated());

        return response()->json([
            'message' => 'Contact created successfully',
            'data' => new ContactResource($contact->load(['client', 'country', 'state', 'city'])),
        ], 201);
    }

    public function update(ContactUpdateRequest $request, Contact $contact): JsonResponse
    {
        $updatedContact = $this->contactService->updateContact($contact->id, $request->validated());

        return response()->json([
            'message' => 'Contact updated successfully',
            'data' => new ContactResource($updatedContact),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $contact = $this->contactService->findContact($id);

        if (! $contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        $this->contactService->deleteContact($id);

        return response()->json([
            'message' => 'Contact deleted successfully',
        ]);
    }
}
