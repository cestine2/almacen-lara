<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materiales';

    protected $fillable = [
        'cod_articulo',
        'nombre',
        'descripcion',
        'categoria_id',
        'proveedor_id',
        'codigo_barras',
        'color_id',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Obtiene la categoría a la que pertenece el material.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Obtiene el proveedor del material.
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    /**
     * Obtiene el color del material.
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}
