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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->enum('motivo', ['entrada', 'salida', 'ajuste']);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['Material', 'Producto']);
            $table->foreignId('material_id')
                  ->nullable()
                  ->constrained('materiales')
                  ->onDelete('restrict');
            $table->foreignId('producto_id')
                  ->nullable()
                  ->constrained('productos')
                  ->onDelete('restrict');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 15, 2)->nullable();;
            $table->decimal('total', 15, 2)->nullable();;
            $table->foreignId('sucursal_id')
                  ->constrained('sucursales')
                  ->onDelete('restrict');
            $table->foreignId('usuario_id')
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->timestamp('created_at')->useCurrent();
            $table->index('created_at');
            $table->index('sucursal_id');
            $table->index(['tipo', 'material_id', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
