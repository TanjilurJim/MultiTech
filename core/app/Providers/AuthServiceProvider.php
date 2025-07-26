<?php

namespace App\Providers;

use App\Models\Customer;          // 👈 import model
use App\Policies\CustomerPolicy;  // 👈 import policy
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Tell Laravel which policy class protects which model.
     * You can register more models here later.
     */
    protected $policies = [
        Customer::class => CustomerPolicy::class,
    ];

    public function boot(): void
    {
        // 🔑 1) Register all policies listed above
        $this->registerPolicies();

        // 🔑 2) Super-admin bypass (your existing Gate::before)
        Gate::before(fn ($user, $ability) =>
            $user->hasRole('super-admin') ? true : null
        );
    }
}
