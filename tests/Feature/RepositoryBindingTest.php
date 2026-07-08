<?php

declare(strict_types=1);

use App\Http\Controllers\DropdownController;
use App\Repositories\AccountRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\ContactRepository;
use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Contracts\ContactRepositoryInterface;
use App\Repositories\Contracts\DistributionRepositoryInterface;
use App\Repositories\Contracts\DropdownRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\PayrollRepositoryInterface;
use App\Repositories\Contracts\ProjectLinkRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\ShareholderRepositoryInterface;
use App\Repositories\Contracts\TransactionCategoryRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\TransferRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\DistributionRepository;
use App\Repositories\DropdownRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\PayrollRepository;
use App\Repositories\ProjectLinkRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\RoleRepository;
use App\Repositories\ShareholderRepository;
use App\Repositories\TransactionCategoryRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\TransferRepository;
use App\Repositories\UserRepository;
use App\Services\AccountService;
use App\Services\ClientService;
use App\Services\CompanyService;
use App\Services\ContactService;
use App\Services\DashboardService;
use App\Services\DistributionService;
use App\Services\EmployeeService;
use App\Services\ExpenseService;
use App\Services\InvoiceService;
use App\Services\PayrollService;
use App\Services\ProjectLinkService;
use App\Services\ProjectService;
use App\Services\RoleService;
use App\Services\ShareholderService;
use App\Services\TransactionCategoryService;
use App\Services\TransactionService;
use App\Services\TransferService;
use App\Services\UserService;

it('resolves each repository interface to its concrete implementation', function (string $interface, string $concrete) {
    expect(app($interface))->toBeInstanceOf($concrete);
})->with([
    'account' => [AccountRepositoryInterface::class, AccountRepository::class],
    'client' => [ClientRepositoryInterface::class, ClientRepository::class],
    'company' => [CompanyRepositoryInterface::class, CompanyRepository::class],
    'contact' => [ContactRepositoryInterface::class, ContactRepository::class],
    'distribution' => [DistributionRepositoryInterface::class, DistributionRepository::class],
    'dropdown' => [DropdownRepositoryInterface::class, DropdownRepository::class],
    'employee' => [EmployeeRepositoryInterface::class, EmployeeRepository::class],
    'expense' => [ExpenseRepositoryInterface::class, ExpenseRepository::class],
    'invoice' => [InvoiceRepositoryInterface::class, InvoiceRepository::class],
    'payroll' => [PayrollRepositoryInterface::class, PayrollRepository::class],
    'project' => [ProjectRepositoryInterface::class, ProjectRepository::class],
    'projectLink' => [ProjectLinkRepositoryInterface::class, ProjectLinkRepository::class],
    'role' => [RoleRepositoryInterface::class, RoleRepository::class],
    'shareholder' => [ShareholderRepositoryInterface::class, ShareholderRepository::class],
    'transaction' => [TransactionRepositoryInterface::class, TransactionRepository::class],
    'transactionCategory' => [TransactionCategoryRepositoryInterface::class, TransactionCategoryRepository::class],
    'transfer' => [TransferRepositoryInterface::class, TransferRepository::class],
    'user' => [UserRepositoryInterface::class, UserRepository::class],
]);

it('resolves each repository consumer through the container', function (string $consumer) {
    expect(app($consumer))->toBeInstanceOf($consumer);
})->with([
    AccountService::class,
    ClientService::class,
    CompanyService::class,
    ContactService::class,
    DashboardService::class,
    DistributionService::class,
    DropdownController::class,
    EmployeeService::class,
    ExpenseService::class,
    InvoiceService::class,
    PayrollService::class,
    ProjectLinkService::class,
    ProjectService::class,
    RoleService::class,
    ShareholderService::class,
    TransactionCategoryService::class,
    TransactionService::class,
    TransferService::class,
    UserService::class,
]);
