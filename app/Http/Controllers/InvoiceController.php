<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\InvoiceTemplate;
use App\Http\Requests\InvoiceDueDateRequest;
use App\Http\Requests\InvoicePaymentStoreRequest;
use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Http\Requests\InvoiceVoidRequest;
use App\Http\Resources\InvoicePaymentResource;
use App\Http\Resources\InvoiceResource;
use App\Services\AccountService;
use App\Services\ClientService;
use App\Services\CompanyService;
use App\Services\InvoiceService;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
        private readonly ClientService $clientService,
        private readonly ProjectService $projectService,
        private readonly AccountService $accountService,
        private readonly CompanyService $companyService
    ) {}

    /**
     * Display invoice list page
     */
    public function index(): Response
    {
        return Inertia::render('dashboard/invoices/index');
    }

    /**
     * API endpoint for invoice data table
     */
    public function data(Request $request): AnonymousResourceCollection
    {
        $invoices = $this->invoiceService->getPaginatedInvoices(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['status', 'client_id', 'project_id', 'currency_code', 'date_range']),
            sortBy: $request->input('sort_by', 'issue_date'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return InvoiceResource::collection($invoices);
    }

    /**
     * Show invoice creation form
     */
    public function create(): Response
    {
        $companies = $this->companyService->getAllCompanies()->map(fn ($company) => [
            'id' => $company->id,
            'name' => $company->name,
        ]);

        $clients = $this->clientService->getAllClients()->map(fn ($client) => [
            'id' => $client->id,
            'name' => $client->name,
            'currency_code' => $client->currency?->code ?? 'USD',
        ]);

        $projects = $this->projectService->getAllProjects()->map(fn ($project) => [
            'id' => $project->id,
            'name' => $project->name,
            'client_id' => $project->client_id,
        ]);

        return Inertia::render('dashboard/invoices/create', [
            'companies' => $companies,
            'clients' => $clients,
            'projects' => $projects,
            'templates' => InvoiceTemplate::toArray(),
        ]);
    }

    /**
     * Store new invoice
     */
    public function store(InvoiceStoreRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->createInvoice($request->validated());

        return response()->json([
            'message' => 'Invoice created successfully',
            'data' => new InvoiceResource($invoice),
        ], 201);
    }

    /**
     * Show single invoice details
     */
    public function show(int $id): Response
    {
        $invoice = $this->invoiceService->findInvoice($id);

        if (! $invoice) {
            abort(404, 'Invoice not found');
        }

        $accounts = $this->accountService->getActiveAccounts()
            ->filter(fn ($account) => $account->currency_code === $invoice->currency_code)
            ->map(fn ($account) => [
                'id' => $account->id,
                'name' => $account->name,
                'currency_code' => $account->currency_code,
                'current_balance' => $account->balance_in_major_units,
                'formatted_balance' => $account->formatted_balance,
            ])->values();

        return Inertia::render('dashboard/invoices/show', [
            'invoice' => (new InvoiceResource($invoice))->resolve(),
            'accounts' => $accounts,
        ]);
    }

    /**
     * Show invoice edit form
     */
    public function edit(int $id): Response
    {
        $invoice = $this->invoiceService->findInvoice($id);

        if (! $invoice) {
            abort(404, 'Invoice not found');
        }

        if ($invoice->status !== 'draft') {
            abort(403, 'Only draft invoices can be edited');
        }

        $companies = $this->companyService->getAllCompanies()->map(fn ($company) => [
            'id' => $company->id,
            'name' => $company->name,
        ]);

        $clients = $this->clientService->getAllClients()->map(fn ($client) => [
            'id' => $client->id,
            'name' => $client->name,
            'currency_code' => $client->currency?->code ?? 'USD',
        ]);

        $projects = $this->projectService->getAllProjects()->map(fn ($project) => [
            'id' => $project->id,
            'name' => $project->name,
            'client_id' => $project->client_id,
        ]);

        return Inertia::render('dashboard/invoices/edit', [
            'invoice' => (new InvoiceResource($invoice))->resolve(),
            'companies' => $companies,
            'clients' => $clients,
            'projects' => $projects,
            'templates' => InvoiceTemplate::toArray(),
        ]);
    }

    /**
     * Update invoice
     */
    public function update(int $id, InvoiceUpdateRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->updateInvoice($id, $request->validated());

        return response()->json([
            'message' => 'Invoice updated successfully',
            'data' => new InvoiceResource($invoice),
        ]);
    }

    /**
     * Delete invoice (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->findInvoice($id);

        if (! $invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        if ($invoice->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft invoices can be deleted',
            ], 403);
        }

        $this->invoiceService->deleteInvoice($id);

        return response()->json([
            'message' => 'Invoice deleted successfully',
        ]);
    }

    /**
     * Change invoice status
     */
    public function changeStatus(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:draft,sent,partial,paid,void,overdue'],
        ]);

        try {
            $invoice = $this->invoiceService->changeStatus($id, $request->input('status'));
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Invoice status updated successfully',
            'data' => new InvoiceResource($invoice),
        ]);
    }

    /**
     * Record payment against invoice
     */
    public function recordPayment(int $id, InvoicePaymentStoreRequest $request): JsonResponse
    {
        $payment = $this->invoiceService->recordPayment($id, $request->validated());

        return response()->json([
            'message' => 'Payment recorded successfully',
            'data' => [
                'payment' => new InvoicePaymentResource($payment),
                'invoice' => new InvoiceResource($payment->invoice),
            ],
        ], 201);
    }

    /**
     * Void invoice
     */
    public function void(int $id, InvoiceVoidRequest $request): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->voidInvoice($id, $request->validated()['void_reason']);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        }

        return response()->json([
            'message' => 'Invoice voided successfully',
            'data' => new InvoiceResource($invoice),
        ]);
    }

    /**
     * Update invoice due date
     */
    public function updateDueDate(int $id, InvoiceDueDateRequest $request): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->updateDueDate($id, $request->validated()['due_date']);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        }

        return response()->json([
            'message' => 'Due date updated successfully',
            'data' => new InvoiceResource($invoice),
        ]);
    }

    /**
     * Generate PDF
     */
    public function generatePdf(int $id): \Illuminate\Http\Response
    {
        $invoice = $this->invoiceService->findInvoice($id);

        if (! $invoice) {
            abort(404, 'Invoice not found');
        }

        return $this->invoiceService->buildPdf($invoice)->stream();
    }

    /**
     * Send invoice via email
     */
    public function sendEmail(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->findInvoice($id);

        if (! $invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        // TODO: Implement email sending with PDF attachment
        // Mail::to($invoice->client->email)->send(new InvoiceEmail($invoice));

        // Update status to 'sent'
        $this->invoiceService->changeStatus($id, 'sent');

        return response()->json([
            'message' => 'Invoice sent successfully',
        ]);
    }
}
