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
        Schema::create('limpiezas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personalID');
            $table->unsignedBigInteger('habitacionID');
            $table->unsignedBigInteger('tarjetaID');
            $table->date('fecha'); 
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->boolean('status')->default(1);

            $table->foreign('personalID')->references('id')->on('personal');
            $table->foreign('habitacionID')->references('id')->on('habitaciones');
            $table->foreign('tarjetaID')->references('id')->on('tarjetas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('limpiezas');
    }
};
