<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Declaration;
use App\Models\Document;
use App\Models\Dossier;
use App\Models\Employee;
use App\Policies\ClientPolicy;
use App\Policies\DeclarationPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\DossierPolicy;
use App\Policies\EmployeePolicy;
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
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Declaration::class, DeclarationPolicy::class);
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(Dossier::class, DossierPolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
    }
}
