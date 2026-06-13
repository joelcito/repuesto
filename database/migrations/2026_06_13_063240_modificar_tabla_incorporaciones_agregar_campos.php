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
        Schema::table('incorporaciones', function (Blueprint $table) {

            // NUEVOS CAMPOS
            $table->string('nombre_producto')
                ->nullable()
                ->after('producto_id');

            $table->text('descripcion_producto')
                ->nullable()
                ->after('nombre_producto');

            // producto_id nullable
            $table->unsignedBigInteger('producto_id')
                ->nullable()
                ->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incorporaciones', function (Blueprint $table) {

            $table->dropColumn('nombre_producto');

            $table->dropColumn('descripcion_producto');

            // volver obligatorio
            $table->unsignedBigInteger('producto_id')
                ->nullable(false)
                ->change();

        });
    }
};
