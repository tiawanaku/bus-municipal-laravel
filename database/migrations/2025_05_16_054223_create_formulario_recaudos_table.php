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
        Schema::create('formulario_recaudo', function (Blueprint $table) {
            $table->id();

            // Relación con entrega_talonarios_anfitrion
            $table->foreignId('anfitrion_id')->constrained('entrega_talonarios_anfitrion')->onDelete('restrict');

            // Relaciones y datos asociados
            $table->string('bus_id');
            $table->string('conductor_id');
            $table->string('ruta_id');
            $table->string('horario'); // Asumido como string, puede cambiar a foreignId si tienes una tabla 'asignacions'

            // Regulares
            $table->integer('cantidad_ventas_regulares')->nullable();
            $table->integer('rango_inicial_regulares')->nullable();
            $table->integer('rango_final_regulares')->nullable();
            $table->decimal('monto_recaudado_regular', 10, 2)->nullable();
            $table->decimal('total_recaudo_regular_preferencial', 10, 2)->nullable();

            // Preferenciales
            $table->integer('cantidad_ventas_preferenciales')->nullable();
            $table->integer('rango_inicial_preferencial')->nullable();
            $table->integer('rango_final_preferencial')->nullable();
            $table->decimal('monto_recaudado_preferencial', 10, 2)->nullable();

            // Recomendados adicionales
            $table->dateTime('fecha_recaudo')->nullable(); // Para saber cuándo se realizó la operación
            $table->text('observaciones')->nullable(); // Para registrar incidencias o comentarios del cajero/anfitrión
            $table->boolean('estado')->default(true); // Para marcar registros activos/inactivos (útil para soft-delete lógico)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulario_recaudo');
    }
};
