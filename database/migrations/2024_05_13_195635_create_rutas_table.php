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
        Schema::create('rutas', function (Blueprint $table) {
            $table->bigIncrements('id_paradas');  
            $table->string('nombre_parada', 100);  
            $table->string('sentido', 100);        
            $table->point('lat_long');             
            $table->unsignedBigInteger('id_ruta'); 

           //relacion con ruta
            $table->foreign('id_ruta')
                  ->references('id')   
                  ->on('rutas')        
                  ->onDelete('cascade'); 

            $table->timestamps();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
