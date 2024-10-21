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
        DB::statement('
            CREATE TABLE paradas (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                direccion VARCHAR(255) NOT NULL,
                ubicacion POINT NOT NULL,
                id_ruta BIGINT UNSIGNED,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (id_ruta) REFERENCES rutas(id) ON DELETE CASCADE
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paradas');
    }
};
