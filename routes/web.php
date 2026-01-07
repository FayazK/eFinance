<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ProjectLinkController;
use App\Http\Controllers\ShareholderController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('dropdown', DropdownController::class)->name('dropdown');

    Route::prefix('dashboard/users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/data', [UserController::class, 'data'])->name('data');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('dashboard/clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/data', [ClientController::class, 'data'])->name('data');
        Route::get('/create', [ClientController::class, 'create'])->name('create');
        Route::get('/{client}', [ClientController::class, 'show'])->name('show');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('dashboard/contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/data', [ContactController::class, 'data'])->name('data');
        Route::get('/create', [ContactController::class, 'create'])->name('create');
        Route::get('/{contact}', [ContactController::class, 'show'])->name('show');
        Route::get('/{contact}/edit', [ContactController::class, 'edit'])->name('edit');
        Route::post('/', [ContactController::class, 'store'])->name('store');
        Route::put('/{contact}', [ContactController::class, 'update'])->name('update');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('dashboard/projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/data', [ProjectController::class, 'data'])->name('data');
        Route::get('/create', [ProjectController::class, 'create'])->name('create');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
        Route::post('/', [ProjectController::class, 'store'])->name('store');
        Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');

        // Document routes
        Route::post('/{project}/documents', [ProjectDocumentController::class, 'store'])->name('documents.store');
        Route::delete('/{project}/documents/{media}', [ProjectDocumentController::class, 'destroy'])->name('documents.destroy');

        // Link routes
        Route::get('/{project}/links', [ProjectLinkController::class, 'index'])->name('links.index');
        Route::post('/{project}/links', [ProjectLinkController::class, 'store'])->name('links.store');
        Route::put('/{project}/links/{link}', [ProjectLinkController::class, 'update'])->name('links.update');
        Route::delete('/{project}/links/{link}', [ProjectLinkController::class, 'destroy'])->name('links.destroy');
    });

    // Accounts
    Route::prefix('dashboard/accounts')->name('accounts.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('/create', [AccountController::class, 'create'])->name('create');
        Route::get('/{account}', [AccountController::class, 'show'])->name('show');
        Route::get('/{account}/edit', [AccountController::class, 'edit'])->name('edit');
        Route::get('/{account}/transactions', [AccountController::class, 'transactions'])->name('transactions');
        Route::post('/', [AccountController::class, 'store'])->name('store');
        Route::put('/{account}', [AccountController::class, 'update'])->name('update');
        Route::delete('/{account}', [AccountController::class, 'destroy'])->name('destroy');
    });

    // Transactions
    Route::prefix('dashboard/transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/data', [TransactionController::class, 'data'])->name('data');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
    });

    // Transaction Categories
    Route::prefix('dashboard/transaction-categories')->name('transaction-categories.')->group(function () {
        Route::get('/', [TransactionCategoryController::class, 'index'])->name('index');
        Route::get('/data', [TransactionCategoryController::class, 'data'])->name('data');
        Route::post('/', [TransactionCategoryController::class, 'store'])->name('store');
        Route::put('/{category}', [TransactionCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [TransactionCategoryController::class, 'destroy'])->name('destroy');
    });

    // Transfers
    Route::prefix('dashboard/transfers')->name('transfers.')->group(function () {
        Route::get('/', [TransferController::class, 'index'])->name('index');
        Route::get('/data', [TransferController::class, 'data'])->name('data');
        Route::get('/create', [TransferController::class, 'create'])->name('create');
        Route::get('/{transfer}', [TransferController::class, 'show'])->name('show');
        Route::post('/', [TransferController::class, 'store'])->name('store');
    });

    // Expenses
    Route::prefix('dashboard/expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/data', [ExpenseController::class, 'data'])->name('data');
        Route::get('/create', [ExpenseController::class, 'create'])->name('create');
        Route::get('/{id}', [ExpenseController::class, 'show'])->name('show');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
        Route::delete('/{id}', [ExpenseController::class, 'destroy'])->name('destroy');

        // Helper route for exchange rates
        Route::get('/last-exchange-rate/{currency}', [ExpenseController::class, 'lastExchangeRate'])->name('last-exchange-rate');
    });

    // Invoices
    Route::prefix('dashboard/invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/data', [InvoiceController::class, 'data'])->name('data');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');

        // Custom invoice actions
        Route::post('/{id}/change-status', [InvoiceController::class, 'changeStatus'])->name('change-status');
        Route::post('/{id}/record-payment', [InvoiceController::class, 'recordPayment'])->name('record-payment');
        Route::post('/{id}/void', [InvoiceController::class, 'void'])->name('void');
        Route::get('/{id}/pdf', [InvoiceController::class, 'generatePdf'])->name('pdf');
        Route::post('/{id}/send-email', [InvoiceController::class, 'sendEmail'])->name('send-email');
    });

    // Employees
    Route::prefix('dashboard/employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/data', [EmployeeController::class, 'data'])->name('data');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('destroy');
    });

    // Payroll
    Route::prefix('dashboard/payroll')->name('payroll.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('/data', [PayrollController::class, 'data'])->name('data');
        Route::get('/{payroll}', [PayrollController::class, 'show'])->name('show');
        Route::post('/generate', [PayrollController::class, 'generate'])->name('generate');
        Route::put('/{id}/adjustments', [PayrollController::class, 'updateAdjustments'])->name('update-adjustments');
        Route::post('/pay', [PayrollController::class, 'pay'])->name('pay');
    });

    // Shareholders
    Route::prefix('dashboard/shareholders')->name('shareholders.')->group(function () {
        Route::get('/', [ShareholderController::class, 'index'])->name('index');
        Route::get('/data', [ShareholderController::class, 'data'])->name('data');
        Route::get('/validate-equity', [ShareholderController::class, 'validateEquity'])->name('validate-equity');
        Route::post('/', [ShareholderController::class, 'store'])->name('store');
        Route::put('/{id}', [ShareholderController::class, 'update'])->name('update');
        Route::delete('/{id}', [ShareholderController::class, 'destroy'])->name('destroy');
    });

    // Distributions
    Route::prefix('dashboard/distributions')->name('distributions.')->group(function () {
        Route::get('/', [DistributionController::class, 'index'])->name('index');
        Route::get('/create', [DistributionController::class, 'create'])->name('create');
        Route::get('/data', [DistributionController::class, 'data'])->name('data');
        Route::get('/{id}', [DistributionController::class, 'show'])->name('show');
        Route::post('/', [DistributionController::class, 'store'])->name('store');
        Route::put('/{id}', [DistributionController::class, 'update'])->name('update');
        Route::delete('/{id}', [DistributionController::class, 'destroy'])->name('destroy');
        Route::put('/{id}/adjust-profit', [DistributionController::class, 'adjustProfit'])->name('adjust-profit');
        Route::post('/{id}/process', [DistributionController::class, 'process'])->name('process');
        Route::get('/{id}/statements/{shareholderId}', [DistributionController::class, 'downloadStatement'])->name('download-statement');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
