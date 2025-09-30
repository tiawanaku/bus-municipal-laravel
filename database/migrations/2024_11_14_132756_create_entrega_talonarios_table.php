<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrega_talonarios', function (Blueprint $table) {
            $table->id();
            
            // 🟡 RELACIONES CON RESTRICT
            $table->foreignId('inventario_id')->constrained('inventario_talonarios')->onDelete('restrict');
            $table->foreignId('cajero_id')->constrained('cajeros')->onDelete('restrict');
            
            // Campos existentes (mantener igual)
            $table->integer('preferencial_del')->nullable();
            $table->integer('preferencial_al')->nullable();
            $table->integer('cantidad_preferenciales')->nullable();
            $table->integer('rango_inicial_preferencial')->nullable();
            $table->integer('rango_final_preferencial')->nullable();
            $table->integer('total_boletos_preferenciales')->nullable();
            $table->decimal('total_aproximado_bolivianos_preferencial', 10, 2)->nullable();
            $table->integer('cantidad_restante_preferencial')->nullable();
            
            $table->integer('regular_del')->nullable();
            $table->integer('regular_al')->nullable();
            $table->integer('cantidad_regulares')->nullable();
            $table->integer('rango_inicial_regular')->nullable();
            $table->integer('rango_final_regular')->nullable();
            $table->integer('total_boletos_regulares')->nullable();
            $table->decimal('total_aproximado_bolivianos_regular', 10, 2)->nullable();
            $table->integer('cantidad_restante_regular')->nullable();
            
            $table->integer('estado_preferencial')->nullable();
            $table->integer('estado_regular')->nullable();
            $table->string('tipo_talonario')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->string('observaciones')->nullable();
            $table->decimal('total_recaudacion_bolivianos', 10, 2)->nullable();
            
            // 🆕 CAMPOS ADICIONALES
            $table->enum('estado', ['activo', 'devuelto', 'agotado'])->default('activo');
            $table->date('fecha_devolucion')->nullable();
            $table->string('observaciones_devolucion')->nullable();
            $table->decimal('monto_recaudado_real', 10, 2)->nullable();
            
            $table->timestamps();
            
            // ÍNDICES
            $table->index('inventario_id');
            $table->index('cajero_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_talonarios');
    }
};