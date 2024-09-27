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
            $table->foreignId('cajero_id')->constrained('cajeros'); // Relación con la tabla cajeros
            $table->string('codigo_autorizacion'); // Número de autorización
            $table->string('tipo_talonario'); // Preferencial o Regular
            $table->integer('cantidad_tickets'); // Cantidad de tickets
            $table->integer('rango_inicial'); // Rango inicial
            $table->integer('rango_final'); // Rango final
            $table->integer('numero_bloques'); // Número de bloques de talonarios
            $table->decimal('valor_ticket_bs', 8, 2); // Valor de tickets en Bs
            $table->timestamps(); // Campos created_at y updated_at
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