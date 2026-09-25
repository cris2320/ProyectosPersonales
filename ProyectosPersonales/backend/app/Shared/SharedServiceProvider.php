<?php

declare(strict_types=1);

namespace App\Shared;

use App\Shared\Console\DespacharOutbox;
use App\Shared\Console\MakeModule;
use App\Shared\Events\RegistroConsumidores;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

final class SharedServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RegistroConsumidores::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->commands([DespacharOutbox::class, MakeModule::class]);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            // Respaldo del worker permanente y limpieza operativa (04 §14).
            $schedule->command('outbox:despachar')->everyMinute()->withoutOverlapping();
            $schedule->call(fn () => DB::table('sys_outbox')->whereNotNull('despachado_en')->where('despachado_en', '<', now()->subDays(30))->delete())->daily();
            $schedule->call(fn () => DB::table('sys_idempotencia')->where('expira_en', '<', now())->delete())->hourly();
        });
    }
}
