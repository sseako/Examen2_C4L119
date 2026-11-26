<?php

use App\Models\Cliente;
use App\Models\ItemReserva;
use App\Models\Reserva;
use App\Models\Salida;
use App\Models\Tour;

test('registra una reserva valida con dos salidas y descuenta los cupos', function () {
    $cliente = Cliente::create([
        'nombre' => 'Ana Pérez',
        'correo' => 'ana@example.com',
    ]);

    $tourA = Tour::create([
        'nombre' => 'Caminata Volcán',
        'precioPorPersona' => 45.00,
        'duracionHoras' => 4,
    ]);

    $tourB = Tour::create([
        'nombre' => 'Aguas Termales',
        'precioPorPersona' => 60.00,
        'duracionHoras' => 3,
    ]);

    $salida1 = Salida::create([
        'tour_id' => $tourA->id,
        'fecha' => now()->addDay(),
        'cuposDisponibles' => 10,
    ]);

    $salida2 = Salida::create([
        'tour_id' => $tourB->id,
        'fecha' => now()->addDays(2),
        'cuposDisponibles' => 8,
    ]);

    $response = $this->postJson('/reservas', [
        'cliente_id' => $cliente->id,
        'items' => [
            ['salida_id' => $salida1->id, 'cantidad_personas' => 2],
            ['salida_id' => $salida2->id, 'cantidad_personas' => 3],
        ],
    ]);

    $response->assertCreated();

    expect(Reserva::count())->toBe(1)
        ->and(ItemReserva::count())->toBe(2);

    $reserva = Reserva::first();
    expect((float) $reserva->total)->toBe(270.0)
        ->and((int) $reserva->cliente_id)->toBe($cliente->id);

    expect((int) $salida1->fresh()->cuposDisponibles)->toBe(8)
        ->and((int) $salida2->fresh()->cuposDisponibles)->toBe(5);
});

test('no registra nada ni modifica cupos de la primera salida si la segunda no tiene cupos', function () {
    $cliente = Cliente::create([
        'nombre' => 'Luis Mora',
        'correo' => 'luis@example.com',
    ]);

    $tourA = Tour::create([
        'nombre' => 'Canopy Bosque',
        'precioPorPersona' => 50.00,
        'duracionHoras' => 2,
    ]);

    $tourB = Tour::create([
        'nombre' => 'Cabalgata',
        'precioPorPersona' => 40.00,
        'duracionHoras' => 3,
    ]);

    $salida1 = Salida::create([
        'tour_id' => $tourA->id,
        'fecha' => now()->addDay(),
        'cuposDisponibles' => 10,
    ]);

    $salida2 = Salida::create([
        'tour_id' => $tourB->id,
        'fecha' => now()->addDays(2),
        'cuposDisponibles' => 1,
    ]);

    $response = $this->postJson('/reservas', [
        'cliente_id' => $cliente->id,
        'items' => [
            ['salida_id' => $salida1->id, 'cantidad_personas' => 3],
            ['salida_id' => $salida2->id, 'cantidad_personas' => 4],
        ],
    ]);

    expect($response->status())->toBeIn([409, 422, 500]);

    expect(Reserva::count())->toBe(0)
        ->and(ItemReserva::count())->toBe(0)
        ->and((int) $salida1->fresh()->cuposDisponibles)->toBe(10)
        ->and((int) $salida2->fresh()->cuposDisponibles)->toBe(1);
});
