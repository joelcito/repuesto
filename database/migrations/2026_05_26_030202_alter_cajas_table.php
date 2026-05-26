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
        Schema::table('cajas', function (Blueprint $table) {

            $table->renameColumn('usuario_venta_id', 'usuario_id');

            $table->renameColumn('fecha_cierra', 'fecha_cierre');

            $table->decimal('total_ingresos', 12, 2)
                ->default(0)
                ->after('monto_cierre');

            $table->decimal('total_egresos', 12, 2)
                ->default(0)
                ->after('total_ingresos');

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
