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
        Schema::table('mantenimientos', function (Blueprint $table) {
            //
            $table->dateTime('fecha_recibido')->nullable()->after('fecha_mantenimiento');
            $table->dateTime('fecha_fin')->nullable()->after('fecha_recibido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            //
            $table->dropColumn('fecha_recibido');
            $table->dropColumn('fecha_fin');
        });
    }
};
