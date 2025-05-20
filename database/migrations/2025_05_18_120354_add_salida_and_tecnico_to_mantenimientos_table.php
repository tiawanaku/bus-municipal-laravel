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
        Schema::table('mantenimientos', function (Blueprint $table) {
            //
            // Relación con salida (nullable si puede haber mantenimientos independientes)
            $table->unsignedBigInteger('salida_id')->nullable()->after('observaciones');
            $table->foreign('salida_id')->references('id_salida_bus')->on('salida_de_buses')->onDelete('set null');

            // Relación con técnico (obligatorio)

            $table->unsignedBigInteger('tecnico_id')->nullable()->after('salida_id'); 
           $table->foreign('tecnico_id')->references('id')->on('tecnicos')->onDelete('set null');

            // Soft deletes
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            //
            $table->dropForeign(['salida_id']);
            $table->dropColumn('salida_id');

            $table->dropForeign(['tecnico_id']);
            $table->dropColumn('tecnico_id');

            $table->dropSoftDeletes();
        });
    }
};
