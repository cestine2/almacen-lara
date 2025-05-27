<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'tipo' => 'string',
    ];

    public function productos(): HasMany {
        return $this->hasMany(Product::class, 'categoria_id');
    }

    public function materiales(): HasMany {
        return $this->hasMany(Material::class, 'categoria_id');
    }
}
