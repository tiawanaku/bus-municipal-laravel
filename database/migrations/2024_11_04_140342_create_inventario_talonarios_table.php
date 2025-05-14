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
        Schema::create('inventario_talonarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cajero_id')->constrained('cajeros')->onDelete('cascade'); // Cajero principal

            // Preferenciales
            $table->integer('cantidad_preferenciales')->nullable();
            $table->integer('rango_inicial_preferencial')->nullable();
            $table->integer('rango_final_preferencial')->nullable();
            $table->integer('cantidad_restante_preferencial')->nullable();
            $table->integer('total_boletos_preferenciales')->nullable();
            $table->decimal('total_aproximado_bolivianos', 10, 2)->nullable();

            // Regulares
            $table->integer('cantidad_regulares')->nullable();
            $table->integer('rango_inicial_regular')->nullable();
            $table->integer('rango_final_regular')->nullable();
            $table->integer('cantidad_restante_regular')->nullable();
            $table->integer('total_boletos_regulares')->nullable();
            $table->decimal('total_aproximado_bolivianos_regular', 10, 2)->nullable();

            // Info adicional
            $table->integer('estado_preferencial')->default(0);
            $table->integer('estado_regular')->default(0);
            $table->string('tipo_talonarios')->nullable(); // puede ser mixto, preferencial, regular
            $table->date('fecha_entrega')->nullable();
            $table->string('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_talonarios');
    }
};