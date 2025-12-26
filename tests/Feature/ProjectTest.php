<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create minimal world data for testing
    DB::table('countries')->insertOrIgnore([
        'id' => 1,
        'name' => 'Test Country',
        'iso2' => 'TC',
        'iso3' => 'TST',
        'phone_code' => '+1',
        'region' => 'Test',
        'subregion' => 'Test',
    ]);

    DB::table('states')->insertOrIgnore([
        'id' => 1,
        'name' => 'Test State',
        'country_id' => 1,
        'country_code' => 'TC',
    ]);

    DB::table('cities')->insertOrIgnore([
        'id' => 1,
        'name' => 'Test City',
        'country_id' => 1,
        'state_id' => 1,
        'country_code' => 'TC',
    ]);

    DB::table('currencies')->insertOrIgnore([
        'id' => 1,
        'name' => 'Test Dollar',
        'code' => 'TSD',
        'country_id' => 1,
        'precision' => 2,
        'symbol' => '$',
        'symbol_native' => '$',
        'symbol_first' => true,
        'decimal_mark' => '.',
        'thousands_separator' => ',',
    ]);

    $this->client = Client::factory()->create();
});

describe('Project Index', function () {
    test('guests are redirected to the login page', function () {
        $this->get('/dashboard/projects')->assertRedirect('/login');
    });

    test('authenticated users can visit the projects index', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/projects')->assertOk();
    });
});

describe('Project Create', function () {
    test('authenticated users can visit the create project page', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/projects/create')->assertOk();
    });

    test('authenticated users can create a project', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/projects', [
            'name' => 'Test Project',
            'description' => 'Test description',
            'client_id' => $this->client->id,
            'start_date' => '2025-01-01',
            'completion_date' => '2025-12-31',
            'status' => 'Planning',
            'budget' => 50000.00,
            'actual_cost' => 10000.00,
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message', 'Project created successfully');

        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'client_id' => $this->client->id,
        ]);
    });

    test('project creation requires name and client', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/projects', [
            'status' => 'Planning',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'client_id']);
    });

    test('completion date must be after or equal to start date', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/projects', [
            'name' => 'Test Project',
            'client_id' => $this->client->id,
            'start_date' => '2025-12-31',
            'completion_date' => '2025-01-01',
            'status' => 'Planning',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['completion_date']);
    });

    test('status must be valid enum value', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/projects', [
            'name' => 'Test Project',
            'client_id' => $this->client->id,
            'status' => 'InvalidStatus',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['status']);
    });
});

describe('Project Update', function () {
    test('authenticated users can update a project', function () {
        $this->actingAs($this->user);

        $project = Project::factory()->create(['client_id' => $this->client->id]);

        $response = $this->putJson("/dashboard/projects/{$project->id}", [
            'name' => 'Updated Project',
            'client_id' => $this->client->id,
            'status' => 'Active',
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Project updated successfully');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project',
            'status' => 'Active',
        ]);
    });
});

describe('Project Delete', function () {
    test('authenticated users can delete a project', function () {
        $this->actingAs($this->user);

        $project = Project::factory()->create(['client_id' => $this->client->id]);

        $response = $this->deleteJson("/dashboard/projects/{$project->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'Project deleted successfully');

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    });

    test('deleting non-existent project returns 404', function () {
        $this->actingAs($this->user);

        $response = $this->deleteJson('/dashboard/projects/99999');

        $response->assertNotFound();
    });
});

describe('Project Show', function () {
    test('authenticated users can view a project', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $project = Project::factory()->create(['client_id' => $this->client->id]);

        $response = $this->get("/dashboard/projects/{$project->id}");

        $response->assertOk();
    });
});

describe('Project Documents', function () {
    test('authenticated users can upload documents', function () {
        Storage::fake('public');
        $this->actingAs($this->user);

        $project = Project::factory()->create(['client_id' => $this->client->id]);

        // Create a fake PDF with actual PDF content
        $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000056 00000 n\n0000000115 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n206\n%%EOF";
        $file = UploadedFile::fake()->createWithContent('document.pdf', $pdfContent);

        $response = $this->postJson("/dashboard/projects/{$project->id}/documents", [
            'document' => $file,
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message', 'Document uploaded successfully');

        expect($project->fresh()->getMedia('documents'))->toHaveCount(1);
    });

    test('only allowed file types can be uploaded', function () {
        Storage::fake('public');
        $this->actingAs($this->user);

        $project = Project::factory()->create(['client_id' => $this->client->id]);
        $file = UploadedFile::fake()->create('document.txt', 100, 'text/plain');

        $response = $this->postJson("/dashboard/projects/{$project->id}/documents", [
            'document' => $file,
        ]);

        $response->assertUnprocessable();
    });

    test('authenticated users can delete documents', function () {
        Storage::fake('public');
        $this->actingAs($this->user);

        $project = Project::factory()->create(['client_id' => $this->client->id]);

        // Create a fake PDF with actual PDF content
        $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000056 00000 n\n0000000115 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n206\n%%EOF";
        $file = UploadedFile::fake()->createWithContent('document.pdf', $pdfContent);
        $media = $project->addMedia($file)->toMediaCollection('documents');

        $response = $this->deleteJson("/dashboard/projects/{$project->id}/documents/{$media->id}");

        $response->assertOk();
        expect($project->fresh()->getMedia('documents'))->toHaveCount(0);
    });
});

describe('Project Links', function () {
    test('authenticated users can create links', function () {
        $this->actingAs($this->user);

        $project = Project::factory()->create(['client_id' => $this->client->id]);

        $response = $this->postJson("/dashboard/projects/{$project->id}/links", [
            'title' => 'Test Link',
            'url' => 'https://example.com',
            'description' => 'Test description',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message', 'Link created successfully');

        $this->assertDatabaseHas('project_links', [
            'project_id' => $project->id,
            'title' => 'Test Link',
        ]);
    });

    test('link url must be valid', function () {
        $this->actingAs($this->user);

        $project = Project::factory()->create(['client_id' => $this->client->id]);

        $response = $this->postJson("/dashboard/projects/{$project->id}/links", [
            'title' => 'Test Link',
            'url' => 'not-a-url',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['url']);
    });

    test('authenticated users can update links', function () {
        $this->actingAs($this->user);

        $project = Project::factory()->create(['client_id' => $this->client->id]);
        $link = $project->links()->create([
            'title' => 'Original Title',
            'url' => 'https://original.com',
        ]);

        $response = $this->putJson("/dashboard/projects/{$project->id}/links/{$link->id}", [
            'title' => 'Updated Title',
            'url' => 'https://updated.com',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('project_links', [
            'id' => $link->id,
            'title' => 'Updated Title',
        ]);
    });

    test('authenticated users can delete links', function () {
        $this->actingAs($this->user);

        $project = Project::factory()->create(['client_id' => $this->client->id]);
        $link = $project->links()->create([
            'title' => 'Test Link',
            'url' => 'https://example.com',
        ]);

        $response = $this->deleteJson("/dashboard/projects/{$project->id}/links/{$link->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('project_links', ['id' => $link->id]);
    });
});
