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
            $table->string('medidas')->nullable()->after('descripcion_producto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incorporaciones', function (Blueprint $table) {
            //
        });
    }
};
