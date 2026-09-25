<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Tablas compartidas (04 §11). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sys_outbox', function (Blueprint $t): void {
            $t->id();
            $t->char('event_uid', 26)->unique();
            $t->string('nombre', 60);
            $t->unsignedTinyInteger('version');
            $t->string('agregado_tipo', 40);
            $t->char('agregado_uid', 26);
            $t->json('payload');
            $t->dateTime('ocurrido_en', 3);
            $t->dateTime('despachado_en', 3)->nullable();
            $t->unsignedTinyInteger('intentos')->default(0);
            $t->string('error', 255)->nullable();
            $t->index(['despachado_en', 'id']);
        });

        Schema::create('sys_eventos_procesados', function (Blueprint $t): void {
            $t->string('consumidor', 120);
            $t->char('event_uid', 26);
            $t->dateTime('procesado_en', 3);
            $t->primary(['consumidor', 'event_uid']);
        });

        Schema::create('sys_idempotencia', function (Blueprint $t): void {
            $t->string('clave', 80);
            $t->string('actor_uid', 80);
            $t->string('ruta', 120);
            $t->char('cuerpo_hash', 64);
            $t->unsignedSmallInteger('status_respuesta');
            $t->json('respuesta');
            $t->dateTime('expira_en', 3);
            $t->dateTime('creado_en', 3);
            $t->primary(['clave', 'actor_uid']);
            $t->index('expira_en');
        });

        Schema::create('sys_auditoria', function (Blueprint $t): void {
            $t->id();
            $t->string('actor_tipo', 20);
            $t->char('actor_uid', 26)->nullable();
            $t->string('accion', 60);
            $t->string('entidad_tipo', 40);
            $t->string('entidad_uid', 80);
            $t->json('antes')->nullable();
            $t->json('despues')->nullable();
            $t->char('ip_hash', 64)->nullable();
            $t->char('trace_id', 32)->nullable();
            $t->dateTime('creado_en', 3);
            $t->index(['entidad_tipo', 'entidad_uid']);
            $t->index(['actor_uid', 'creado_en']);
        });

        Schema::create('sys_configuracion', function (Blueprint $t): void {
            $t->string('clave', 80)->primary();
            $t->json('valor');
            $t->string('descripcion', 255)->nullable();
            $t->char('actualizado_por_uid', 26)->nullable();
            $t->dateTime('actualizado_en', 3)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_configuracion');
        Schema::dropIfExists('sys_auditoria');
        Schema::dropIfExists('sys_idempotencia');
        Schema::dropIfExists('sys_eventos_procesados');
        Schema::dropIfExists('sys_outbox');
    }
};
