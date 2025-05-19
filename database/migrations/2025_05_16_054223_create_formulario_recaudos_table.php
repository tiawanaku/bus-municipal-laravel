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

            // Llaves foráneas correctamente definidas
            $table->foreignId('anfitrion_id')->constrained('anfitrions');
            $table->foreignId('conductor_id')->constrained('conductors');
            $table->foreignId('bus_id')->constrained('buses');

            // Otros campos
            $table->string('rutas');
            $table->string('horario');
            $table->string('N_ficha'); // Podrías cambiarlo a foreignId si hay relación

            // Regulares
            $table->integer('cantidad_ventas_regulares')->nullable();
            $table->integer('rango_inicial_regulares')->nullable();
            $table->integer('rango_final_regulares')->nullable();
            $table->decimal('monto_recaudado_regular', 10, 2)->nullable();

            // Preferenciales
            $table->integer('cantidad_ventas_preferenciales')->nullable();
            $table->integer('rango_inicial_preferencial')->nullable();
            $table->integer('rango_final_preferencial')->nullable();
            $table->decimal('monto_recaudado_preferencial', 10, 2)->nullable();

            $table->decimal('total_recaudo_regular_preferencial', 10, 2)->nullable();

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
