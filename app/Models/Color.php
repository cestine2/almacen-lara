<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    use HasFactory;

    protected $table = 'colores';

    protected $fillable = [
        'nombre',
        'estado',
    ];


    protected $casts = [
        'estado' => 'boolean',
    ];

    public function materiales(): HasMany {
        return $this->hasMany(Material::class, 'color_id');
    }

    public function productos(): HasMany {
        return $this->hasMany(Product::class, 'color_id');
    }
}
