<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoicePaymentStoreRequest;
use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Http\Requests\InvoiceVoidRequest;
use App\Http\Resources\InvoicePaymentResource;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * JSON REST surface for invoices. Reuses the web module's InvoiceService,
 * Resources, and Form Requests; every id-scoped action returns a clean 404
 * for a missing invoice. Business-rule violations raised by the service
 * (bad status transition, insufficient balance, etc.) are mapped to 422/403.
 */
class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService
    ) {}

    /**
     * Paginated list of invoices. Money is exposed in major units; nested
     * items/payments are omitted here (loaded only on the single-invoice reads).
     */
    public function index(Request $request): AnonymousResourceCollection
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

    public function show(int $id): InvoiceResource
    {
        return new InvoiceResource($this->findOrFail($id));
    }

    public function store(InvoiceStoreRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->createInvoice($request->validated());

        // createInvoice() only loads items/client/project; top up company + payments.account
        // so the resource serializes fully without a lazy-load violation.
        return (new InvoiceResource($invoice->load(['company', 'client', 'project', 'items', 'payments.account'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(int $id, InvoiceUpdateRequest $request): InvoiceResource
    {
        // The request's authorize() already 403s a non-draft invoice; a missing one
        // falls through to here for a clean 404. updateInvoice() returns a fresh() load.
        $this->findOrFail($id);

        return new InvoiceResource($this->invoiceService->updateInvoice($id, $request->validated()));
    }

    public function destroy(int $id): JsonResponse
    {
        $invoice = $this->findOrFail($id);

        if ($invoice->status !== 'draft') {
            return response()->json(['message' => 'Only draft invoices can be deleted'], 403);
        }

        $this->invoiceService->deleteInvoice($id);

        return response()->json(['message' => 'Invoice deleted successfully']);
    }

    public function changeStatus(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:draft,sent,partial,paid,void,overdue'],
        ]);

        $this->findOrFail($id);

        try {
            $invoice = $this->invoiceService->changeStatus($id, $request->input('status'));
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Invoice status updated successfully',
            'data' => new InvoiceResource($invoice),
        ]);
    }

    public function recordPayment(int $id, InvoicePaymentStoreRequest $request): JsonResponse
    {
        $this->findOrFail($id);

        try {
            $payment = $this->invoiceService->recordPayment($id, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        // Return the payment resource for response-shape consistency (#104).
        return (new InvoicePaymentResource($payment))
            ->response()
            ->setStatusCode(201);
    }

    public function void(int $id, InvoiceVoidRequest $request): JsonResponse
    {
        $this->findOrFail($id);

        try {
            $invoice = $this->invoiceService->voidInvoice($id, $request->validated()['void_reason']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json([
            'message' => 'Invoice voided successfully',
            'data' => new InvoiceResource($invoice),
        ]);
    }

    /**
     * Validated inline (not via InvoiceDueDateRequest) so the 404 guard runs
     * before the request's authorize() would otherwise 403 a missing invoice.
     */
    public function updateDueDate(int $id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'due_date' => ['required', 'date'],
        ]);

        $this->findOrFail($id);

        try {
            $invoice = $this->invoiceService->updateDueDate($id, $validated['due_date']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json([
            'message' => 'Due date updated successfully',
            'data' => new InvoiceResource($invoice),
        ]);
    }

    /**
     * Email delivery is not yet implemented (mirrors the web stub); this flips
     * the invoice to "sent" and acknowledges.
     */
    public function sendEmail(int $id): JsonResponse
    {
        $this->findOrFail($id);

        try {
            $this->invoiceService->changeStatus($id, 'sent');
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Invoice sent successfully']);
    }

    public function pdf(int $id): Response
    {
        return $this->invoiceService->buildPdf($this->findOrFail($id))->download();
    }

    /**
     * Resolve a fully eager-loaded invoice or abort with a 404.
     */
    private function findOrFail(int $id): Invoice
    {
        $invoice = $this->invoiceService->findInvoice($id);

        abort_if($invoice === null, 404, 'Invoice not found');

        return $invoice;
    }
}
