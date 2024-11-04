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
        Schema::create('asignacion_de_bus', function (Blueprint $table) {
            $table->id('id_designacion_bus');
            $table->foreignId('id_conductor')->constrained('conductors')->onDelete('cascade');
            $table->foreignId('id_buses')->constrained('buses')->onDelete('cascade');
            $table->foreignId('id_anfitrion')->constrained('anfitrions')->onDelete('cascade'); // Agrega la relación con anfitriones
            $table->string('n_ficha');
            $table->text('observaciones')->nullable();
            $table->date('fecha_designacion');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_de_bus');
    }
};
