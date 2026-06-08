<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Request;


class ClienteVehiculo extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cliente_vehiculos';

    protected $fillable = [

        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'usuario_cliente_id',
        'nombre_vehiculo',
        'modelo',
        'placa',
        'estado',


        'estado',
        'deleted_at'
    ];

    public function cliente()
    {
        return $this->belongsTo('App\Models\User', 'usuario_cliente_id');

    }
}