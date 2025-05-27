<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\SucursalRepositoryInterface;
use App\Repositories\Eloquent\EloquentSucursalRepository;
use App\Contracts\Repositories\ProveedorRepositoryInterface;
use App\Repositories\Eloquent\EloquentProveedorRepository;
use App\Contracts\Repositories\ColorRepositoryInterface;
use App\Repositories\Eloquent\EloquentColorRepository;
use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Repositories\Eloquent\EloquentCategoriaRepository;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Repositories\Eloquent\EloquentProductRepository;
use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Repositories\Eloquent\EloquentMaterialRepository;
use App\Contracts\Repositories\InventarioRepositoryInterface;
use App\Repositories\Eloquent\EloquentInventarioRepository;
use App\Contracts\Repositories\MovimientoInventarioRepositoryInterface;
use App\Repositories\Eloquent\EloquentMovimientoInventarioRepository;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentUserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            SucursalRepositoryInterface::class,
            EloquentSucursalRepository::class
        );

        $this->app->bind(
            ProveedorRepositoryInterface::class,
            EloquentProveedorRepository::class
        );

        $this->app->bind(
            ColorRepositoryInterface::class,
            EloquentColorRepository::class
        );

        $this->app->bind(
            CategoriaRepositoryInterface::class,
            EloquentCategoriaRepository::class
        );

        $this->app->bind(
            ProductRepositoryInterface::class,
            EloquentProductRepository::class
        );

        $this->app->bind(
            MaterialRepositoryInterface::class,
            EloquentMaterialRepository::class
        );

        $this->app->bind(
            InventarioRepositoryInterface::class,
            EloquentInventarioRepository::class
        );

        $this->app->bind(
            MovimientoInventarioRepositoryInterface::class,
            EloquentMovimientoInventarioRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::middleware('api')
        ->prefix('api')
        ->group(base_path('routes/api.php'));
    }
}
