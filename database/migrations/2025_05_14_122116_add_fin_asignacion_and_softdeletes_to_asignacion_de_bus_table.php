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
        Schema::table('asignacion_de_bus', function (Blueprint $table) {
            //
            $table->dateTime('fin_asignacion')->nullable()->after('fecha_designacion'); 
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignacion_de_bus', function (Blueprint $table) {
            //
             $table->dropColumn(['fin_asignacion', 'deleted_at']);
        });
    }
};
