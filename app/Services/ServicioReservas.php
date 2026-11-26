<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\ItemReserva;
use App\Models\Reserva;
use App\Models\Salida;
use DomainException;
use Illuminate\Support\Facades\DB;

class ServicioReservas
{
    /**
     * @param  array<int, array{salida_id: int, cantidad_personas?: int, cantidadPersonas?: int}>  $items
     */
    public function reservar(int $clienteId, array $items): Reserva
    {
        return DB::transaction(function () use ($clienteId, $items): Reserva {
            $salidasBloqueadas = [];
            $lineasPreparadas = [];
            $total = 0.0;

            foreach ($items as $item) {
                $salidaId = (int) $item['salida_id'];
                $cantidad = (int) ($item['cantidad_personas'] ?? $item['cantidadPersonas'] ?? 0);

                if ($cantidad <= 0) {
                    throw new DomainException('La cantidad de personas debe ser mayor que cero.');
                }

                $salida = Salida::query()
                    ->whereKey($salidaId)
                    ->lockForUpdate()
                    ->with('tour')
                    ->firstOrFail();

                if ($salida->cuposDisponibles < $cantidad) {
                    throw new DomainException(
                        "No hay cupos suficientes para la salida #{$salida->id}."
                    );
                }

                $precioUnitario = (float) $salida->tour->precioPorPersona;
                $subtotal = $precioUnitario * $cantidad;
                $total += $subtotal;

                $salidasBloqueadas[] = [
                    'salida' => $salida,
                    'cantidad' => $cantidad,
                ];

                $lineasPreparadas[] = [
                    'salida_id' => $salida->id,
                    'cantidadPersonas' => $cantidad,
                    'precioUnitario' => $precioUnitario,
                ];
            }

            foreach ($salidasBloqueadas as $entrada) {
                /** @var Salida $salida */
                $salida = $entrada['salida'];
                $salida->cuposDisponibles = $salida->cuposDisponibles - $entrada['cantidad'];
                $salida->save();
            }

            $reserva = Reserva::create([
                'cliente_id' => $clienteId,
                'fecha' => now(),
                'total' => $total,
            ]);

            foreach ($lineasPreparadas as $linea) {
                ItemReserva::create([
                    'reserva_id' => $reserva->id,
                    'salida_id' => $linea['salida_id'],
                    'cantidadPersonas' => $linea['cantidadPersonas'],
                    'precioUnitario' => $linea['precioUnitario'],
                ]);
            }

            return $reserva;
        });
    }
}