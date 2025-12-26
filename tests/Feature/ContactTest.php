<?php

use App\Models\Client;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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

    $this->countryId = 1;
    $this->client = Client::factory()->create();
});

describe('Contact Index', function () {
    test('guests are redirected to the login page', function () {
        $this->get('/dashboard/contacts')->assertRedirect('/login');
    });

    test('authenticated users can visit the contacts index', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/contacts')->assertOk();
    });
});

describe('Contact Create', function () {
    test('authenticated users can visit the create contact page', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/contacts/create')->assertOk();
    });

    test('authenticated users can create a contact', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/contacts', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'client_id' => $this->client->id,
            'primary_email' => 'john.doe@example.com',
            'primary_phone' => '+1234567890',
            'additional_phones' => ['+0987654321', '+1122334455'],
            'additional_emails' => ['john.alt@example.com'],
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message', 'Contact created successfully');

        $this->assertDatabaseHas('contacts', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'primary_email' => 'john.doe@example.com',
        ]);
    });

    test('contact creation requires first name, last name, client, and email', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/contacts', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['first_name', 'last_name', 'client_id', 'primary_email']);
    });

    test('contact primary email must be unique', function () {
        $this->actingAs($this->user);

        Contact::factory()->create([
            'primary_email' => 'existing@example.com',
            'client_id' => $this->client->id,
        ]);

        $response = $this->postJson('/dashboard/contacts', [
            'first_name' => 'Test',
            'last_name' => 'Contact',
            'client_id' => $this->client->id,
            'primary_email' => 'existing@example.com',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['primary_email']);
    });

    test('additional emails must be valid emails', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/contacts', [
            'first_name' => 'Test',
            'last_name' => 'Contact',
            'client_id' => $this->client->id,
            'primary_email' => 'test@example.com',
            'additional_emails' => ['invalid-email', 'also-invalid'],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['additional_emails.0', 'additional_emails.1']);
    });

    test('can create contact with multiple additional phones and emails', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/contacts', [
            'first_name' => 'Test',
            'last_name' => 'Contact',
            'client_id' => $this->client->id,
            'primary_email' => 'test@example.com',
            'additional_phones' => ['+1111111111', '+2222222222', '+3333333333'],
            'additional_emails' => ['email1@test.com', 'email2@test.com'],
        ]);

        $response->assertCreated();

        $contact = Contact::where('primary_email', 'test@example.com')->first();
        expect($contact->additional_phones)->toHaveCount(3);
        expect($contact->additional_emails)->toHaveCount(2);
    });
});

describe('Contact Update', function () {
    test('authenticated users can update a contact', function () {
        $this->actingAs($this->user);

        $contact = Contact::factory()->create(['client_id' => $this->client->id]);

        $response = $this->putJson("/dashboard/contacts/{$contact->id}", [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'client_id' => $this->client->id,
            'primary_email' => 'updated@example.com',
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Contact updated successfully');

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'primary_email' => 'updated@example.com',
        ]);
    });

    test('contact email can stay the same on update', function () {
        $this->actingAs($this->user);

        $contact = Contact::factory()->create([
            'primary_email' => 'test@example.com',
            'client_id' => $this->client->id,
        ]);

        $response = $this->putJson("/dashboard/contacts/{$contact->id}", [
            'first_name' => 'Updated',
            'last_name' => 'Contact',
            'client_id' => $this->client->id,
            'primary_email' => 'test@example.com',
        ]);

        $response->assertOk();
    });

    test('can update additional phones and emails', function () {
        $this->actingAs($this->user);

        $contact = Contact::factory()->create([
            'client_id' => $this->client->id,
            'additional_phones' => ['+1111111111'],
            'additional_emails' => ['old@test.com'],
        ]);

        $response = $this->putJson("/dashboard/contacts/{$contact->id}", [
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
            'client_id' => $this->client->id,
            'primary_email' => $contact->primary_email,
            'additional_phones' => ['+9999999999', '+8888888888'],
            'additional_emails' => ['new1@test.com', 'new2@test.com'],
        ]);

        $response->assertOk();

        $contact->refresh();
        expect($contact->additional_phones)->toBe(['+9999999999', '+8888888888']);
        expect($contact->additional_emails)->toBe(['new1@test.com', 'new2@test.com']);
    });
});

describe('Contact Delete', function () {
    test('authenticated users can delete a contact', function () {
        $this->actingAs($this->user);

        $contact = Contact::factory()->create(['client_id' => $this->client->id]);

        $response = $this->deleteJson("/dashboard/contacts/{$contact->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'Contact deleted successfully');

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    });

    test('deleting non-existent contact returns 404', function () {
        $this->actingAs($this->user);

        $response = $this->deleteJson('/dashboard/contacts/99999');

        $response->assertNotFound();
    });
});

describe('Contact Show', function () {
    test('authenticated users can view a contact', function () {
        $this->actingAs($this->user);

        $contact = Contact::factory()->create(['client_id' => $this->client->id]);

        $response = $this->getJson("/dashboard/contacts/{$contact->id}");

        $response->assertOk();
        $response->assertJsonPath('id', $contact->id);
    });

    test('viewing non-existent contact returns 404', function () {
        $this->actingAs($this->user);

        $response = $this->getJson('/dashboard/contacts/99999');

        $response->assertNotFound();
    });
});

describe('Contact Cascade Delete', function () {
    test('deleting a client deletes its contacts', function () {
        $this->actingAs($this->user);

        $client = Client::factory()->create();
        $contact = Contact::factory()->create(['client_id' => $client->id]);

        $contactId = $contact->id;

        // Delete the client
        $client->delete();

        // Contact should also be deleted
        $this->assertDatabaseMissing('contacts', ['id' => $contactId]);
    });
});
