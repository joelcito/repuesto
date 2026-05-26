<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'usuario_cliente_id',
        'usuario_venta_id',
        'caja_id',

        'subtotal',
        'descuento',
        'total',
        'metodo_pago',
        'observacion',

        'estado',
        'deleted_at',
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_venta_id');
    }

    public function cliente()
    {
        // return $this->belongsTo('App\Models\Cliente', 'cliente_id');
        return $this->belongsTo('App\Models\User', 'usuario_cliente_id');

    }
    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

}
