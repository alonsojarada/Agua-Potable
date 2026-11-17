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
        Schema::create('recibos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('folio_recibo')->unique(); // Ej: REC-202611-X9K2P
            $table->decimal('monto', 10, 2);
            $table->string('mes_anio'); // Ej: "Noviembre 2026"
            $table->enum('estatus', ['pendiente', 'pagado', 'cancelado'])->default('pendiente');
            $table->string('token_seguridad', 64)->unique(); // Hash seguro para el QR
            $table->timestamp('fecha_pago')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recibos');
    }
};
