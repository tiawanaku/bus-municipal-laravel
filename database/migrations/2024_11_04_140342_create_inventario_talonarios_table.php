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
        Schema::create('inventario_talonarios', function (Blueprint $table) {
            $table->id();

            // 🟡 CAMBIO: Restrict en lugar de cascade
            $table->foreignId('cajero_id')->constrained('cajeros')->onDelete('restrict');

            // Preferenciales
            $table->integer('preferencial_del')->nullable();
            $table->integer('preferencial_al')->nullable();
            $table->integer('cantidad_preferenciales')->nullable();
            $table->integer('rango_inicial_preferencial')->nullable();
            $table->integer('rango_final_preferencial')->nullable();
            $table->integer('total_boletos_preferenciales')->nullable();
            $table->decimal('total_aproximado_bolivianos_preferencial', 10, 2)->nullable();
            $table->integer('cantidad_restante_preferencial')->nullable();

            // Regulares
            $table->integer('regular_del')->nullable();
            $table->integer('regular_al')->nullable();
            $table->integer('cantidad_regulares')->nullable();
            $table->integer('rango_inicial_regular')->nullable();
            $table->integer('rango_final_regular')->nullable();
            $table->integer('total_boletos_regulares')->nullable();
            $table->decimal('total_aproximado_bolivianos_regular', 10, 2)->nullable();
            $table->integer('cantidad_restante_regular')->nullable();

            // Info adicional
            $table->integer('estado_preferencial')->nullable();
            $table->integer('estado_regular')->nullable();

            $table->string('tipo_talonarios')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->string('observaciones')->nullable();

            $table->decimal('total_recaudacion_bolivianos', 10, 2)->nullable();

            // Campos del archivo PDF y cita
            $table->string('cite_nota_solicitud')->nullable();
            $table->string('cite_nota_solicitud_filename')->nullable();
            $table->string('n_cite')->nullable();
            $table->string('gestion')->nullable();
            $table->string('n_dosificacion')->nullable();
            $table->string('numero_autorizacion')->nullable();
            $table->date('fecha_solicitud_dosificacion')->nullable();
            $table->date('fecha_autorizacion')->nullable();
            $table->date('fecha_activacion')->nullable();

            // 🆕 CAMPOS ADICIONALES
            $table->enum('estado', ['disponible', 'agotado', 'inactivo'])->default('disponible');
            $table->integer('talonarios_entregados')->default(0);
            $table->integer('talonarios_devueltos')->default(0);
            $table->integer('talonarios_vendidos')->default(0);
            $table->date('fecha_cierre')->nullable();
            $table->string('observaciones_cierre')->nullable();

            $table->timestamps();

            // 🟡 ÍNDICES PARA MEJOR PERFORMANCE
            $table->index('cajero_id');
            $table->index('estado');
            $table->index('fecha_entrega');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_talonarios');
    }
};