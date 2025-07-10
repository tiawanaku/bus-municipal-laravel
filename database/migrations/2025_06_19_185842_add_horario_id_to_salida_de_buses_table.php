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
        Schema::table('salida_de_buses', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('horario_id')->nullable()->after('designacion_id');
            $table->foreign('horario_id')->references('id')->on('horarios')->onDelete('set null');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salida_de_buses', function (Blueprint $table) {
            //
            $table->dropForeign(['horario_id']);
            $table->dropColumn('horario_id');
            $table->dropSoftDeletes();
        });
    }
};
