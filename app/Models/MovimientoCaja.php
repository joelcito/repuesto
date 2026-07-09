<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoCaja extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'movimientos_caja';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'caja_id',
        'venta_id',

        'tipo',
        'metodo_pago',
        'monto',
        'origen_dinero',

        'descripcion',
        'fecha',

        'estado',
        'deleted_at',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
