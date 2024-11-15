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
        Schema::create('avisos', function (Blueprint $table) {
            $table->id();
            $table->string('noticia'); 
            $table->timestamp('inicio_periodo')->nullable(); 
            $table->timestamp('fin_periodo')->nullable(); 
            $table->string('razon');
            $table->text('paradas_afectadas'); 
            $table->text('detalle'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avisos');
    }
};
