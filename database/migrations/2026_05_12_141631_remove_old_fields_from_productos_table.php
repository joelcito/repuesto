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

            // ELIMINAR COLUMNAS ANTIGUAS
            if (Schema::hasColumn('productos', 'categoria')) {
                $table->dropColumn('categoria');
            }

            if (Schema::hasColumn('productos', 'proveedor')) {
                $table->dropColumn('proveedor');
            }

            if (Schema::hasColumn('productos', 'ubicacion')) {
                $table->dropColumn('ubicacion');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {

            // RECUPERAR COLUMNAS
            if (!Schema::hasColumn('productos', 'categoria')) {
                $table->string('categoria')->nullable();
            }

            if (!Schema::hasColumn('productos', 'proveedor')) {
                $table->string('proveedor')->nullable();
            }

            if (!Schema::hasColumn('productos', 'ubicacion')) {
                $table->string('ubicacion')->nullable();
            }

        });
    }
};
