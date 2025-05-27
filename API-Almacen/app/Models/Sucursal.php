<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Obtiene los usuarios que pertenecen a esta sucursal.
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'sucursal_id');
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }
}
