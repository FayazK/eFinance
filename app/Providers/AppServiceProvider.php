<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
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
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(ProjectLinkRepositoryInterface::class, ProjectLinkRepository::class);
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(DistributionRepositoryInterface::class, DistributionRepository::class);
        $this->app->bind(DropdownRepositoryInterface::class, DropdownRepository::class);
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, ExpenseRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(PayrollRepositoryInterface::class, PayrollRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(ShareholderRepositoryInterface::class, ShareholderRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(TransactionCategoryRepositoryInterface::class, TransactionCategoryRepository::class);
        $this->app->bind(TransferRepositoryInterface::class, TransferRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent lazy loading in non-production environments
        Model::preventLazyLoading(! app()->isProduction());

        // Register permission gates
        $this->registerPermissionGates();

        // Rate limiter for the /api/v1 surface: throttle per authenticated token/user, or per IP when unauthenticated.
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)
            ->by($request->user()?->id ?: $request->ip()));
    }

    /**
     * Register all permission gates based on the permissions config.
     */
    protected function registerPermissionGates(): void
    {
        // Super admin bypasses all gate checks
        Gate::before(function (User $user, string $ability) {
            if ($user->is_super_admin) {
                return true;
            }

            return null;
        });

        // Register gates for each permission defined in config
        $modules = config('permissions.modules', []);

        foreach ($modules as $module => $config) {
            foreach ($config['permissions'] as $action) {
                $permission = "{$module}.{$action}";

                Gate::define($permission, function (User $user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            }
        }
    }
}
