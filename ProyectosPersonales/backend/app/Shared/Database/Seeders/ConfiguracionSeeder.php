<?php

declare(strict_types=1);

namespace App\Shared\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/** Valores iniciales confirmados por el dueño (02 §9, 04 §11). */
final class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $valores = [
            ['riesgo.confirmacion_manual_para_todos', true, 'Si está activo, todos los pedidos pasan a revisión manual'],
            ['riesgo.umbral', 3, 'Puntaje máximo para confirmar automáticamente'],
            ['riesgo.plazo_revision_horas', 2, 'Horas de horario operativo antes de cancelar un pedido en revisión'],
            ['inventario.expiracion_reserva_min', 30, 'Minutos de reserva para clientes nuevos sin correo verificado'],
            ['fulfillment.max_reintentos', 1, 'Reintentos de entrega antes de devolver'],
            ['fulfillment.capacidad_moto_referencia', 30, 'Pedidos por moto; solo aviso'],
            ['catalogo.umbral_pocas_unidades', 5, 'Stock ≤ este valor muestra "Últimas unidades"'],
            ['operacion.hora_inicio', '07:00', 'Inicio del horario de operación'],
            ['operacion.hora_fin', '21:00', 'Fin del horario de operación'],
        ];
        foreach ($valores as [$clave, $valor, $desc]) {
            DB::table('sys_configuracion')->updateOrInsert(['clave' => $clave], ['valor' => json_encode($valor), 'descripcion' => $desc]);
        }
    }
}
