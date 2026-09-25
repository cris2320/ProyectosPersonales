<?php

declare(strict_types=1);

namespace App\Shared\Events;

use Illuminate\Database\Eloquent\Model;

/** Tabla sys_outbox (04 §11). */
final class Outbox extends Model
{
    protected $table = 'sys_outbox';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'ocurrido_en' => 'datetime',
        'despachado_en' => 'datetime',
    ];
}
