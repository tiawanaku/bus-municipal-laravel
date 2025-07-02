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
        Schema::create('control_de_reguladors', function (Blueprint $table) {
             $table->id();
            $table->integer('molinete_inicial');
            $table->string('foto_respaldo_inicial');
            $table->integer('molinete_final');
            $table->string('foto_respaldo_final');
            $table->string('total_giros');
            $table->timestamp('fecha_envio')->nullable();

            // Relaciones sin cascada
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('bus_id')->constrained('buses');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_de_reguladors');
    }
};
