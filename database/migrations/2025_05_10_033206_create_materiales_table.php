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
        Schema::create('materiales', function (Blueprint $table) {
            $table->id();
            $table->string('cod_articulo')->unique();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();

            // Clave foránea para categoria_id
            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('restrict');

            // Clave foránea para proveedor_id
            $table->foreignId('proveedor_id')
                  ->constrained('proveedores')
                  ->onDelete('restrict');

            $table->string('codigo_barras', 100)->nullable()->unique();

            // Clave foránea para color_id
            $table->foreignId('color_id')
                  ->constrained('colores')
                  ->onDelete('restrict');

            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiales');
    }
};
