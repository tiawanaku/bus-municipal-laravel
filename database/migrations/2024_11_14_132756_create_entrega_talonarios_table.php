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
        // Agrega columna entregado_at si no existe
        if (!Schema::hasColumn('inventario_talonarios', 'entregado_at')) {
            Schema::table('inventario_talonarios', function (Blueprint $table) {
                $table->dateTime('entregado_at')->nullable();
            });
        }

        // Agrega columna cajero_actual_id si no existe
        if (!Schema::hasColumn('inventario_talonarios', 'cajero_actual_id')) {
            Schema::table('inventario_talonarios', function (Blueprint $table) {
                $table->foreignId('cajero_actual_id')
                    ->nullable()
                    ->constrained('cajeros')
                    ->onDelete('cascade'); // Importante
            });
        }

        // Crea tabla entrega_talonarios si no existe
        if (!Schema::hasTable('entrega_talonarios')) {
            Schema::create('entrega_talonarios', function (Blueprint $table) {
                $table->id();
                $table->string('responsable_entrega');
                $table->foreignId('cajero_id')
                    ->constrained('cajeros')
                    ->onDelete('cascade'); // Importante
                $table->integer('numero_paquetes_entregados');
                $table->integer('cantidad_talonarios');
                $table->integer('cantidad_tickets');
                $table->date('fecha_entrega');
                $table->string('tipo_talonarios');
                $table->text('observaciones')->nullable();
                $table->json('inventario_talonarios_ids')->nullable();
                $table->timestamps();
            });
        }

        // Crear tabla pivot con índice renombrado para evitar error de longitud
        Schema::create('entrega_talonario_inventario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entrega_talonario_id');
            $table->unsignedBigInteger('inventario_talonarios_id');
            $table->timestamps();

            $table->foreign('entrega_talonario_id')
                ->references('id')
                ->on('entrega_talonarios')
                ->onDelete('cascade');

            $table->foreign('inventario_talonarios_id')
                ->references('id')
                ->on('inventario_talonarios')
                ->onDelete('cascade');

            // Índice con nombre personalizado
            $table->index(
                ['entrega_talonario_id', 'inventario_talonarios_id'],
                'etii_etid_itid_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrega_talonario_inventario');
        Schema::dropIfExists('entrega_talonarios');

        // Elimina las columnas solo si existen
        if (Schema::hasColumn('inventario_talonarios', 'cajero_actual_id')) {
            Schema::table('inventario_talonarios', function (Blueprint $table) {
                $table->dropForeign(['cajero_actual_id']);
                $table->dropColumn('cajero_actual_id');
            });
        }

        if (Schema::hasColumn('inventario_talonarios', 'entregado_at')) {
            Schema::table('inventario_talonarios', function (Blueprint $table) {
                $table->dropColumn('entregado_at');
            });
        }
    }
};
