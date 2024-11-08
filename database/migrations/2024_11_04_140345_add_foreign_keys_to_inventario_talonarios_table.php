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
        Schema::table('inventario_talonarios', function (Blueprint $table) {
            $table->foreign(['cajero_id'])->references(['id'])->on('cajeros')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventario_talonarios', function (Blueprint $table) {
            $table->dropForeign('inventario_talonarios_cajero_id_foreign');
        });
    }
};
