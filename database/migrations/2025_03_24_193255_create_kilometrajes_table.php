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
        Schema::create('kilometrajes', function (Blueprint $table) {
            $table->id();
            $table->year('anio'); 
            $table->decimal('kilometraje', 10, 2); 
            $table->unsignedBigInteger('bus_id'); 
            $table->timestamps();
            

            // Claves foráneas
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kilometrajes');
    }
};
