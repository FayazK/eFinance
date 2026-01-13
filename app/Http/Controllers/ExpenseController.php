<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseStoreRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Account;
use App\Models\TransactionCategory;
use App\Services\ExpenseService;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function __construct(
        private ExpenseService $expenseService,
        private MediaService $mediaService
    ) {}

    public function index(): Response
    {
        return Inertia::render('dashboard/expenses/index', [
            'accounts' => Account::where('is_active', true)->get(),
            'categories' => TransactionCategory::where('type', 'expense')->get(),
        ]);
    }

    public function data(): AnonymousResourceCollection
    {
        $perPage = request()->integer('per_page', 15);
        $search = request()->string('search')->value();
        $filters = request()->input('filters', []);
        $sortBy = request()->string('sort_by', 'expense_date')->value();
        $sortDirection = request()->string('sort_direction', 'desc')->value();

        $expenses = $this->expenseService->getPaginatedExpenses(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );

        return ExpenseResource::collection($expenses);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/expenses/create', [
            'accounts' => Account::where('is_active', true)
                ->get()
                ->map(fn ($account) => [
                    'id' => $account->id,
                    'name' => $account->name,
                    'currency_code' => $account->currency_code,
                    'formatted_balance' => $account->formatted_balance,
                ]),
            'categories' => TransactionCategory::where('type', 'expense')
                ->get()
                ->map(fn ($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'color' => $category->color,
                ]),
        ]);
    }

    public function store(ExpenseStoreRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            // Handle recurring vs one-time expense
            if ($request->boolean('is_recurring')) {
                $expense = $this->expenseService->createRecurringExpense($validated);
                $message = 'Recurring expense template created successfully!';
            } else {
                $expense = $this->expenseService->createAndProcessExpense($validated);
                $message = 'Expense recorded successfully!';

                // Handle receipt uploads if provided
                if ($request->hasFile('receipts')) {
                    foreach ($request->file('receipts') as $receipt) {
                        $this->mediaService->addMedia($expense, $receipt, 'receipts');
                    }
                }
            }

            return redirect()->route('expenses.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create expense: '.$e->getMessage()]);
        }
    }

    public function show(int $id): Response
    {
        $expense = $this->expenseService->getPaginatedExpenses(1, null, ['id' => $id])->items()[0] ?? null;

        if (! $expense) {
            abort(404);
        }

        return Inertia::render('dashboard/expenses/show', [
            'expense' => $expense,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->expenseService->cancelExpense($id);

            return redirect()->route('expenses.index')
                ->with('success', 'Expense cancelled successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to cancel expense: '.$e->getMessage()]);
        }
    }

    public function lastExchangeRate(string $currency)
    {
        $rate = $this->expenseService->getLastExchangeRate($currency);

        return response()->json(['rate' => $rate]);
    }
}
