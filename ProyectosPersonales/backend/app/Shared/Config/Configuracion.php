<?php

declare(strict_types=1);

namespace App\Shared\Config;

use App\Shared\Audit\Auditoria;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Parámetros de negocio configurables (04 §11 sys_configuracion). Cacheados 60 s.
 * Claves: riesgo.confirmacion_manual_para_todos, riesgo.umbral, inventario.expiracion_reserva_min,
 * riesgo.plazo_revision_horas, fulfillment.max_reintentos, fulfillment.capacidad_moto_referencia, catalogo.umbral_pocas_unidades
 */
final class Configuracion
{
    public function obtener(string $clave, mixed $porDefecto = null): mixed
    {
        return Cache::remember("cfg:{$clave}", 60, function () use ($clave, $porDefecto) {
            $fila = DB::table('sys_configuracion')->where('clave', $clave)->first();

            return $fila === null ? $porDefecto : json_decode((string) $fila->valor, true);
        });
    }

    public function establecer(string $clave, mixed $valor, ?string $usuarioUid): void
    {
        $anterior = $this->obtener($clave);
        DB::table('sys_configuracion')->updateOrInsert(
            ['clave' => $clave],
            ['valor' => json_encode($valor), 'actualizado_por_uid' => $usuarioUid, 'actualizado_en' => now()],
        );
        Cache::forget("cfg:{$clave}");
        Auditoria::registrar('configuracion.cambiar', 'Configuracion', $clave, ['valor' => $anterior], ['valor' => $valor], $usuarioUid);
    }
}
