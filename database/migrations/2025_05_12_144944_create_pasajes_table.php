<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pasajes', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->decimal('precio', 5, 2);
            $table->text('descripcion')->nullable();
            $table->string('color')->nullable(); 
            $table->string('imagen')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasajes');
    }
};
