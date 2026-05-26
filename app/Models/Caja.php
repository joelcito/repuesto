<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caja extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cajas';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'usuario_id',
        'sucursal_id',

        'total_ingresos',
        'total_egresos',

        'monto_apertura',
        'monto_cierre',

        'fecha_apertura',
        'fecha_cierra',

        'estado',
        'deleted_at',
    ];

    public function sucursal()
    {
        return $this->belongsTo('App\Models\Sucursal', 'sucursal_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class, 'caja_id');
    }

    public function venta()
    {
        return $this->belongsTo('App\Models\Ventas', 'venta_id');
    }

}
