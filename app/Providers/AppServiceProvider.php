<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** @var array<int, string> */
    private const OVERZICHT_ROLLEN = ['admin', 'tandarts', 'mondhygienist', 'assistent'];

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
        Gate::define('view-appointment-overview', fn (User $user): bool => in_array(
            $user->role?->name,
            self::OVERZICHT_ROLLEN,
            true,
        ));
    }
}
