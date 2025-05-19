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
        //
        // Eliminar la tabla kilometrajes si existe
        Schema::dropIfExists('kilometrajes');

        // Reemplazar n_bus con bus_id
        Schema::table('salida_de_buses', function (Blueprint $table) {
            
            if (Schema::hasColumn('salida_de_buses', 'n_bus')) {
                $table->dropColumn('n_bus');
            }



            // Agregar columnas de kilometraje y mantenimiento
            $table->decimal('kilometraje_salida', 10, 2)->nullable()->after('motivo_no_salida');
            $table->decimal('kilometraje_llegada', 10, 2)->nullable()->after('kilometraje_salida');
            $table->string('tipo_mantenimiento')->nullable()->after('kilometraje_llegada');
            $table->enum('status_mantenimiento', ['pendiente', 'realizado'])->default('pendiente')->after('tipo_mantenimiento');

            $table->unsignedBigInteger('bus_id')->after('status_mantenimiento');
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::create('kilometrajes', function (Blueprint $table) {
            $table->id();
            $table->year('anio'); 
            $table->decimal('kilometraje', 10, 2); 
            $table->unsignedBigInteger('bus_id'); 
            $table->timestamps();
            

        });

         // Revertir cambios en salida_de_buses
         Schema::table('salida_de_buses', function (Blueprint $table) {
            $table->dropForeign(['bus_id']);
            $table->dropColumn(['bus_id', 'kilometraje_salida', 'kilometraje_llegada', 'tipo_mantenimiento', 'status_mantenimiento']);
            $table->string('n_bus')->nullable(); 
        });
    }
};
