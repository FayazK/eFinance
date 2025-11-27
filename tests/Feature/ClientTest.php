<?php

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create minimal world data for testing using DB to bypass model validation
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

    $this->countryId = 1;
    $this->cityId = 1;
    $this->currencyId = 1;
});

describe('Client Index', function () {
    test('guests are redirected to the login page', function () {
        $this->get('/dashboard/clients')->assertRedirect('/login');
    });

    test('authenticated users can visit the clients index', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/clients')->assertOk();
    });
});

describe('Client Create', function () {
    test('authenticated users can visit the create client page', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/clients/create')->assertOk();
    });

    test('authenticated users can create a client', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/clients', [
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'country_id' => $this->countryId,
            'city_id' => $this->cityId,
            'currency_id' => $this->currencyId,
            'address' => '123 Test Street',
            'phone' => '+1234567890',
            'company' => 'Test Company',
            'tax_id' => 'TAX-12345678',
            'website' => 'https://example.com',
            'notes' => 'Test notes',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message', 'Client created successfully');

        $this->assertDatabaseHas('clients', [
            'name' => 'Test Client',
            'email' => 'test@example.com',
        ]);
    });

    test('client creation requires name and email', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/clients', [
            'country_id' => $this->countryId,
            'currency_id' => $this->currencyId,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'email']);
    });

    test('client email must be unique', function () {
        $this->actingAs($this->user);

        Client::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/dashboard/clients', [
            'name' => 'Test Client',
            'email' => 'existing@example.com',
            'country_id' => $this->countryId,
            'currency_id' => $this->currencyId,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email']);
    });
});

describe('Client Update', function () {
    test('authenticated users can update a client', function () {
        $this->actingAs($this->user);

        $client = Client::factory()->create();

        $response = $this->putJson("/dashboard/clients/{$client->id}", [
            'name' => 'Updated Client',
            'email' => 'updated@example.com',
            'country_id' => $this->countryId,
            'currency_id' => $this->currencyId,
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Client updated successfully');

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Client',
            'email' => 'updated@example.com',
        ]);
    });

    test('client email can stay the same on update', function () {
        $this->actingAs($this->user);

        $client = Client::factory()->create(['email' => 'test@example.com']);

        $response = $this->putJson("/dashboard/clients/{$client->id}", [
            'name' => 'Updated Client',
            'email' => 'test@example.com',
            'country_id' => $this->countryId,
            'currency_id' => $this->currencyId,
        ]);

        $response->assertOk();
    });
});

describe('Client Delete', function () {
    test('authenticated users can delete a client', function () {
        $this->actingAs($this->user);

        $client = Client::factory()->create();

        $response = $this->deleteJson("/dashboard/clients/{$client->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'Client deleted successfully');

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    });

    test('deleting non-existent client returns 404', function () {
        $this->actingAs($this->user);

        $response = $this->deleteJson('/dashboard/clients/99999');

        $response->assertNotFound();
    });
});

describe('Client Show', function () {
    test('authenticated users can view a client', function () {
        $this->actingAs($this->user);

        $client = Client::factory()->create();

        $response = $this->getJson("/dashboard/clients/{$client->id}");

        $response->assertOk();
        $response->assertJsonPath('id', $client->id);
    });

    test('viewing non-existent client returns 404', function () {
        $this->actingAs($this->user);

        $response = $this->getJson('/dashboard/clients/99999');

        $response->assertNotFound();
    });
});
