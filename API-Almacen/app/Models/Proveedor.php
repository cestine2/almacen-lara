<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    // Si Proveedor tuviera relaciones (ej: con Materiales), las definirías aquí.
    // public function materiales(): HasMany { ... }
}
