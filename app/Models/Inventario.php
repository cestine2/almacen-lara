<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Inventario extends Model
{
    use HasFactory;

    // Indica a Eloquent que la tabla se llama 'inventarios'
    protected $table = 'inventarios';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'tipo',
        'material_id',
        'producto_id',
        'stock_actual',
        'sucursal_id',
        'usuario_id',
    ];

    protected $casts = [
        'stock_actual' => 'integer',
        'tipo' => 'string',
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
     * Obtiene la sucursal asociada a este registro de inventario.
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Obtiene el usuario que registró/modificó este registro de inventario.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
