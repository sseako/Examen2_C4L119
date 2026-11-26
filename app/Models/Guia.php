<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guia extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
    ];

    public function salidas(): BelongsTo
    {
        return $this->belongsTo(Salida::class, 'guia_salida');
    }
}
