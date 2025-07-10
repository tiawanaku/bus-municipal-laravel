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
        Schema::table('salida_de_buses', function (Blueprint $table) {
            //
             // Fecha y hora de llegada
            $table->date('fecha_llegada')->nullable()->after('motivo_no_salida');
            $table->time('hora_llegada')->nullable()->after('fecha_llegada');
            

            // Relación con rutas
            $table->foreignId('ruta_id')
                  ->nullable()
                  ->constrained('rutas')
                  ->after('id_salida_bus')  // Ajusta según el orden que desees
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salida_de_buses', function (Blueprint $table) {
            //
            $table->dropForeign(['ruta_id']);
            $table->dropColumn(['fecha_llegada', 'hora_llegada', 'ruta_id']);
        });
    }
};
