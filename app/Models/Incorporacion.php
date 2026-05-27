<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Request;

class Incorporacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'movimientos';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'producto_id',
        'sucursal_id',
        'cantidad',
        'precio_compra',
        'precio_venta',
        'motivo',
        'descripcion',

        'estado',
        'usuario_creador_id'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }


}