<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EgresoCaja extends Model
{
    use HasFactory;

    protected $table = 'egresos_caja';

    protected $fillable = [
        'caja_id',
        'descripcion',
        'monto'
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
}
