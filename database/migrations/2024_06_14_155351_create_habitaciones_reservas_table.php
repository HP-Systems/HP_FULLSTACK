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
        Schema::create('habitaciones_reservas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservaID');
            $table->unsignedBigInteger('habitacionID');

            $table->foreign('reservaID')->references('id')->on('reservas');
            $table->foreign('habitacionID')->references('id')->on('habitaciones');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitaciones_reservas');
    }
};
