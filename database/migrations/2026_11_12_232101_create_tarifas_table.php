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
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Doméstico, Comercial, Rural
            $table->decimal('monto_mensual', 10, 2); // Monto fijo a pagar cada mes (ej: 150.00)
            $table->text('descripcion')->nullable(); // Detalles opcionales de la tarifa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifas');
    }
};