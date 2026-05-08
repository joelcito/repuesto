<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'codigo_barras',
        'codigo_interno',
        'nombre',
        'descripcion',
        'categoria',
        'marca',
        'numero_parte_vehiculo',
        'stock_actual',
        'stock_minimo',
        'unidad',
        'precio_compra',
        'precio_venta',
        'precio_mayor',
        'ubicacion',
        'proveedor',
        'observaciones',
        'imagen',
        'estado',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'estado',
        'deleted_at',
    ];

}
