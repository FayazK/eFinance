<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Invoice;
use App\Services\InvoiceService;

describe('Invoice Number Generation', function () {
    beforeEach(function () {
        seedMinimalWorld();

        $this->service = app(InvoiceService::class);
    });

    test('continues the sequence past 999 instead of resetting to 001', function () {
        // The factory pads a random 4-digit sequence, so seed the trigger row explicitly.
        Invoice::factory()->create([
            'invoice_number' => 'INV-'.now()->year.'-1000',
        ]);

        $client = Client::factory()->create();

        $invoice = $this->service->createInvoice([
            'client_id' => $client->id,
            'currency_code' => $client->currency->code,
            'issue_date' => now()->year.'-01-01',
            'due_date' => now()->year.'-01-31',
            'items' => [
                [
                    'description' => 'Consulting',
                    'quantity' => 1,
                    'unit_price' => 100.00,
                ],
            ],
        ]);

        expect($invoice->invoice_number)->toBe('INV-'.now()->year.'-1001');
    });
});
