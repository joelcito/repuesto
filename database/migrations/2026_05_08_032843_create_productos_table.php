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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();

            $table->string('codigo_barras')->nullable()->unique();
            $table->string('codigo_interno')->nullable()->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();

            // Clasificación
            $table->string('categoria')->nullable();
            // $table->string('marca')->nullable();
            $table->string('numero_parte_vehiculo')->nullable();

            // Stock
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->string('unidad')->nullable(); // ej: unidad, caja, litro

            // Precios
            $table->decimal('precio_compra', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->decimal('precio_mayor', 10, 2)->nullable();

            // Ubicación y proveedor
            $table->string('ubicacion')->nullable();
            $table->string('proveedor')->nullable();

            // Extras
            $table->text('observaciones')->nullable();
            $table->string('imagen')->nullable(); // ruta o nombre de archivo

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
        Schema::dropIfExists('productos');
    }
};
