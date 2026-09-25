<?php

declare(strict_types=1);

namespace App\Modules\Identidad;

use App\Modules\Identidad\Contracts\IdentidadApi;
use App\Modules\Identidad\Infrastructure\IdentidadApiEloquent;
use App\Modules\Identidad\Infrastructure\Usuario;
use Illuminate\Support\ServiceProvider;

final class IdentidadServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IdentidadApi::class, IdentidadApiEloquent::class);

        // Guard y provider `personal` separados de los clientes de la tienda (02 §4.9, ADR-002).
        config([
            'auth.guards.personal' => ['driver' => 'sanctum', 'provider' => 'personal'],
            'auth.providers.personal' => ['driver' => 'eloquent', 'model' => Usuario::class],
        ]);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
    }
}
