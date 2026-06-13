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

            $table->integer('cantidad')
                ->nullable()
                ->change();

            $table->decimal('precio_compra', 10, 2)
                ->nullable()
                ->change();

            $table->decimal('precio_venta', 10, 2)
                ->nullable()
                ->change();

            $table->string('motivo')
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

            $table->integer('cantidad')
                ->nullable(false)
                ->change();

            $table->decimal('precio_compra', 10, 2)
                ->nullable(false)
                ->change();

            $table->decimal('precio_venta', 10, 2)
                ->nullable(false)
                ->change();

            $table->string('motivo')
                ->nullable(false)
                ->change();

        });
    }
};
