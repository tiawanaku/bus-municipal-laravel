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
        Schema::table('rutas', function (Blueprint $table) {
            //
             $table->string('imagen')->nullable()->after('nombre');
        $table->text('descripcion')->nullable()->after('imagen');
        $table->string('color', 7)->nullable()->after('descripcion');
        $table->string('video_link')->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rutas', function (Blueprint $table) {
            //
            $table->dropColumn(['imagen', 'descripcion', 'color', 'video_link']);
        });
    }
};
