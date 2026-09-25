<?php

declare(strict_types=1);

namespace App\Shared\Tests\Feature;

use App\Shared\Events\EventoDeDominio;
use App\Shared\Events\EventoPublicado;
use App\Shared\Events\PublicadorEventos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use LogicException;
use Tests\TestCase;

final class OutboxTest extends TestCase
{
    use RefreshDatabase;

    private function evento(): EventoDeDominio
    {
        return new class('01J7Q4X8Z1Y2W3V4U5T6S7R8Q9') extends EventoDeDominio
        {
            public static function nombre(): string { return 'PruebaOcurrida'; }
            public static function version(): int { return 1; }
            public static function agregadoTipo(): string { return 'Prueba'; }
            public function payload(): array { return ['hola' => 'mundo']; }
        };
    }

    public function test_publicar_fuera_de_transaccion_falla(): void
    {
        $this->expectException(LogicException::class);
        app(PublicadorEventos::class)->publicar($this->evento());
    }

    public function test_el_evento_queda_en_outbox_y_se_despacha_a_la_cola(): void
    {
        Bus::fake();
        DB::transaction(fn () => app(PublicadorEventos::class)->publicar($this->evento()));

        $this->assertDatabaseCount('sys_outbox', 1);
        $this->assertDatabaseHas('sys_outbox', ['nombre' => 'PruebaOcurrida', 'despachado_en' => null]);

        $this->artisan('outbox:despachar')->assertSuccessful();

        Bus::assertDispatched(EventoPublicado::class, fn ($job) => $job->evento['payload']['hola'] === 'mundo');
        $this->assertDatabaseMissing('sys_outbox', ['despachado_en' => null]);
    }

    public function test_rollback_no_deja_eventos(): void
    {
        try {
            DB::transaction(function (): void {
                app(PublicadorEventos::class)->publicar($this->evento());
                throw new \RuntimeException('falla de negocio');
            });
        } catch (\RuntimeException) {
        }
        $this->assertDatabaseCount('sys_outbox', 0);
    }
}
