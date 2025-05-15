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
            $table->id();

            $table->foreignId('anfitrion_id')->constrained('anfitrions')->onDelete('restrict');
            $table->foreignId('cajero_id')->nullable()->constrained('cajeros'); // Cajero secundario que entrega
            $table->date('fecha_entrega')->nullable();
            $table->string('numero_autorizacion')->nullable();

            // Preferenciales
            $table->integer('cantidad_talonarios_preferenciales')->nullable();
            $table->integer('rango_inicial_preferenciales')->nullable();
            $table->integer('rango_final_preferenciales')->nullable();

            // Regulares
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