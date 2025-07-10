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
        //
          Schema::table('asignacion_de_bus', function (Blueprint $table) {
        // Eliminar columna fin_asignacion (datetime)
        $table->dropColumn('fin_asignacion');

        // Agregar fin_designacion como date, después de la columna que quieras (ej: inicio_asignacion)
        $table->date('fin_designacion')->after('fecha_designacion');

        // Agregar tipo_asignacion string nullable, después de fin_designacion
        $table->string('tipo_asignacion')->nullable()->after('fin_designacion');

        // Eliminar hora_salida
        $table->dropColumn('hora_salida');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('asignacion_de_bus', function (Blueprint $table) {
        // Volver a agregar fin_asignacion como datetime
        $table->dateTime('fin_asignacion')->after('fecha_designacion');

        // Eliminar fin_designacion
        $table->dropColumn('fin_designacion');

        // Eliminar tipo_asignacion
        $table->dropColumn('tipo_asignacion');

        // Volver a agregar hora_salida
        $table->time('hora_salida')->nullable();
    });
    }
};
