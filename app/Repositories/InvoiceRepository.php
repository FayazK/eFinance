<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository
{
    public function find(int $id): ?Invoice
    {
        return Invoice::with(['company', 'client', 'project', 'items', 'payments.account'])
            ->find($id);
    }

    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }

    public function update(int $id, array $data): Invoice
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($data);

        return $invoice->fresh(['company', 'client', 'project', 'items', 'payments']);
    }

    public function delete(int $id): bool
    {
        $invoice = Invoice::findOrFail($id);

        return $invoice->delete();
    }

    public function paginateInvoices(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'issue_date',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Invoice::query()->with(['company', 'client', 'project']);

        // Search by invoice number or client name
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filters
        if ($filters) {
            foreach ($filters as $column => $value) {
                if ($value !== null && $value !== '') {
                    if ($column === 'status') {
                        $query->where('status', $value);
                    } elseif ($column === 'client_id') {
                        $query->where('client_id', $value);
                    } elseif ($column === 'project_id') {
                        $query->where('project_id', $value);
                    } elseif ($column === 'currency_code') {
                        $query->where('currency_code', $value);
                    } elseif ($column === 'date_range' && is_array($value) && count($value) === 2) {
                        $query->whereBetween('issue_date', $value);
                    }
                }
            }
        }

        $allowedSortColumns = ['invoice_number', 'issue_date', 'due_date', 'total_amount', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }

    public function getClientInvoices(int $clientId, int $perPage = 20): LengthAwarePaginator
    {
        return Invoice::with(['items', 'payments'])
            ->where('client_id', $clientId)
            ->orderBy('issue_date', 'desc')
            ->paginate($perPage);
    }

    public function getTotalReceivables(): int
    {
        return Invoice::whereIn('status', ['sent', 'partial', 'overdue'])
            ->sum('balance_due');
    }

    public function getOverdueInvoices(): Collection
    {
        return Invoice::with(['client'])
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->where('due_date', '<', now())
            ->get();
    }

    public function markOverdueInvoices(): int
    {
        return Invoice::whereIn('status', ['sent', 'partial'])
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);
    }
}
