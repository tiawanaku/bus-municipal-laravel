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
            $table->foreignId('cajero_id')->constrained('cajeros')->onDelete('cascade'); // Relación con tabla cajeros
            $table->integer('numero_paquetes_entregados');
            $table->integer('cantidad_talonarios');
            $table->integer('cantidad_tickets');
            $table->date('fecha_entrega');
            $table->string('tipo_talonarios');
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
