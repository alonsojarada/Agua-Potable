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
        Schema::create('redes_tramos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('red_id')->constrained('redes')->onDelete('cascade');
            $table->string('nombre_tramo'); // Ej: Tramo Calle Hidalgo
            $table->json('coordenadas_linea'); // Coordenadas [lat, lng] para Leaflet
            $table->decimal('diametro_pulgadas', 4, 2)->default(2.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redes_tramos');
    }
};
