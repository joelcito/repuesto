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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();

            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();

            $table->foreign('venta_id')->references('id')->on('ventas');
            $table->unsignedBigInteger('venta_id');

            $table->foreign('caja_id')->references('id')->on('cajas');
            $table->unsignedBigInteger('caja_id')->nullable();

            $table->foreign('usuario_cliente_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_cliente_id')->nullable();


            $table->string('tipo');
            $table->decimal('total', 10, 2)->default(0);
            $table->text('motivo')->nullable();

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
        Schema::dropIfExists('devoluciones');
    }
};
