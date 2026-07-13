<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
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
use App\Http\Controllers\RoleController;
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
        Route::get('/', [UserController::class, 'index'])->middleware('permission:users.read')->name('index');
        Route::get('/data', [UserController::class, 'data'])->middleware('permission:users.read')->name('data');
        Route::get('/create', [UserController::class, 'create'])->middleware('permission:users.create')->name('create');
        Route::get('/{user}', [UserController::class, 'show'])->middleware('permission:users.read')->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->middleware('permission:users.update')->name('edit');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create')->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:users.update')->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('destroy');
    });

    Route::prefix('dashboard/clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->middleware('permission:clients.read')->name('index');
        Route::get('/data', [ClientController::class, 'data'])->middleware('permission:clients.read')->name('data');
        Route::get('/create', [ClientController::class, 'create'])->middleware('permission:clients.create')->name('create');
        Route::get('/{client}', [ClientController::class, 'show'])->middleware('permission:clients.read')->name('show');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->middleware('permission:clients.update')->name('edit');
        Route::post('/', [ClientController::class, 'store'])->middleware('permission:clients.create')->name('store');
        Route::put('/{client}', [ClientController::class, 'update'])->middleware('permission:clients.update')->name('update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->middleware('permission:clients.delete')->name('destroy');
    });

    Route::prefix('dashboard/contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->middleware('permission:contacts.read')->name('index');
        Route::get('/data', [ContactController::class, 'data'])->middleware('permission:contacts.read')->name('data');
        Route::get('/create', [ContactController::class, 'create'])->middleware('permission:contacts.create')->name('create');
        Route::get('/{contact}', [ContactController::class, 'show'])->middleware('permission:contacts.read')->name('show');
        Route::get('/{contact}/edit', [ContactController::class, 'edit'])->middleware('permission:contacts.update')->name('edit');
        Route::post('/', [ContactController::class, 'store'])->middleware('permission:contacts.create')->name('store');
        Route::put('/{contact}', [ContactController::class, 'update'])->middleware('permission:contacts.update')->name('update');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->middleware('permission:contacts.delete')->name('destroy');
    });

    // Companies
    Route::prefix('dashboard/companies')->name('companies.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->middleware('permission:companies.read')->name('index');
        Route::get('/data', [CompanyController::class, 'data'])->middleware('permission:companies.read')->name('data');
        Route::get('/create', [CompanyController::class, 'create'])->middleware('permission:companies.create')->name('create');
        Route::get('/{company}', [CompanyController::class, 'show'])->middleware('permission:companies.read')->name('show');
        Route::get('/{company}/edit', [CompanyController::class, 'edit'])->middleware('permission:companies.update')->name('edit');
        Route::post('/', [CompanyController::class, 'store'])->middleware('permission:companies.create')->name('store');
        Route::put('/{company}', [CompanyController::class, 'update'])->middleware('permission:companies.update')->name('update');
        Route::delete('/{company}', [CompanyController::class, 'destroy'])->middleware('permission:companies.delete')->name('destroy');
    });

    Route::prefix('dashboard/projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->middleware('permission:projects.read')->name('index');
        Route::get('/data', [ProjectController::class, 'data'])->middleware('permission:projects.read')->name('data');
        Route::get('/create', [ProjectController::class, 'create'])->middleware('permission:projects.create')->name('create');
        Route::get('/{project}', [ProjectController::class, 'show'])->middleware('permission:projects.read')->name('show');
        Route::get('/{project}/edit', [ProjectController::class, 'edit'])->middleware('permission:projects.update')->name('edit');
        Route::post('/', [ProjectController::class, 'store'])->middleware('permission:projects.create')->name('store');
        Route::put('/{project}', [ProjectController::class, 'update'])->middleware('permission:projects.update')->name('update');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->middleware('permission:projects.delete')->name('destroy');

        // Document routes
        Route::post('/{project}/documents', [ProjectDocumentController::class, 'store'])->middleware('permission:projects.update')->name('documents.store');
        Route::delete('/{project}/documents/{media}', [ProjectDocumentController::class, 'destroy'])->middleware('permission:projects.update')->name('documents.destroy');

        // Link routes
        Route::get('/{project}/links', [ProjectLinkController::class, 'index'])->middleware('permission:projects.read')->name('links.index');
        Route::post('/{project}/links', [ProjectLinkController::class, 'store'])->middleware('permission:projects.update')->name('links.store');
        Route::put('/{project}/links/{link}', [ProjectLinkController::class, 'update'])->middleware('permission:projects.update')->name('links.update');
        Route::delete('/{project}/links/{link}', [ProjectLinkController::class, 'destroy'])->middleware('permission:projects.update')->name('links.destroy');
    });

    // Accounts
    Route::prefix('dashboard/accounts')->name('accounts.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->middleware('permission:accounts.read')->name('index');
        Route::get('/create', [AccountController::class, 'create'])->middleware('permission:accounts.create')->name('create');
        Route::get('/{account}', [AccountController::class, 'show'])->middleware('permission:accounts.read')->name('show');
        Route::get('/{account}/edit', [AccountController::class, 'edit'])->middleware('permission:accounts.update')->name('edit');
        Route::get('/{account}/transactions', [AccountController::class, 'transactions'])->middleware('permission:accounts.read')->name('transactions');
        Route::post('/', [AccountController::class, 'store'])->middleware('permission:accounts.create')->name('store');
        Route::put('/{account}', [AccountController::class, 'update'])->middleware('permission:accounts.update')->name('update');
        Route::delete('/{account}', [AccountController::class, 'destroy'])->middleware('permission:accounts.delete')->name('destroy');
    });

    // Transactions
    Route::prefix('dashboard/transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->middleware('permission:transactions.read')->name('index');
        Route::get('/data', [TransactionController::class, 'data'])->middleware('permission:transactions.read')->name('data');
        Route::get('/create', [TransactionController::class, 'create'])->middleware('permission:transactions.create')->name('create');
        Route::post('/', [TransactionController::class, 'store'])->middleware('permission:transactions.create')->name('store');
    });

    // Transaction Categories
    Route::prefix('dashboard/transaction-categories')->name('transaction-categories.')->group(function () {
        Route::get('/', [TransactionCategoryController::class, 'index'])->middleware('permission:transaction_categories.read')->name('index');
        Route::get('/data', [TransactionCategoryController::class, 'data'])->middleware('permission:transaction_categories.read')->name('data');
        Route::post('/', [TransactionCategoryController::class, 'store'])->middleware('permission:transaction_categories.create')->name('store');
        Route::put('/{category}', [TransactionCategoryController::class, 'update'])->middleware('permission:transaction_categories.update')->name('update');
        Route::delete('/{category}', [TransactionCategoryController::class, 'destroy'])->middleware('permission:transaction_categories.delete')->name('destroy');
    });

    // Transfers
    Route::prefix('dashboard/transfers')->name('transfers.')->group(function () {
        Route::get('/', [TransferController::class, 'index'])->middleware('permission:transfers.read')->name('index');
        Route::get('/data', [TransferController::class, 'data'])->middleware('permission:transfers.read')->name('data');
        Route::get('/create', [TransferController::class, 'create'])->middleware('permission:transfers.create')->name('create');
        Route::get('/{transfer}', [TransferController::class, 'show'])->middleware('permission:transfers.read')->name('show');
        Route::post('/', [TransferController::class, 'store'])->middleware('permission:transfers.create')->name('store');
    });

    // Expenses
    Route::prefix('dashboard/expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->middleware('permission:expenses.read')->name('index');
        Route::get('/data', [ExpenseController::class, 'data'])->middleware('permission:expenses.read')->name('data');
        Route::get('/create', [ExpenseController::class, 'create'])->middleware('permission:expenses.create')->name('create');
        Route::get('/{id}', [ExpenseController::class, 'show'])->middleware('permission:expenses.read')->name('show')->whereNumber('id');
        Route::get('/{id}/edit', [ExpenseController::class, 'edit'])->middleware('permission:expenses.update')->name('edit')->whereNumber('id');
        Route::post('/', [ExpenseController::class, 'store'])->middleware('permission:expenses.create')->name('store');
        Route::put('/{id}', [ExpenseController::class, 'update'])->middleware('permission:expenses.update')->name('update')->whereNumber('id');
        Route::post('/{id}/process', [ExpenseController::class, 'process'])->middleware('permission:expenses.update')->name('process')->whereNumber('id');
        Route::post('/{id}/void', [ExpenseController::class, 'void'])->middleware('permission:expenses.update')->name('void')->whereNumber('id');
        Route::delete('/{id}', [ExpenseController::class, 'destroy'])->middleware('permission:expenses.delete')->name('destroy');

        // Helper route for exchange rates
        Route::get('/last-exchange-rate/{currency}', [ExpenseController::class, 'lastExchangeRate'])->middleware('permission:expenses.read')->name('last-exchange-rate');
    });

    // Invoices
    Route::prefix('dashboard/invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->middleware('permission:invoices.read')->name('index');
        Route::get('/data', [InvoiceController::class, 'data'])->middleware('permission:invoices.read')->name('data');
        Route::get('/create', [InvoiceController::class, 'create'])->middleware('permission:invoices.create')->name('create');
        Route::get('/{id}', [InvoiceController::class, 'show'])->middleware('permission:invoices.read')->name('show');
        Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->middleware('permission:invoices.update')->name('edit');
        Route::post('/', [InvoiceController::class, 'store'])->middleware('permission:invoices.create')->name('store');
        Route::put('/{id}', [InvoiceController::class, 'update'])->middleware('permission:invoices.update')->name('update');
        Route::delete('/{id}', [InvoiceController::class, 'destroy'])->middleware('permission:invoices.delete')->name('destroy');

        // Custom invoice actions
        Route::post('/{id}/change-status', [InvoiceController::class, 'changeStatus'])->middleware('permission:invoices.update')->name('change-status');
        Route::post('/{id}/record-payment', [InvoiceController::class, 'recordPayment'])->middleware('permission:invoices.update')->name('record-payment');
        Route::post('/{id}/void', [InvoiceController::class, 'void'])->middleware('permission:invoices.update')->name('void');
        Route::put('/{id}/due-date', [InvoiceController::class, 'updateDueDate'])->middleware('permission:invoices.update')->name('update-due-date');
        Route::get('/{id}/pdf', [InvoiceController::class, 'generatePdf'])->middleware('permission:invoices.read')->name('pdf');
        Route::post('/{id}/send-email', [InvoiceController::class, 'sendEmail'])->middleware('permission:invoices.update')->name('send-email');
    });

    // Employees
    Route::prefix('dashboard/employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->middleware('permission:employees.read')->name('index');
        Route::get('/data', [EmployeeController::class, 'data'])->middleware('permission:employees.read')->name('data');
        Route::get('/create', [EmployeeController::class, 'create'])->middleware('permission:employees.create')->name('create');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->middleware('permission:employees.read')->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->middleware('permission:employees.update')->name('edit');
        Route::post('/', [EmployeeController::class, 'store'])->middleware('permission:employees.create')->name('store');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->middleware('permission:employees.update')->name('update');
        Route::delete('/{id}', [EmployeeController::class, 'destroy'])->middleware('permission:employees.delete')->name('destroy');
    });

    // Payroll
    Route::prefix('dashboard/payroll')->name('payroll.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->middleware('permission:payroll.read')->name('index');
        Route::get('/data', [PayrollController::class, 'data'])->middleware('permission:payroll.read')->name('data');
        Route::get('/{payroll}', [PayrollController::class, 'show'])->middleware('permission:payroll.read')->name('show');
        Route::post('/generate', [PayrollController::class, 'generate'])->middleware('permission:payroll.update')->name('generate');
        Route::put('/{id}/adjustments', [PayrollController::class, 'updateAdjustments'])->middleware('permission:payroll.update')->name('update-adjustments');
        Route::post('/pay', [PayrollController::class, 'pay'])->middleware('permission:payroll.update')->name('pay');
    });

    // Shareholders — create/edit are modal-only; no GET /create or GET /{id} page by design.
    // {id} is numeric-constrained so "create" falls through to a clean 404 (not a 405).
    Route::prefix('dashboard/shareholders')->name('shareholders.')->group(function () {
        Route::get('/', [ShareholderController::class, 'index'])->middleware('permission:shareholders.read')->name('index');
        Route::get('/data', [ShareholderController::class, 'data'])->middleware('permission:shareholders.read')->name('data');
        Route::get('/validate-equity', [ShareholderController::class, 'validateEquity'])->middleware('permission:shareholders.read')->name('validate-equity');
        Route::post('/', [ShareholderController::class, 'store'])->middleware('permission:shareholders.create')->name('store');
        Route::put('/{id}', [ShareholderController::class, 'update'])->middleware('permission:shareholders.update')->name('update')->whereNumber('id');
        Route::delete('/{id}', [ShareholderController::class, 'destroy'])->middleware('permission:shareholders.delete')->name('destroy')->whereNumber('id');
    });

    // Distributions
    Route::prefix('dashboard/distributions')->name('distributions.')->group(function () {
        Route::get('/', [DistributionController::class, 'index'])->middleware('permission:distributions.read')->name('index');
        Route::get('/create', [DistributionController::class, 'create'])->middleware('permission:distributions.create')->name('create');
        Route::get('/data', [DistributionController::class, 'data'])->middleware('permission:distributions.read')->name('data');
        Route::get('/{id}', [DistributionController::class, 'show'])->middleware('permission:distributions.read')->name('show');
        Route::post('/', [DistributionController::class, 'store'])->middleware('permission:distributions.create')->name('store');
        Route::put('/{id}', [DistributionController::class, 'update'])->middleware('permission:distributions.update')->name('update');
        Route::delete('/{id}', [DistributionController::class, 'destroy'])->middleware('permission:distributions.delete')->name('destroy');
        Route::put('/{id}/adjust-profit', [DistributionController::class, 'adjustProfit'])->middleware('permission:distributions.update')->name('adjust-profit');
        Route::post('/{id}/process', [DistributionController::class, 'process'])->middleware('permission:distributions.update')->name('process');
        Route::get('/{id}/statements/{shareholderId}', [DistributionController::class, 'downloadStatement'])->middleware('permission:distributions.read')->name('download-statement');
    });

    // Activities (Activity Log API)
    Route::get('dashboard/activities/{type}/{id}', [ActivityController::class, 'index'])->name('activities.index');

    // Roles
    Route::prefix('dashboard/roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('permission:roles.read')->name('index');
        Route::get('/data', [RoleController::class, 'data'])->middleware('permission:roles.read')->name('data');
        Route::get('/assignable', [RoleController::class, 'assignable'])->middleware('permission:roles.read')->name('assignable');
        Route::get('/create', [RoleController::class, 'create'])->middleware('permission:roles.create')->name('create');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:roles.update')->name('edit');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('store');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('permission:roles.update')->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('destroy');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
