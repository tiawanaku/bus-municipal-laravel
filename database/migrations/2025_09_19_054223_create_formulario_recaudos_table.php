<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulario_recaudo', function (Blueprint $table) {
            $table->id();
            
            // 🟡 RELACIONES CON RESTRICT
            $table->foreignId('anfitrion_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('conductor_id')->constrained('conductors')->onDelete('restrict'); // 👈 corregido
            $table->foreignId('bus_id')->constrained('buses')->onDelete('restrict');
            $table->foreignId('entrega_talonario_anfitrion_id')
                  ->nullable()
                  ->constrained('entrega_talonarios_anfitrion')
                  ->onDelete('restrict');
            
            // Campos existentes
            $table->string('rutas');
            $table->string('horario');
            $table->string('N_ficha');
            
            $table->integer('cantidad_ventas_regulares')->nullable();
            $table->integer('rango_inicial_regulares')->nullable();
            $table->integer('rango_final_regulares')->nullable();
            $table->decimal('monto_recaudado_regular', 10, 2)->nullable();
            
            $table->integer('cantidad_ventas_preferenciales')->nullable();
            $table->integer('rango_inicial_preferencial')->nullable();
            $table->integer('rango_final_preferencial')->nullable();
            $table->decimal('monto_recaudado_preferencial', 10, 2)->nullable();
            
            $table->decimal('total_recaudo_regular_preferencial', 10, 2)->nullable();
            
            // 🆕 CAMPOS ADICIONALES
            $table->date('fecha_recaudo');
            $table->enum('estado', ['pendiente', 'verificado', 'contabilizado'])->default('pendiente');
            $table->string('comprobante_filename')->nullable();
            $table->text('observaciones_verificacion')->nullable();
            
            $table->timestamps();
            
            // ÍNDICES
            $table->index('anfitrion_id');
            $table->index('fecha_recaudo');
            $table->index('estado');
            $table->index('entrega_talonario_anfitrion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulario_recaudo');
    }
};
