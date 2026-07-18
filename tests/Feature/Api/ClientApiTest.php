<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    // Read role permissions directly in tests — deterministic, no cross-test cache bleed.
    config(['permissions.cache.enabled' => false]);

    // ClientFactory + the country_id/currency_id 'exists' rules need world reference data.
    seedMinimalWorld();
});

/**
 * Create a user whose role holds exactly the given permissions and return
 * a Bearer auth header for a fresh token. Uniquely named to avoid Pest's
 * global-function collision across API test files.
 *
 * @param  list<string>  $permissions
 * @return array<string, string>
 */
function clientApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

describe('GET /api/v1/clients', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/clients')->assertUnauthorized();
    });

    it('returns 403 without clients.read', function () {
        $this->getJson('/api/v1/clients', clientApiBearer(['accounts.read']))
            ->assertForbidden();
    });

    it('lists clients in a paginated envelope', function () {
        Client::factory()->count(3)->create();

        $this->getJson('/api/v1/clients', clientApiBearer(['clients.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'name', 'email', 'country', 'currency', 'created_at',
                ]],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });
});

describe('POST /api/v1/clients', function () {
    it('returns 403 without clients.create', function () {
        $this->postJson('/api/v1/clients', [
            'name' => 'Acme Corp',
            'email' => 'acme@example.com',
            'country_id' => 1,
            'currency_id' => 1,
        ], clientApiBearer(['clients.read']))
            ->assertForbidden();
    });

    it('creates a client and exposes its currency', function () {
        $response = $this->postJson('/api/v1/clients', [
            'name' => 'Acme Corp',
            'email' => 'acme@example.com',
            'country_id' => 1,
            'currency_id' => 1,
        ], clientApiBearer(['clients.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'country', 'currency']])
            ->assertJsonPath('data.name', 'Acme Corp')
            ->assertJsonPath('data.currency.id', 1);

        $this->assertDatabaseHas('clients', [
            'id' => $response->json('data.id'),
            'email' => 'acme@example.com',
            'currency_id' => 1,
        ]);
    });

    it('returns 422 when required fields are missing', function () {
        $this->postJson('/api/v1/clients', [], clientApiBearer(['clients.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'country_id', 'currency_id']);
    });
});

describe('id-scoped client actions', function () {
    it('returns 404 for a missing client', function () {
        $this->getJson('/api/v1/clients/999999', clientApiBearer(['clients.read']))
            ->assertNotFound();
    });

    it('shows a single client with state and currency eager-loaded', function () {
        $client = Client::factory()->create([
            'name' => 'Main Client',
            'state_id' => 1,
            'currency_id' => 1,
        ]);

        $this->getJson("/api/v1/clients/{$client->id}", clientApiBearer(['clients.read']))
            ->assertOk()
            ->assertJsonPath('data.name', 'Main Client')
            ->assertJsonPath('data.state.id', 1)      // #6 — state is not silently dropped
            ->assertJsonPath('data.currency.code', 'TSD'); // #114 — currency is unambiguous (ISO code)
    });

    it('updates a client', function () {
        $client = Client::factory()->create(['currency_id' => 1]);

        $this->putJson("/api/v1/clients/{$client->id}", [
            'name' => 'Renamed Client',
            'email' => 'renamed@example.com',
            'country_id' => 1,
            'currency_id' => 1,
        ], clientApiBearer(['clients.update']))
            ->assertOk()
            ->assertJsonPath('data.name', 'Renamed Client');

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Renamed Client',
            'email' => 'renamed@example.com',
        ]);
    });

    it('allows an update that keeps the client\'s own email', function () {
        // Regression: on the {id} API route the unique-email rule must ignore THIS client,
        // otherwise a PUT that keeps the same email 422s against itself.
        $client = Client::factory()->create(['email' => 'keep@example.com', 'currency_id' => 1]);

        $this->putJson("/api/v1/clients/{$client->id}", [
            'name' => 'Kept Client',
            'email' => 'keep@example.com',
            'country_id' => 1,
            'currency_id' => 1,
        ], clientApiBearer(['clients.update']))
            ->assertOk()
            ->assertJsonPath('data.email', 'keep@example.com');
    });

    it('returns 404 when updating a missing client', function () {
        $this->putJson('/api/v1/clients/999999', [
            'name' => 'Nobody',
            'email' => 'nobody@example.com',
            'country_id' => 1,
            'currency_id' => 1,
        ], clientApiBearer(['clients.update']))
            ->assertNotFound();
    });

    it('returns 403 without clients.delete', function () {
        $client = Client::factory()->create();

        $this->deleteJson("/api/v1/clients/{$client->id}", [], clientApiBearer(['clients.read']))
            ->assertForbidden();
    });

    it('deletes a client', function () {
        $client = Client::factory()->create();

        $this->deleteJson("/api/v1/clients/{$client->id}", [], clientApiBearer(['clients.delete']))
            ->assertOk()
            ->assertJsonPath('message', 'Client deleted successfully');

        // Client is a hard delete (no SoftDeletes trait).
        $this->assertModelMissing($client);
    });

    it('returns 404 when deleting a missing client', function () {
        $this->deleteJson('/api/v1/clients/999999', [], clientApiBearer(['clients.delete']))
            ->assertNotFound();
    });
});
