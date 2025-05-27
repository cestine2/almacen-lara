<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'talla',
        'color_id',
        'precio',
        'codigo_barras',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'precio' => 'decimal:2',
    ];

    /**
     * Obtiene la categoría a la que pertenece el producto.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Obtiene el color del producto.
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}
