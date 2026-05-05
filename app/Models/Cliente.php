<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'nombres',
        'ap_paterno',
        'ap_materno',
        'celular',
        'cedula',
        'nit',
        'razon_social',
        'direccion',
        'imagen',
        'imagen_CI_anverso',
        'imagen_CI_reverso',
        'nombre_referencia_1',
        'celular_referencia_1',
        'nombre_referencia_2',
        'celular_referencia_2',
        'nombre_referencia_3',
        'celular_referencia_3',

        'estado',
        'deleted_at',
    ];


}
