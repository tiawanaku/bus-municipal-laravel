<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalidaDeBusesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salida_de_buses', function (Blueprint $table) {
            $table->id('id_salida_bus'); // ID de la salida de bus
            $table->unsignedBigInteger('id_designacion_bus'); // ID de la designación de bus
            $table->string('n_bus'); // Número del bus
            $table->string('anfitrion'); // Número del bus
            $table->string('conductor'); // Número del bus
            $table->date('fecha_salida'); // Fecha de salida
            $table->time('hora_salida'); // Hora de salida
            $table->string('estado_salida'); // Estado de la salida
            $table->text('motivo_no_salida')->nullable(); // Motivo de no salida
            $table->timestamps(); // Timestamps para created_at y updated_at

            // Definir la clave foránea de id_designacion_bus con la tabla asignacion_de_bus
            $table->foreign('id_designacion_bus')->references('id_designacion_bus')->on('asignacion_de_bus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salida_de_buses');
    }
}
