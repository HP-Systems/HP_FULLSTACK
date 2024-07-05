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
        Schema::create('servicios_reservas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('servicioID');
            $table->unsignedBigInteger('habitacionReservaID');
            $table->integer('cantidad')->default(1);
            $table->integer('status');
            // 0 cancelado, 1 entregado, 2 en proceso de entrega
            

            $table->foreign('servicioID')->references('id')->on('servicios');
            $table->foreign('habitacionReservaID')->references('id')->on('habitaciones_reservas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios_reservas');
    }
};
