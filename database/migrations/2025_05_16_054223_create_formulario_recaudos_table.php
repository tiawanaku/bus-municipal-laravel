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
            // Relaciones y datos asociados
            $table->string('bus_id');
            $table->string('conductor_id');
            $table->string('rutas');
            $table->string('horario');
            $table->string('N_ficha'); // Asumido como string, puede cambiar a foreignId si tienes una tabla 'asignacions'

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


            // Recomendados adicionales

           $table->foreign('anfitrion_id')->references('id')->on('anfitrions');
           $table->foreign('conductor_id')->references('id')->on('conductors');
           $table->foreign('bus_id')->references('id')->on('buses');


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
