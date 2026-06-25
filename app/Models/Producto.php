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
        'vehiculos_compatibles',
        'categoria_id',
        'proveedor_id',
        'sucursal_id',
        'numero_parte_vehiculo',
        'stock_actual',
        'stock_minimo',
        'unidad',
        'precio_compra',
        'precio_venta',
        'precio_mayor',
        'compra_ingreso',
        'observaciones',
        'imagen',
        'estado',
        'medidas',
        'ubicacion',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'deleted_at',
    ];
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'unidad_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class, 'producto_id')
            ->orderBy('orden');
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'producto_id');
    }
}
