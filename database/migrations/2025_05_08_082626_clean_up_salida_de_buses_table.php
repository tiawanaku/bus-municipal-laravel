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
        //
        Schema::table('salida_de_buses', function (Blueprint $table) {
            // Cambiar nombre de id_desinacion_bus a designacion_id
            if (Schema::hasColumn('salida_de_buses', 'id_designacion_bus')) {
                $table->renameColumn('id_designacion_bus', 'designacion_id');
            }

            // Eliminar bus_id si existe
            if (Schema::hasColumn('salida_de_buses', 'bus_id')) {
                $table->dropForeign(['bus_id']);
                $table->dropColumn('bus_id');
            }

            // eliminar columnas duplicadas 
            if (Schema::hasColumn('salida_de_buses', 'anfitrion')) {
                $table->dropColumn('anfitrion');
            }

            if (Schema::hasColumn('salida_de_buses', 'conductor')) {
                $table->dropColumn('conductor');
            }

            // Agregar confirmaciones si no existen
            if (!Schema::hasColumn('salida_de_buses', 'conductor_confirmado')) {
                $table->boolean('conductor_confirmado')->default(false)->after('status_mantenimiento');
            }

            if (!Schema::hasColumn('salida_de_buses', 'anfitrion_confirmado')) {
                $table->boolean('anfitrion_confirmado')->default(false)->after('conductor_confirmado');
            }

            // Agregar FK 
            $table->foreign('designacion_id')->references('id_designacion_bus')->on('asignacion_de_bus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('salida_de_buses', function (Blueprint $table) {
            $table->dropForeign(['designacion_id']);
            $table->renameColumn('designacion_id', 'id_designacion_bus');

            $table->string('anfitrion')->nullable();
            $table->string('conductor')->nullable();

            $table->dropColumn(['conductor_confirmado', 'anfitrion_confirmado']);

            $table->unsignedBigInteger('bus_id')->nullable();
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
        });
    }
};
