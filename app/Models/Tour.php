<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'precioPorPersona',
        'duracionHoras',
    ];

    protected function casts(): array
    {
        return [
            'precioPorPersona' => 'decimal:2',
            'duracionHoras' => 'integer',
        ];
    }

    public function salidas(): HasMany
    {
        return $this->hasMany(Salida::class);
    }
}
