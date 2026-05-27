<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Request;

class Devolucion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'movimientos';

    protected $fillable = [

        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'venta_id',
        'caja_id',
        'cliente_id',
        'tipo',
        'total',
        'motivo',

        'estado',
        'usuario_creador_id'
    ];



}