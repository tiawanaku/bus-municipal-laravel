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
        Schema::create('rango_mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->decimal('km_min', 8, 2); 
            $table->decimal('km_max', 8, 2); 
            $table->string('tipo_mantenimiento', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rango_mantenimientos');
    }
};
