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
        Schema::table('producto_imagenes', function (Blueprint $table) {

            $table->foreignId('usuario_creador_id')
                ->nullable()
                ->constrained('users');

            $table->foreignId('usuario_modificador_id')
                ->nullable()
                ->constrained('users');

            $table->foreignId('usuario_eliminador_id')
                ->nullable()
                ->constrained('users');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('producto_imagenes', function (Blueprint $table) {

            $table->dropForeign(['usuario_creador_id']);
            $table->dropForeign(['usuario_modificador_id']);
            $table->dropForeign(['usuario_eliminador_id']);

            $table->dropColumn([
                'usuario_creador_id',
                'usuario_modificador_id',
                'usuario_eliminador_id'
            ]);
        });
    }
};
