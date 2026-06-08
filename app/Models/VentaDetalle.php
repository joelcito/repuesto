<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VentaDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'venta_detalle';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'venta_id',
        'producto_id',
        'cantidad',
        'cantidad_devuelta',
        'precio_compra',
        'precio_original',
        'precio_unitario',
        'descuento',
        'tipo_precio',
        'subtotal',
        'descripcion',
        'estado',
        'deleted_at',
    ];

    public function producto()
    {
        return $this->belongsTo('App\Models\Producto', 'producto_id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

}
