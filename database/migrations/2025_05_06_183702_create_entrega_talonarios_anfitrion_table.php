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
        Schema::create('entrega_talonarios_anfitrion', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relaciones opcionales
            $table->bigInteger('anfitrion_id')->unsigned(); // Definir como unsigned porque la clave primaria en la tabla anfitrions es unsigned
            $table->foreignId('cajero_id')->nullable()->constrained('cajeros');

            // Datos de entrega
            $table->string('numero_autorizacion')->nullable();

            // Talonarios Preferenciales
            $table->integer('cantidad_talonarios_preferenciales')->nullable();
            $table->integer('rango_inicial_preferenciales')->nullable();
            $table->integer('rango_final_preferenciales')->nullable();

            // Talonarios Regulares
            $table->integer('cantidad_talonarios_regulares')->nullable();
            $table->integer('rango_inicial_regulares')->nullable();
            $table->integer('rango_final_regulares')->nullable();

            // Totales
            $table->integer('total_tickets_regulares')->nullable();
            $table->integer('total_tickets_preferenciales')->nullable();
            $table->decimal('total_recaudar_regulares', 10, 2)->nullable();
            $table->decimal('total_recaudar_preferenciales', 10, 2)->nullable();
            $table->decimal('total_recaudar', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrega_talonarios_anfitrion');
    }
};