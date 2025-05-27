<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['Material', 'Producto']);
            $table->foreignId('material_id')
                  ->nullable()
                  ->constrained('materiales')
                  ->onDelete('restrict');
            $table->foreignId('producto_id')
                  ->nullable()
                  ->constrained('productos')
                  ->onDelete('restrict');
            $table->integer('stock_actual');
            $table->foreignId('sucursal_id')
                  ->nullable()
                  ->constrained('sucursales')
                  ->onDelete('restrict');
            $table->foreignId('usuario_id')
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->timestamps();
            $table->unique(['tipo', 'material_id', 'producto_id', 'sucursal_id'], 'unique_item_sucursal');

            // Índices adicionales si son necesarios para rendimiento
            // $table->index('tipo');
            // $table->index('material_id');
            // $table->index('producto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
