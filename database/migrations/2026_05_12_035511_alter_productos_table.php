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
        Schema::table('productos', function (Blueprint $table) {

            // CATEGORIA
            if (!Schema::hasColumn('productos', 'categoria_id')) {

                $table->unsignedBigInteger('categoria_id')->nullable();

                $table->foreign('categoria_id')
                    ->references('id')
                    ->on('categorias');
            }

            // SUCURSAL
            if (!Schema::hasColumn('productos', 'sucursal_id')) {

                $table->unsignedBigInteger('sucursal_id')->nullable();

                $table->foreign('sucursal_id')
                    ->references('id')
                    ->on('sucursales');
            }

            // PROVEEDOR
            if (!Schema::hasColumn('productos', 'proveedor_id')) {

                $table->unsignedBigInteger('proveedor_id')->nullable();

                $table->foreign('proveedor_id')
                    ->references('id')
                    ->on('proveedores');
            }

        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {

            if (Schema::hasColumn('productos', 'categoria_id')) {

                $table->dropForeign(['categoria_id']);
                $table->dropColumn('categoria_id');
            }

            if (Schema::hasColumn('productos', 'sucursal_id')) {

                $table->dropForeign(['sucursal_id']);
                $table->dropColumn('sucursal_id');
            }

            if (Schema::hasColumn('productos', 'proveedor_id')) {

                $table->dropForeign(['proveedor_id']);
                $table->dropColumn('proveedor_id');
            }

        });
    }

};
