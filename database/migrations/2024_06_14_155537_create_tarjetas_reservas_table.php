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
        Schema::create('tarjetas_reservas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservaID');
            $table->unsignedBigInteger('tarjetaID');
            $table->boolean('status');

            $table->foreign('reservaID')->references('id')->on('reservas');
            $table->foreign('tarjetaID')->references('id')->on('tarjetas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjetas_reservas');
    }
};
