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
        Schema::create('suplantaciones_anfitrions', function (Blueprint $table) {
             $table->id();
            $table->unsignedBigInteger('salida_bus_id');
            $table->unsignedBigInteger('id_anfitrion_suplente');
            $table->text('motivo')->nullable();
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('salida_bus_id')->references('id_salida_bus')->on('salida_de_buses');
            $table->foreign('id_anfitrion_suplente')->references('id')->on('anfitrions');
            $table->foreign('aprobado_por')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suplantaciones_anfitrions');
    }
};
