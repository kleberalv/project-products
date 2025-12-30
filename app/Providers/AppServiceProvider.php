<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\EloquentProductRepository;
use App\Repositories\ProductRepositoryInterface;
use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar bindings de repositório com injeção de dependência
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Usar o modelo customizado de tokens do Sanctum com SoftDeletes
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
