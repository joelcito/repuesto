<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoImagen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'producto_imagenes';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'producto_id',
        'imagen',
        'orden',
        'estado',
        'parent_id',
        'deleted_at'
    ];
  

     public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

}