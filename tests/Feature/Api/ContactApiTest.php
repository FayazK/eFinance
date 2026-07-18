<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Contact;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    // Read role permissions directly in tests — deterministic, no cross-test cache bleed.
    config(['permissions.cache.enabled' => false]);

    // ContactFactory + the country_id/state_id/city_id 'exists' rules need world reference data.
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
function contactApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

describe('GET /api/v1/contacts', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/contacts')->assertUnauthorized();
    });

    it('returns 403 without contacts.read', function () {
        $this->getJson('/api/v1/contacts', contactApiBearer(['accounts.read']))
            ->assertForbidden();
    });

    it('lists contacts in a paginated envelope', function () {
        Contact::factory()->count(3)->create();

        $this->getJson('/api/v1/contacts', contactApiBearer(['contacts.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'first_name', 'last_name', 'full_name', 'primary_email', 'client', 'created_at',
                ]],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });

    it('reports the true total, not the page size (#4)', function () {
        // 18 rows across a default 15-per-page window: the page holds 15 but meta.total
        // must be 18 — proves the count is not truncated to the page size.
        Contact::factory()->count(18)->create();

        $this->getJson('/api/v1/contacts', contactApiBearer(['contacts.read']))
            ->assertOk()
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('meta.total', 18);
    });
});

describe('POST /api/v1/contacts', function () {
    it('returns 403 without contacts.create', function () {
        $client = Client::factory()->create();

        $this->postJson('/api/v1/contacts', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'client_id' => $client->id,
            'primary_email' => 'jane@example.com',
        ], contactApiBearer(['contacts.read']))
            ->assertForbidden();
    });

    it('creates a contact and exposes its client', function () {
        $client = Client::factory()->create();

        $response = $this->postJson('/api/v1/contacts', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'client_id' => $client->id,
            'primary_email' => 'jane@example.com',
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
        ], contactApiBearer(['contacts.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'first_name', 'last_name', 'full_name', 'client', 'country']])
            ->assertJsonPath('data.first_name', 'Jane')
            ->assertJsonPath('data.full_name', 'Jane Doe')
            ->assertJsonPath('data.client.id', $client->id);

        $this->assertDatabaseHas('contacts', [
            'id' => $response->json('data.id'),
            'primary_email' => 'jane@example.com',
            'client_id' => $client->id,
        ]);
    });

    it('returns 422 when required fields are missing', function () {
        $this->postJson('/api/v1/contacts', [], contactApiBearer(['contacts.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'client_id', 'primary_email']);
    });
});

describe('id-scoped contact actions', function () {
    it('returns 404 for a missing contact', function () {
        $this->getJson('/api/v1/contacts/999999', contactApiBearer(['contacts.read']))
            ->assertNotFound();
    });

    it('shows a single contact with its client eager-loaded', function () {
        $client = Client::factory()->create();
        $contact = Contact::factory()->create([
            'client_id' => $client->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $this->getJson("/api/v1/contacts/{$contact->id}", contactApiBearer(['contacts.read']))
            ->assertOk()
            ->assertJsonPath('data.full_name', 'Jane Doe')
            ->assertJsonPath('data.client.id', $client->id);
    });

    it('updates a contact', function () {
        $client = Client::factory()->create();
        $contact = Contact::factory()->create(['client_id' => $client->id]);

        $this->putJson("/api/v1/contacts/{$contact->id}", [
            'first_name' => 'Renamed',
            'last_name' => 'Contact',
            'client_id' => $client->id,
            'primary_email' => 'renamed@example.com',
        ], contactApiBearer(['contacts.update']))
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Renamed');

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => 'Renamed',
            'primary_email' => 'renamed@example.com',
        ]);
    });

    it('allows an update that keeps the contact\'s own email', function () {
        // Regression: on the {id} API route the unique-email rule must ignore THIS contact,
        // otherwise a PUT that keeps the same email 422s against itself.
        $client = Client::factory()->create();
        $contact = Contact::factory()->create([
            'client_id' => $client->id,
            'primary_email' => 'keep@example.com',
        ]);

        $this->putJson("/api/v1/contacts/{$contact->id}", [
            'first_name' => 'Kept',
            'last_name' => 'Person',
            'client_id' => $client->id,
            'primary_email' => 'keep@example.com',
        ], contactApiBearer(['contacts.update']))
            ->assertOk()
            ->assertJsonPath('data.primary_email', 'keep@example.com');
    });

    it('returns 404 when updating a missing contact', function () {
        $client = Client::factory()->create();

        $this->putJson('/api/v1/contacts/999999', [
            'first_name' => 'Nobody',
            'last_name' => 'Here',
            'client_id' => $client->id,
            'primary_email' => 'nobody@example.com',
        ], contactApiBearer(['contacts.update']))
            ->assertNotFound();
    });

    it('returns 403 without contacts.delete', function () {
        $contact = Contact::factory()->create();

        $this->deleteJson("/api/v1/contacts/{$contact->id}", [], contactApiBearer(['contacts.read']))
            ->assertForbidden();
    });

    it('deletes a contact', function () {
        $contact = Contact::factory()->create();

        $this->deleteJson("/api/v1/contacts/{$contact->id}", [], contactApiBearer(['contacts.delete']))
            ->assertOk()
            ->assertJsonPath('message', 'Contact deleted successfully');

        // Contact is a hard delete (no SoftDeletes trait).
        $this->assertModelMissing($contact);
    });

    it('returns 404 when deleting a missing contact', function () {
        $this->deleteJson('/api/v1/contacts/999999', [], contactApiBearer(['contacts.delete']))
            ->assertNotFound();
    });
});
