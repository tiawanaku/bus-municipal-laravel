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
        Schema::create('cajeros', function (Blueprint $table) {
            $table->id();

            $table->enum('tipo_cajero', ['principal', 'secundario'])->default('secundario'); // nuevo campo
            $table->unsignedBigInteger('cajero_padre_id')->nullable(); // para jerarquía
            $table->foreign('cajero_padre_id')->references('id')->on('cajeros')->onDelete('set null');

            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('ci');
            $table->string('complemento')->nullable();
            $table->string('ci_expedido');
            $table->string('celular', 20);
            $table->enum('genero', ['masculino', 'femenino', 'otro']);

            $table->string('numero_contrato');
            $table->date('fecha_inicio_contrato')->nullable();
            $table->date('fecha_fin_contrato')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajeros');
    }
};