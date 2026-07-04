<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncorporacionImagen extends Model
{
    protected $table = 'incorporacion_imagens';

    protected $fillable = [
        'incorporacion_id',
        'ruta'
    ];

    public function incorporacion()
    {
        return $this->belongsTo(Incorporacion::class);
    }
}
