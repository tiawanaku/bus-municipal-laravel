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
        Schema::table('paradas', function (Blueprint $table) {
            $table->foreign(['id_ruta'], 'paradas_ibfk_1')->references(['id'])->on('rutas')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paradas', function (Blueprint $table) {
            $table->dropForeign('paradas_ibfk_1');
        });
    }
};
