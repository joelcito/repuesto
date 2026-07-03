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
        Schema::create('devolucion_detalle', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('devolucion_id');
            $table->unsignedBigInteger('producto_id');


            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();


            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);

            $table->string('estado')->nullable();

            $table->dateTime('deleted_at')->nullable();

            $table->timestamps();


            $table->foreign('devolucion_id')->references('id')->on('devoluciones');
            $table->foreign('producto_id')->references('id')->on('productos');


            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('devolucion_detalle');
    }
};
