<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectLink;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Read role permissions directly in tests — deterministic, no cross-test cache bleed.
    config(['permissions.cache.enabled' => false]);

    // ProjectFactory pulls in a Client, which reads the world reference tables.
    seedMinimalWorld();
});

/**
 * Create a user whose role holds exactly the given permissions and return a Bearer auth
 * header for a fresh token. Uniquely named to avoid Pest's global-function collision across
 * API test files.
 *
 * @param  list<string>  $permissions
 * @return array<string, string>
 */
function projectApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

/**
 * A minimal but real PDF payload — `mimes:pdf` sniffs content, so a plain fake()->create()
 * won't pass validation.
 */
function projectApiFakePdf(): UploadedFile
{
    $pdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000056 00000 n\n0000000115 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n206\n%%EOF";

    return UploadedFile::fake()->createWithContent('document.pdf', $pdf);
}

describe('GET /api/v1/projects', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/projects')->assertUnauthorized();
    });

    it('returns 403 without projects.read', function () {
        $this->getJson('/api/v1/projects', projectApiBearer(['accounts.read']))
            ->assertForbidden();
    });

    it('lists projects in a paginated envelope', function () {
        Project::factory()->count(3)->create();

        $this->getJson('/api/v1/projects', projectApiBearer(['projects.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'name', 'status', 'client_id', 'documents_count', 'created_at',
                ]],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });

    it('reports the true total, not the page size (#4)', function () {
        // 18 rows across a default 15-per-page window: the page holds 15 but meta.total
        // must be 18 — proves the count is not truncated to the page size.
        Project::factory()->count(18)->create();

        $this->getJson('/api/v1/projects', projectApiBearer(['projects.read']))
            ->assertOk()
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('meta.total', 18);
    });
});

describe('POST /api/v1/projects', function () {
    it('returns 403 without projects.create', function () {
        $client = Client::factory()->create();

        $this->postJson('/api/v1/projects', [
            'name' => 'Apollo',
            'client_id' => $client->id,
            'status' => 'Active',
        ], projectApiBearer(['projects.read']))
            ->assertForbidden();
    });

    it('creates a project', function () {
        $client = Client::factory()->create();

        $response = $this->postJson('/api/v1/projects', [
            'name' => 'Apollo',
            'client_id' => $client->id,
            'status' => 'Active',
        ], projectApiBearer(['projects.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'name', 'status', 'client']])
            ->assertJsonPath('data.name', 'Apollo')
            ->assertJsonPath('data.status', 'Active')
            ->assertJsonPath('data.client.id', $client->id);

        $this->assertDatabaseHas('projects', [
            'id' => $response->json('data.id'),
            'name' => 'Apollo',
            'client_id' => $client->id,
        ]);
    });

    it('returns 422 when required fields are missing', function () {
        $this->postJson('/api/v1/projects', [], projectApiBearer(['projects.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'client_id', 'status']);
    });
});

describe('id-scoped project actions', function () {
    it('returns 404 for a missing project', function () {
        $this->getJson('/api/v1/projects/999999', projectApiBearer(['projects.read']))
            ->assertNotFound();
    });

    it('shows a single project with its eager-loaded client (#6)', function () {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id, 'name' => 'Shown']);

        $this->getJson("/api/v1/projects/{$project->id}", projectApiBearer(['projects.read']))
            ->assertOk()
            ->assertJsonPath('data.id', $project->id)
            ->assertJsonPath('data.name', 'Shown')
            ->assertJsonPath('data.client.id', $client->id);
    });

    it('updates a project', function () {
        $project = Project::factory()->create(['name' => 'Old Name', 'status' => 'Planning']);

        $this->putJson("/api/v1/projects/{$project->id}", [
            'name' => 'New Name',
            'client_id' => $project->client_id,
            'status' => 'Active',
        ], projectApiBearer(['projects.update']))
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.status', 'Active');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'New Name',
            'status' => 'Active',
        ]);
    });

    it('returns 404 when updating a missing project', function () {
        // Use a real client so validation passes and the request reaches the 404 guard.
        $client = Client::factory()->create();

        $this->putJson('/api/v1/projects/999999', [
            'name' => 'Nobody',
            'client_id' => $client->id,
            'status' => 'Active',
        ], projectApiBearer(['projects.update']))
            ->assertNotFound();
    });

    it('returns 403 without projects.delete', function () {
        $project = Project::factory()->create();

        $this->deleteJson("/api/v1/projects/{$project->id}", [], projectApiBearer(['projects.read']))
            ->assertForbidden();
    });

    it('soft-deletes a project', function () {
        $project = Project::factory()->create();

        $this->deleteJson("/api/v1/projects/{$project->id}", [], projectApiBearer(['projects.delete']))
            ->assertOk()
            ->assertJsonPath('message', 'Project deleted successfully');

        // Project uses the SoftDeletes trait.
        $this->assertSoftDeleted($project);
    });

    it('returns 404 when deleting a missing project', function () {
        $this->deleteJson('/api/v1/projects/999999', [], projectApiBearer(['projects.delete']))
            ->assertNotFound();
    });
});

describe('project links', function () {
    it('returns 401 without a token', function () {
        $project = Project::factory()->create();

        $this->getJson("/api/v1/projects/{$project->id}/links")->assertUnauthorized();
    });

    it('returns 403 listing links without projects.read', function () {
        $project = Project::factory()->create();

        $this->getJson("/api/v1/projects/{$project->id}/links", projectApiBearer(['accounts.read']))
            ->assertForbidden();
    });

    it('lists a project links', function () {
        $project = Project::factory()->create();
        ProjectLink::create([
            'project_id' => $project->id,
            'title' => 'Repo',
            'url' => 'https://example.com/repo',
        ]);

        $this->getJson("/api/v1/projects/{$project->id}/links", projectApiBearer(['projects.read']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Repo');
    });

    it('returns 403 creating a link without projects.update', function () {
        $project = Project::factory()->create();

        $this->postJson("/api/v1/projects/{$project->id}/links", [
            'title' => 'Repo',
            'url' => 'https://example.com/repo',
        ], projectApiBearer(['projects.read']))
            ->assertForbidden();
    });

    it('creates a link', function () {
        $project = Project::factory()->create();

        $this->postJson("/api/v1/projects/{$project->id}/links", [
            'title' => 'Design',
            'url' => 'https://example.com/design',
        ], projectApiBearer(['projects.update']))
            ->assertCreated()
            ->assertJsonPath('data.title', 'Design')
            ->assertJsonPath('data.project_id', $project->id);

        $this->assertDatabaseHas(ProjectLink::class, [
            'project_id' => $project->id,
            'title' => 'Design',
        ]);
    });

    it('updates a link', function () {
        $project = Project::factory()->create();
        $link = ProjectLink::create([
            'project_id' => $project->id,
            'title' => 'Old',
            'url' => 'https://example.com/old',
        ]);

        $this->putJson("/api/v1/projects/{$project->id}/links/{$link->id}", [
            'title' => 'Updated',
            'url' => 'https://example.com/updated',
        ], projectApiBearer(['projects.update']))
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated');
    });

    it('returns 404 updating a link that belongs to another project', function () {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();
        $link = ProjectLink::create([
            'project_id' => $projectA->id,
            'title' => 'A link',
            'url' => 'https://example.com/a',
        ]);

        $this->putJson("/api/v1/projects/{$projectB->id}/links/{$link->id}", [
            'title' => 'Hijack',
            'url' => 'https://example.com/hijack',
        ], projectApiBearer(['projects.update']))
            ->assertNotFound();
    });

    it('deletes a link', function () {
        $project = Project::factory()->create();
        $link = ProjectLink::create([
            'project_id' => $project->id,
            'title' => 'Gone',
            'url' => 'https://example.com/gone',
        ]);

        $this->deleteJson("/api/v1/projects/{$project->id}/links/{$link->id}", [], projectApiBearer(['projects.update']))
            ->assertOk()
            ->assertJsonPath('message', 'Link deleted successfully');

        // ProjectLink is hard-deleted.
        $this->assertModelMissing($link);
    });
});

describe('project documents', function () {
    it('returns 403 uploading without projects.update', function () {
        Storage::fake('public');
        $project = Project::factory()->create();

        $this->post("/api/v1/projects/{$project->id}/documents", [
            'document' => projectApiFakePdf(),
        ], projectApiBearer(['projects.read']) + ['Accept' => 'application/json'])
            ->assertForbidden();
    });

    it('uploads a document', function () {
        // File uploads must go through a multipart request, not postJson.
        Storage::fake('public');
        $project = Project::factory()->create();

        $response = $this->post("/api/v1/projects/{$project->id}/documents", [
            'document' => projectApiFakePdf(),
        ], projectApiBearer(['projects.update']) + ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('message', 'Document uploaded successfully')
            ->assertJsonStructure(['data' => ['id', 'name', 'size', 'mime_type', 'url', 'created_at']]);

        expect($response->json('data.name'))->toBe('document.pdf');
        expect($project->fresh()->getMedia('documents'))->toHaveCount(1);
    });

    it('rejects a disallowed file type', function () {
        Storage::fake('public');
        $project = Project::factory()->create();

        $this->post("/api/v1/projects/{$project->id}/documents", [
            'document' => UploadedFile::fake()->create('note.txt', 10, 'text/plain'),
        ], projectApiBearer(['projects.update']) + ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['document']);
    });

    it('deletes a document', function () {
        Storage::fake('public');
        $project = Project::factory()->create();
        $media = $project->addMedia(projectApiFakePdf())->toMediaCollection('documents');

        $this->deleteJson("/api/v1/projects/{$project->id}/documents/{$media->id}", [], projectApiBearer(['projects.update']))
            ->assertOk()
            ->assertJsonPath('message', 'Document deleted successfully');

        expect($project->fresh()->getMedia('documents'))->toHaveCount(0);
    });

    it('returns 404 deleting a document that belongs to another project', function () {
        Storage::fake('public');
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();
        $media = $projectA->addMedia(projectApiFakePdf())->toMediaCollection('documents');

        $this->deleteJson("/api/v1/projects/{$projectB->id}/documents/{$media->id}", [], projectApiBearer(['projects.update']))
            ->assertNotFound();

        // The media must still exist — the ownership guard blocked the delete.
        expect($projectA->fresh()->getMedia('documents'))->toHaveCount(1);
    });
});
