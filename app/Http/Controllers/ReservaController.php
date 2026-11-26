<?php

namespace App\Http\Controllers;

use App\Services\ServicioReservas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function __construct(
        private readonly ServicioReservas $servicioReservas
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.salida_id' => ['required', 'integer', 'exists:salidas,id'],
            'items.*.cantidad_personas' => ['required', 'integer', 'min:1'],
        ]);

        $reserva = $this->servicioReservas->reservar(
            (int) $validated['cliente_id'],
            $validated['items']
        );

        return response()->json([
            'message' => 'Reserva registrada correctamente.',
            'data' => $reserva->load('items.salida.tour', 'cliente'),
        ], 201);
    }
}