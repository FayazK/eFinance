<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for clients. Reuses the web module's ClientService, Resource, and
 * Form Requests; every id-scoped action returns a clean 404 for a missing client.
 * Access is enforced entirely by the route-level `permission:clients.*` middleware.
 *
 * Read paths eager-load country/state/city/currency (the repository already does this,
 * and store() loads them explicitly since create() returns a bare model) so the resource
 * never silently drops a relation and never trips preventLazyLoading (#6).
 */
class ClientController extends Controller
{
    private const array RELATIONS = ['country', 'state', 'city', 'currency'];

    public function __construct(
        private readonly ClientService $clientService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $clients = $this->clientService->getPaginatedClients(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['country_id', 'currency_id', 'created_at']),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return ClientResource::collection($clients);
    }

    public function store(ClientStoreRequest $request): JsonResponse
    {
        $client = $this->clientService->createClient($request->validated());

        // create() returns a bare model; load relations so the resource emits them.
        $client->load(self::RELATIONS);

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): ClientResource
    {
        return new ClientResource($this->findOrFail($id));
    }

    public function update(int $id, ClientUpdateRequest $request): JsonResponse
    {
        $this->findOrFail($id); // 404 first

        $client = $this->clientService->updateClient($id, $request->validated());

        return (new ClientResource($client))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findOrFail($id);

        $this->clientService->deleteClient($id);

        return response()->json(['message' => 'Client deleted successfully']);
    }

    /**
     * Resolve a client or abort with a 404.
     */
    private function findOrFail(int $id): Client
    {
        $client = $this->clientService->findClient($id);

        abort_if($client === null, 404, 'Client not found');

        return $client;
    }
}
