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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_creador_id')->nullable()->after('id');
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable()->after('usuario_creador_id');
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable()->after('usuario_modificador_id');
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');

            $table->unsignedBigInteger('rol_id')->nullable()->after('usuario_eliminador_id');
            $table->foreign('rol_id')->references('id')->on('roles');
            $table->unsignedBigInteger('sucursal_id')->nullable()->after('rol_id');
            $table->foreign('sucursal_id')->references('id')->on('sucursales');

            $table->string('nombres')->nullable()->after('sucursal_id');
            $table->string('ap_paterno')->nullable()->after('nombres');
            $table->string('ap_materno')->nullable()->after('ap_paterno');
            $table->string('cedula')->nullable()->after('ap_materno');

            $table->string('celular')->nullable()->after('cedula');

            $table->string('nit')->nullable()->after('celular');
            $table->string('razon_social')->nullable()->after('nit');
            $table->string('direccion')->nullable()->after('razon_social');
            $table->string('imagen')->nullable()->after('direccion');
            $table->string('imagen_CI_anverso')->nullable()->after('imagen');
            $table->string('imagen_CI_reverso')->nullable()->after('imagen_CI_anverso');
            $table->string('nombre_referencia_1')->nullable()->after('imagen_CI_reverso');
            $table->string('celular_referencia_1')->nullable()->after('nombre_referencia_1');
            $table->string('nombre_referencia_2')->nullable()->after('celular_referencia_1');
            $table->string('celular_referencia_2')->nullable()->after('nombre_referencia_2');
            $table->string('nombre_referencia_3')->nullable()->after('celular_referencia_2');
            $table->string('celular_referencia_3')->nullable()->after('nombre_referencia_3');

            $table->string('estado')->nullable()->after('celular');
            $table->datetime('deleted_at')->nullable()->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['usuario_creador_id']);
            $table->dropColumn('usuario_creador_id');
            $table->dropForeign(['usuario_modificador_id']);
            $table->dropColumn('usuario_modificador_id');
            $table->dropForeign(['usuario_eliminador_id']);
            $table->dropColumn('usuario_eliminador_id');
            $table->dropForeign(['rol_id']);
            $table->dropColumn('rol_id');

            $table->dropColumn('nombres');
            $table->dropColumn('ap_paterno');
            $table->dropColumn('ap_materno');
            $table->dropColumn('cedula');
            $table->dropColumn('direccion');
            $table->dropColumn('celular');

            $table->dropColumn('estado');
            $table->dropColumn('deleted_at');
        });
    }
};
