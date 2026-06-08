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
        Schema::create('cliente_vehiculos', function (Blueprint $table) {
            $table->id();


            $table->string('nombre_vehiculo');
            $table->string('modelo');
            $table->string('placa')->nullable();
            $table->foreign('usuario_cliente_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_cliente_id')->nullable();
            $table->string('estado')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_vehiculos');
    }
};
