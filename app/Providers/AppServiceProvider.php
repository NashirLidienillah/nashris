<?php

namespace App\Providers;

use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Policies\PayrollItemPolicy;
use App\Policies\PayrollPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
        Gate::policy(Payroll::class, PayrollPolicy::class);
        Gate::policy(PayrollItem::class, PayrollItemPolicy::class);

        if (
            request()->server('HTTP_X_FORWARDED_PROTO') === 'https' ||
            request()->header('X-Forwarded-Proto') === 'https' ||
            request()->isSecure() ||
            app()->environment('production')
        ) {
            URL::forceScheme('https');
        }
    }
}
