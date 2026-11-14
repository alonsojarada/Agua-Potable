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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('tarifa_id')->nullable()->constrained('tarifas')->nullOnDelete(); // Opcional: Para saber con cuál tarifa se generó
            $table->tinyInteger('periodo_mes'); // 1 al 12
            $table->smallInteger('periodo_anio'); // Ej: 2026
            $table->decimal('monto', 10, 2);
            $table->dateTime('fecha_pago')->nullable();
            $table->enum('estatus', ['pendiente', 'pagado', 'cancelado'])->default('pendiente');
            $table->timestamps();

            // Restricción única vital: Evita que se genere dos veces el mismo mes/año para el mismo usuario
            $table->unique(['usuario_id', 'periodo_anio', 'periodo_mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};