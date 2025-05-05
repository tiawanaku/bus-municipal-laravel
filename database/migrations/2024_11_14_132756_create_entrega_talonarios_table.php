<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entrega_talonarios', function (Blueprint $table) {
            $table->id();
            $table->string('responsable_entrega');

            // Relación opcional sin cascada
            $table->foreignId('cajero_id')->nullable()->constrained('cajeros');
            $table->foreignId('users_id')->nullable()->constrained('users');

            $table->string('cantidad_preferenciales')->nullable();
            $table->integer('rango_inicial_preferencial')->nullable();
            $table->integer('rango_final_preferencial')->nullable();
            $table->integer('cantidad_restante_preferencial')->nullable();

            $table->string('cantidad_regulares')->nullable();
            $table->integer('rango_inicial_regular')->nullable();
            $table->integer('rango_final_regular')->nullable();
            $table->integer('cantidad_restante_regular')->nullable();

            $table->date('fecha_entrega');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrega_talonarios');
    }
};