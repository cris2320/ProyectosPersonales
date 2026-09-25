<?php

declare(strict_types=1);

namespace App\Shared\Events;

/**
 * Mapa nombre+versión → consumidores. Cada módulo registra los suyos en su ServiceProvider:
 *   $registro->registrar('PedidoCreado', 1, EvaluarRiesgoDePedido::class);
 */
final class RegistroConsumidores
{
    /** @var array<string, list<class-string<Consumidor>>> */
    private array $mapa = [];

    /** @param class-string<Consumidor> $consumidor */
    public function registrar(string $nombre, int $version, string $consumidor): void
    {
        $this->mapa["{$nombre}.v{$version}"][] = $consumidor;
    }

    /** @return list<class-string<Consumidor>> */
    public function consumidoresDe(string $nombre, int $version): array
    {
        return $this->mapa["{$nombre}.v{$version}"] ?? [];
    }
}
