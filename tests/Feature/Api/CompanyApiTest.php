<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Read role permissions directly in tests — deterministic, no cross-test cache bleed.
    config(['permissions.cache.enabled' => false]);

    // CompanyFactory is self-contained (no world reference data needed); the one test that
    // creates an Invoice seeds the world locally because InvoiceFactory pulls in a Client.
});

/**
 * Create a user whose role holds exactly the given permissions and return
 * a Bearer auth header for a fresh token. Uniquely named to avoid Pest's
 * global-function collision across API test files.
 *
 * @param  list<string>  $permissions
 * @return array<string, string>
 */
function companyApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

describe('GET /api/v1/companies', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/companies')->assertUnauthorized();
    });

    it('returns 403 without companies.read', function () {
        $this->getJson('/api/v1/companies', companyApiBearer(['accounts.read']))
            ->assertForbidden();
    });

    it('lists companies in a paginated envelope', function () {
        Company::factory()->count(3)->create();

        $this->getJson('/api/v1/companies', companyApiBearer(['companies.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'name', 'logo_url', 'email', 'created_at',
                ]],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });

    it('reports the true total, not the page size (#4)', function () {
        // 18 rows across a default 15-per-page window: the page holds 15 but meta.total
        // must be 18 — proves the count is not truncated to the page size.
        Company::factory()->count(18)->create();

        $this->getJson('/api/v1/companies', companyApiBearer(['companies.read']))
            ->assertOk()
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('meta.total', 18);
    });
});

describe('POST /api/v1/companies', function () {
    it('returns 403 without companies.create', function () {
        $this->postJson('/api/v1/companies', [
            'name' => 'Acme Inc',
        ], companyApiBearer(['companies.read']))
            ->assertForbidden();
    });

    it('creates a company', function () {
        $response = $this->postJson('/api/v1/companies', [
            'name' => 'Acme Inc',
            'email' => 'hello@acme.test',
        ], companyApiBearer(['companies.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'name', 'email']])
            ->assertJsonPath('data.name', 'Acme Inc')
            ->assertJsonPath('data.email', 'hello@acme.test');

        $this->assertDatabaseHas('companies', [
            'id' => $response->json('data.id'),
            'name' => 'Acme Inc',
            'email' => 'hello@acme.test',
        ]);
    });

    it('returns 422 when name is missing', function () {
        $this->postJson('/api/v1/companies', [], companyApiBearer(['companies.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    });

    it('accepts a logo upload', function () {
        // File uploads must go through a multipart request, not postJson.
        Storage::fake('public');

        $response = $this->post('/api/v1/companies', [
            'name' => 'Logo Co',
            'logo' => UploadedFile::fake()->image('logo.png'),
        ], companyApiBearer(['companies.create']) + ['Accept' => 'application/json'])
            ->assertCreated();

        expect($response->json('data.logo_url'))->not->toBeNull();

        // The service stored the file on the public disk under company-logos/.
        $storedPath = Company::find($response->json('data.id'))->logo;
        Storage::disk('public')->assertExists($storedPath);
    });
});

describe('id-scoped company actions', function () {
    it('returns 404 for a missing company', function () {
        $this->getJson('/api/v1/companies/999999', companyApiBearer(['companies.read']))
            ->assertNotFound();
    });

    it('shows a single company', function () {
        $company = Company::factory()->create(['name' => 'Shown Co']);

        $this->getJson("/api/v1/companies/{$company->id}", companyApiBearer(['companies.read']))
            ->assertOk()
            ->assertJsonPath('data.id', $company->id)
            ->assertJsonPath('data.name', 'Shown Co');
    });

    it('updates a company', function () {
        $company = Company::factory()->create(['name' => 'Old Name']);

        $this->putJson("/api/v1/companies/{$company->id}", [
            'name' => 'New Name',
        ], companyApiBearer(['companies.update']))
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name');

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'New Name',
        ]);
    });

    it('returns 404 when updating a missing company', function () {
        $this->putJson('/api/v1/companies/999999', [
            'name' => 'Nobody',
        ], companyApiBearer(['companies.update']))
            ->assertNotFound();
    });

    it('returns 403 without companies.delete', function () {
        $company = Company::factory()->create();

        $this->deleteJson("/api/v1/companies/{$company->id}", [], companyApiBearer(['companies.read']))
            ->assertForbidden();
    });

    it('soft-deletes a company', function () {
        $company = Company::factory()->create();

        $this->deleteJson("/api/v1/companies/{$company->id}", [], companyApiBearer(['companies.delete']))
            ->assertOk()
            ->assertJsonPath('message', 'Company deleted successfully');

        // Company uses the SoftDeletes trait.
        $this->assertSoftDeleted($company);
    });

    it('returns 404 when deleting a missing company', function () {
        $this->deleteJson('/api/v1/companies/999999', [], companyApiBearer(['companies.delete']))
            ->assertNotFound();
    });

    it('refuses to delete a company that has invoices (mirrors the web guard)', function () {
        // InvoiceFactory pulls in a Client, which reads the world reference tables.
        seedMinimalWorld();

        $company = Company::factory()->create();
        Invoice::factory()->create(['company_id' => $company->id]);

        $this->deleteJson("/api/v1/companies/{$company->id}", [], companyApiBearer(['companies.delete']))
            ->assertStatus(422)
            ->assertJsonPath('message', 'Cannot delete company with existing invoices');

        $this->assertNotSoftDeleted($company);
    });
});
