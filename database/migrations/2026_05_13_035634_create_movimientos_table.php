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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();


            $table->foreignId('producto_id');
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->foreignId('sucursal_id');
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->foreignId('usuario_creador_id')->nullable();

            // TIPO: INGRESO / SALIDA
            $table->string('tipo'); // ingreso | salida

            $table->integer('cantidad')->default(0);


            $table->decimal('precio_compra', 10, 2)->nullable();
            $table->decimal('precio_venta', 10, 2)->nullable();

            // SOLO PARA SALIDAS
            $table->string('motivo')->nullable();
            // perdida | robo | deterioro | venta
            $table->dateTime('fecha')->nullable();
            $table->text('descripcion')->nullable();

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
        Schema::dropIfExists('movimientos');
    }
};
