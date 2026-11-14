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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('red_id')->constrained('redes'); // Red a la que pertenece la toma
            $table->foreignId('tarifa_id')->nullable()->constrained('tarifas')->nullOnDelete(); // Tarifa mensual asignada
            $table->string('folio')->unique(); // Ej: WR-001
            $table->string('nombre_completo');
            $table->string('telefono')->nullable();
            $table->string('direccion');
            $table->decimal('latitud', 10, 7);   // Ubicación exacta en el mapa
            $table->decimal('longitud', 10, 7);  // Ubicación exacta en el mapa
            $table->enum('estado_servicio', ['activo', 'suspendido', 'cortado'])->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};