<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salida extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'fecha',
        'cuposDisponibles',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'cuposDisponibles' => 'integer',
        ];
    }

    public function tour(): BelongsTo
{
    return $this->belongsTo(Tour::class);
}

public function guia(): BelongsTo
{
    return $this->belongsTo(Guia::class, 'guia_id');
}

    public function items(): HasMany
    {
        return $this->hasMany(ItemReserva::class);
    }
}
