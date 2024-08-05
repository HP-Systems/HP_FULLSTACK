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
            $table->unsignedBigInteger('habitacion_reservaID');
            //$table->unsignedBigInteger('reservaID');
            $table->unsignedBigInteger('tarjetaID');
            $table->date('fecha');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->foreign('personalID')->references('id')->on('personal');
            $table->foreign('habitacion_reservaID')->references('id')->on('habitaciones_reservas');
            //$table->foreign('reservaID')->references('id')->on('reservas');
            $table->foreign('tarjetaID')->references('id')->on('tarjetas');
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