<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Request;

class Devolucion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'devoluciones';

    protected $fillable = [

        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'venta_id',
        'caja_id',
        'usuario_cliente_id',
        'tipo',
        'total',
        'motivo',

        'estado',
        'usuario_creador_id'
    ];


    public function venta()
    {
        return $this->belongsTo('App\Models\Venta', 'venta_id');
    }
    public function caja()
    {
        return $this->belongsTo('App\Models\Caja', 'caja_id');
    }
    public function cliente()
    {
        return $this->belongsTo('App\Models\User', 'usuario_cliente_id');

    }

    public function detalles()
    {
        return $this->hasMany(DevolucionDetalle::class, 'devolucion_id');
    }


}