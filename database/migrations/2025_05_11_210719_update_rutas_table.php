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
            if (!Schema::hasColumn('rutas', 'imagen')) {
                $table->string('imagen')->nullable()->after('nombre');
            }

            if (!Schema::hasColumn('rutas', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('imagen');
            }

            if (!Schema::hasColumn('rutas', 'color')) {
                $table->string('color', 7)->nullable()->after('descripcion');
            }

            if (!Schema::hasColumn('rutas', 'video_link')) {
                $table->string('video_link')->nullable()->after('color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rutas', function (Blueprint $table) {
            // Solo elimina las columnas si existen
            if (Schema::hasColumn('rutas', 'imagen')) {
                $table->dropColumn('imagen');
            }
            if (Schema::hasColumn('rutas', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
            if (Schema::hasColumn('rutas', 'color')) {
                $table->dropColumn('color');
            }
            if (Schema::hasColumn('rutas', 'video_link')) {
                $table->dropColumn('video_link');
            }
        });
    }
};
