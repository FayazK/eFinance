<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Repositories\ClientRepository;
use App\Repositories\ContactRepository;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\ContactRepositoryInterface;
use App\Repositories\Contracts\ProjectLinkRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\ProjectLinkRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
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
