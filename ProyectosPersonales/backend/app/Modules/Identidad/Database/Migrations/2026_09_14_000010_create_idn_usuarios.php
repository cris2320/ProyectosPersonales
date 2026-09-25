<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idn_usuarios', function (Blueprint $t): void {
            $t->id();
            $t->char('uid', 26)->unique();
            $t->string('nombre', 150);
            $t->string('usuario', 40)->unique();
            $t->string('correo', 190)->nullable();
            $t->string('telefono', 20)->nullable();
            $t->string('password_hash', 255);
            $t->string('rol', 20);
            $t->binary('mfa_secreto_cifrado')->nullable();
            $t->boolean('activo')->default(true);
            $t->dateTime('ultimo_acceso_en', 3)->nullable();
            $t->dateTime('creado_en', 3)->nullable();
            $t->dateTime('actualizado_en', 3)->nullable();
            $t->index(['rol', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idn_usuarios');
    }
};
