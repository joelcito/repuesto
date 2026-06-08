<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pagos';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'venta_id',
        'caja_id',
        'sucursal_id',
        'sub_categoria_id',

        'monto',
        'cambio',
        'fecha',
        'descripcion',
        'tipo_pago',


        'estado',
        'deleted_at',
    ];

    public function usuario()
    {
        return $this->belongsTo('App\Models\User', 'usuario_creador_id');
    }

    public function sucursal()
    {
        return $this->belongsTo('App\Models\Sucursal', 'sucursal_id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }




}
