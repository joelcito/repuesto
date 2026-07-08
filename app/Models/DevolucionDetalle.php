<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevolucionDetalle extends Model
{
    use SoftDeletes;

    protected $table = 'devolucion_detalle';

    protected $fillable = [
        'devolucion_id',
        'producto_id',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'descuento',
        'estado'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class);
    }
}

