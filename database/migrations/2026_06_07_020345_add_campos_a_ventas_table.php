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
        Schema::table('ventas', function (Blueprint $table) {
            $table->dateTime('fecha')->nullable();
            $table->string('numero_factura')->nullable();
            $table->string('nit')->nullable();
            $table->string('razon_social')->nullable();
            $table->enum('estado_pago', ['PAGADO', 'DEUDA', 'PARCIAL'])->default('PAGADO');
            $table->text('descripcion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
