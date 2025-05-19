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
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_mantenimiento');
            $table->decimal('km_anterior', 10, 2)->nullable();
            $table->decimal('km_actual', 10, 2);
            $table->text('tipo_mantenimiento');
            $table->text('observaciones')->nullable();
            $table->foreignId('bus_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
