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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cajero_id')->index('inventario_talonarios_cajero_id_foreign');
            $table->string('codigo_autorizacion');
            $table->string('tipo_talonario');
            $table->integer('cantidad_tickets');
            $table->integer('rango_inicial');
            $table->integer('rango_final');
            $table->integer('numero_bloques');
            $table->decimal('valor_ticket_bs');
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
