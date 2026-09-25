<?php

declare(strict_types=1);

namespace App\Shared\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class IdempotencyKeyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $contador = 0;
        Route::post('/api/v1/_prueba', function () use (&$contador) {
            return response()->json(['n' => ++$contador], 201);
        })->middleware(['api', 'idempotente']);
    }

    public function test_sin_clave_devuelve_422_problem(): void
    {
        $this->postJson('/api/v1/_prueba', ['a' => 1])
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/problem+json')
            ->assertJsonPath('codigo', 'datos_invalidos');
    }

    public function test_misma_clave_misma_respuesta(): void
    {
        $clave = '01J7Q4X8Z1Y2W3V4U5T6S7R8Q9';
        $a = $this->withHeader('Idempotency-Key', $clave)->postJson('/api/v1/_prueba', ['a' => 1])->assertStatus(201);
        $b = $this->withHeader('Idempotency-Key', $clave)->postJson('/api/v1/_prueba', ['a' => 1])->assertStatus(201)->assertHeader('Idempotent-Replayed', 'true');
        $this->assertSame($a->json('n'), $b->json('n'));
    }

    public function test_misma_clave_cuerpo_distinto_422(): void
    {
        $clave = '01J7Q4X8Z1Y2W3V4U5T6S7R8Q9';
        $this->withHeader('Idempotency-Key', $clave)->postJson('/api/v1/_prueba', ['a' => 1])->assertStatus(201);
        $this->withHeader('Idempotency-Key', $clave)->postJson('/api/v1/_prueba', ['a' => 2])->assertStatus(422);
    }
}
