<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemReserva extends Model
{
    use HasFactory;

    protected $table = 'item_reservas';

    protected $fillable = [
        'reserva_id',
        'salida_id',
        'cantidadPersonas',
        'precioUnitario',
    ];

    protected function casts(): array
    {
        return [
            'cantidadPersonas' => 'integer',
            'precioUnitario' => 'decimal:2',
        ];
    }

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    public function salida(): BelongsTo
    {
        return $this->belongsTo(Salida::class);
    }use HasFactory;

}
