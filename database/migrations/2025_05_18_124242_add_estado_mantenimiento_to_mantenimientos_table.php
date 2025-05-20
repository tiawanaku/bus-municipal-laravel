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
              $table->enum('estado_mantenimiento', ['pendiente', 'en_proceso', 'realizado'])
                  ->default('pendiente')
                  ->after('tipo_mantenimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            //
            $table->dropColumn('estado_mantenimiento');
        });
    }
};
