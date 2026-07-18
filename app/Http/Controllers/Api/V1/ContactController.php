<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactStoreRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for contacts (people under clients). Reuses the web module's
 * ContactService, Resource, and Form Requests; every id-scoped action returns a clean 404
 * for a missing contact. Access is enforced entirely by the route-level
 * `permission:contacts.*` middleware.
 *
 * Read paths eager-load client/country/state/city (the repository already does this, and
 * store() loads them explicitly since create() returns a bare model) so the resource never
 * silently drops a relation.
 */
class ContactController extends Controller
{
    private const array RELATIONS = ['client', 'country', 'state', 'city'];

    public function __construct(
        private readonly ContactService $contactService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
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

    public function store(ContactStoreRequest $request): JsonResponse
    {
        $contact = $this->contactService->createContact($request->validated());

        // create() returns a bare model; load relations so the resource emits them.
        $contact->load(self::RELATIONS);

        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): ContactResource
    {
        return new ContactResource($this->findOrFail($id));
    }

    public function update(int $id, ContactUpdateRequest $request): JsonResponse
    {
        $this->findOrFail($id); // 404 first

        $contact = $this->contactService->updateContact($id, $request->validated());

        return (new ContactResource($contact))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findOrFail($id);

        $this->contactService->deleteContact($id);

        return response()->json(['message' => 'Contact deleted successfully']);
    }

    /**
     * Resolve a contact or abort with a 404.
     */
    private function findOrFail(int $id): Contact
    {
        $contact = $this->contactService->findContact($id);

        abort_if($contact === null, 404, 'Contact not found');

        return $contact;
    }
}
