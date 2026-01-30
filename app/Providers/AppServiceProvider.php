<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
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
        //
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
