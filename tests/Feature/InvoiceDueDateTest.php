<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Seed world package data
    $this->artisan('db:seed', ['--class' => 'WorldSeeder']);

    $this->client = Client::factory()->create();

    $this->invoice = Invoice::factory()->create([
        'client_id' => $this->client->id,
        'status' => 'sent',
        'due_date' => now()->addDays(30),
        'issue_date' => now(),
        'currency_code' => $this->client->currency->code ?? 'USD',
        'subtotal' => 100000,
        'tax_amount' => 0,
        'total_amount' => 100000,
        'amount_paid' => 0,
        'balance_due' => 100000,
    ]);
});

describe('Invoice Due Date Editing', function () {
    it('allows updating due date for draft invoice', function () {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'status' => 'draft',
            'due_date' => now()->addDays(30),
            'currency_code' => $this->client->currency->code ?? 'USD',
        ]);

        $newDueDate = now()->addDays(45)->format('Y-m-d');

        $response = $this->putJson("/dashboard/invoices/{$invoice->id}/due-date", [
            'due_date' => $newDueDate,
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Due date updated successfully');

        $invoice->refresh();
        expect($invoice->due_date->format('Y-m-d'))->toBe($newDueDate);
    });

    it('allows updating due date for sent invoice', function () {
        $newDueDate = now()->addDays(60)->format('Y-m-d');

        $response = $this->putJson("/dashboard/invoices/{$this->invoice->id}/due-date", [
            'due_date' => $newDueDate,
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Due date updated successfully');

        $this->invoice->refresh();
        expect($this->invoice->due_date->format('Y-m-d'))->toBe($newDueDate);
    });

    it('allows updating due date for partial invoice', function () {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'status' => 'partial',
            'due_date' => now()->addDays(30),
            'currency_code' => $this->client->currency->code ?? 'USD',
            'total_amount' => 100000,
            'amount_paid' => 50000,
            'balance_due' => 50000,
        ]);

        $newDueDate = now()->addDays(15)->format('Y-m-d');

        $response = $this->putJson("/dashboard/invoices/{$invoice->id}/due-date", [
            'due_date' => $newDueDate,
        ]);

        $response->assertOk();

        $invoice->refresh();
        expect($invoice->due_date->format('Y-m-d'))->toBe($newDueDate);
    });

    it('allows updating due date for overdue invoice', function () {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'status' => 'overdue',
            'due_date' => now()->subDays(10),
            'currency_code' => $this->client->currency->code ?? 'USD',
        ]);

        $newDueDate = now()->addDays(7)->format('Y-m-d');

        $response = $this->putJson("/dashboard/invoices/{$invoice->id}/due-date", [
            'due_date' => $newDueDate,
        ]);

        $response->assertOk();

        $invoice->refresh();
        expect($invoice->due_date->format('Y-m-d'))->toBe($newDueDate);
    });

    it('allows setting due date to past date', function () {
        $pastDate = now()->subDays(5)->format('Y-m-d');

        $response = $this->putJson("/dashboard/invoices/{$this->invoice->id}/due-date", [
            'due_date' => $pastDate,
        ]);

        $response->assertOk();

        $this->invoice->refresh();
        expect($this->invoice->due_date->format('Y-m-d'))->toBe($pastDate);
    });
});

describe('Invoice Due Date Restrictions', function () {
    it('prevents updating due date for paid invoice', function () {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'status' => 'paid',
            'due_date' => now()->addDays(30),
            'currency_code' => $this->client->currency->code ?? 'USD',
            'paid_at' => now(),
        ]);

        $response = $this->putJson("/dashboard/invoices/{$invoice->id}/due-date", [
            'due_date' => now()->addDays(45)->format('Y-m-d'),
        ]);

        $response->assertForbidden();
    });

    it('prevents updating due date for void invoice', function () {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'status' => 'void',
            'due_date' => now()->addDays(30),
            'currency_code' => $this->client->currency->code ?? 'USD',
            'voided_at' => now(),
        ]);

        $response = $this->putJson("/dashboard/invoices/{$invoice->id}/due-date", [
            'due_date' => now()->addDays(45)->format('Y-m-d'),
        ]);

        $response->assertForbidden();
    });

    it('requires due_date field', function () {
        $response = $this->putJson("/dashboard/invoices/{$this->invoice->id}/due-date", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['due_date']);
    });

    it('requires valid date format', function () {
        $response = $this->putJson("/dashboard/invoices/{$this->invoice->id}/due-date", [
            'due_date' => 'not-a-date',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['due_date']);
    });

    it('returns 403 for non-existent invoice', function () {
        $response = $this->putJson('/dashboard/invoices/99999/due-date', [
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $response->assertForbidden(); // Due to authorize() returning false
    });
});

describe('Invoice Due Date Response', function () {
    it('returns updated invoice data in response', function () {
        $newDueDate = now()->addDays(90)->format('Y-m-d');

        $response = $this->putJson("/dashboard/invoices/{$this->invoice->id}/due-date", [
            'due_date' => $newDueDate,
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'due_date',
                    'status',
                    'invoice_number',
                ],
            ])
            ->assertJsonPath('data.due_date', $newDueDate);
    });
});
