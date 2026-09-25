<?php

declare(strict_types=1);

namespace App\Shared\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/** php artisan make:module Catalogo → crea la estructura del ADR-005 con su ServiceProvider. */
final class MakeModule extends Command
{
    protected $signature = 'make:module {nombre}';

    protected $description = 'Crea un módulo con la estructura estándar (ADR-005)';

    public function handle(Filesystem $fs): int
    {
        $nombre = ucfirst((string) $this->argument('nombre'));
        $base = app_path("Modules/{$nombre}");
        if ($fs->exists($base)) {
            $this->error("El módulo {$nombre} ya existe.");

            return self::FAILURE;
        }

        foreach (['Domain', 'Application', 'Infrastructure', 'Http', 'Contracts', 'Events', 'Database/Migrations', 'Database/Seeders', 'Tests/Unit', 'Tests/Feature'] as $dir) {
            $fs->makeDirectory("{$base}/{$dir}", 0755, true);
            $fs->put("{$base}/{$dir}/.gitkeep", '');
        }

        $fs->put("{$base}/Http/routes.php", <<<PHP
<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->middleware(['api'])->group(function (): void {
    // Route::get('/...', [Controlador::class, 'metodo']);
});

PHP);

        $fs->put("{$base}/{$nombre}ServiceProvider.php", <<<PHP
<?php

declare(strict_types=1);

namespace App\Modules\\{$nombre};

use App\Shared\Events\RegistroConsumidores;
use Illuminate\Support\ServiceProvider;

final class {$nombre}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // \$this->app->bind(Contracts\\{$nombre}Api::class, Infrastructure\\{$nombre}ApiEloquent::class);
    }

    public function boot(RegistroConsumidores \$registro): void
    {
        \$this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        \$this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        // \$registro->registrar('PedidoCreado', 1, Application\AlgunConsumidor::class);
    }
}

PHP);

        $this->info("Módulo {$nombre} creado. Regístralo en bootstrap/providers.php y añade su capa a deptrac.yaml.");

        return self::SUCCESS;
    }
}
