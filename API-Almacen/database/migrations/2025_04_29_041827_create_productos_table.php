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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();

            // Clave foránea para categoria_id
            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('restrict');

            $table->string('talla', 10)->nullable();

            // Clave foránea para color_id
            $table->foreignId('color_id')
                  ->constrained('colores')
                  ->onDelete('restrict');

            $table->decimal('precio', 15, 2);

            $table->string('codigo_barras', 100)->nullable()->unique();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
