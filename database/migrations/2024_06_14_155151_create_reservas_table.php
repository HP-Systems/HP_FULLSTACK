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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('huespedID');
            $table->date('fecha_entrada');
            $table->date('fecha_salida');
            $table->time('hora_entrada');
            $table->time('hora_salida');
            $table->boolean('status')->default(1);

            $table->foreign('huespedID')->references('id')->on('huespedes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
