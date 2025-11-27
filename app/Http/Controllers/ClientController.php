<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientService $clientService
    ) {}

    public function index(): Response
    {
        return Inertia::render('dashboard/clients/index');
    }

    public function data(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
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

    public function create(): Response
    {
        return Inertia::render('dashboard/clients/create');
    }

    public function show(int $id): JsonResponse
    {
        $client = $this->clientService->findClient($id);

        if (! $client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        return response()->json(new ClientResource($client));
    }

    public function edit(Client $client): Response
    {
        return Inertia::render('dashboard/clients/edit', [
            'client' => $client->load(['country', 'city', 'currency']),
        ]);
    }

    public function store(ClientStoreRequest $request): JsonResponse
    {
        $client = $this->clientService->createClient($request->validated());

        return response()->json([
            'message' => 'Client created successfully',
            'data' => new ClientResource($client->load(['country', 'city', 'currency'])),
        ], 201);
    }

    public function update(ClientUpdateRequest $request, Client $client): JsonResponse
    {
        $updatedClient = $this->clientService->updateClient($client->id, $request->validated());

        return response()->json([
            'message' => 'Client updated successfully',
            'data' => new ClientResource($updatedClient),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $client = $this->clientService->findClient($id);

        if (! $client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $this->clientService->deleteClient($id);

        return response()->json([
            'message' => 'Client deleted successfully',
        ]);
    }
}
