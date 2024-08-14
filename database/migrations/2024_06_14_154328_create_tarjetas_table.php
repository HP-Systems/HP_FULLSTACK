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
        Schema::create('tarjetas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tipoID');
            $table->integer('status');
            $table->string('numero', 10);
            // 0 ocupada, 1 disponible, 3 deshabilitada

            $table->foreign('tipoID')->references('id')->on('tipo_tarjeta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */ 
    public function down(): void
    {
        Schema::dropIfExists('tarjetas');
    }
};
