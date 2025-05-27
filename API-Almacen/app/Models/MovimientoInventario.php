<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Importa BelongsTo
// use Illuminate\Database\Eloquent\Relations\MorphTo; // <-- Opcional: si decides usar relaciones polimórficas

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    public $timestamps = false;

    protected $fillable = [
        'motivo',
        'descripcion',
        'tipo',
        'material_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'total',
        'sucursal_id',
        'usuario_id'
    ];

    // Atributos que no deberían ser asignables masivamente
    protected $guarded = [
        'id',
        'created_at',
    ];


    // Define los tipos de datos
    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'total' => 'decimal:2',
        'motivo' => 'string',
        'tipo' => 'string',
        'created_at' => 'datetime',
    ];

    /**
     * Obtiene el material asociado si el tipo es 'Material'.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    /**
     * Obtiene el producto asociado si el tipo es 'Producto'.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }

    /**
     * Obtiene la sucursal asociada a este movimiento.
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Obtiene el usuario que registró este movimiento.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
