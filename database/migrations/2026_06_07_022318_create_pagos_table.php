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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();

            $table->decimal('monto', 10, 2);
            $table->decimal('cambio', 10, 2)->default(0);

            // EFECTIVO | QR | TRANSFERENCIA | TARJETA
            $table->string('tipo_pago')->nullable();
            // VENTA | ABONO | DEVOLUCION
            $table->string('descripcion')->nullable();
            $table->dateTime('fecha')->nullable();


            // foreign keys        

            $table->foreign('venta_id')->references('id')->on('ventas');
            $table->unsignedBigInteger('venta_id')->nullable();

            $table->foreign('caja_id')->references('id')->on('cajas');
            $table->unsignedBigInteger('caja_id')->nullable();

            $table->foreign('sucursal_id')->references('id')->on('sucursales');
            $table->unsignedBigInteger('sucursal_id')->nullable();


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
        Schema::dropIfExists('pagos');
    }
};
