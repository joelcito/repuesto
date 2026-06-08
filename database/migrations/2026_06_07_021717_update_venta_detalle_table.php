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
        Schema::table('venta_detalle', function (Blueprint $table) {

            // nuevos campos
            $table->decimal('precio_compra', 10, 2)
                ->nullable()
                ->after('cantidad_devuelta');

            $table->decimal('precio_original', 10, 2)
                ->nullable()
                ->after('precio_compra');

            $table->decimal('descuento', 10, 2)
                ->default(0)
                ->after('precio_original');

            $table->text('descripcion')
                ->nullable()
                ->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venta_detalle', function (Blueprint $table) {

            $table->dropColumn([
                'precio_compra',
                'precio_original',
                'descuento',
                'descripcion'
            ]);
        });
    }
};
