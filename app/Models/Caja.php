<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'monto_inicial',
        'total_ventas',
        'total_egresos',
        'monto_cierre',
        'diferencia',
        'fecha_apertura',
        'fecha_cierre',
        'estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    // Relación con ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'caja_id', 'id');
    }
                    

     // Relación con egresos
    public function egresos()
    {
        return $this->hasMany(EgresoCaja::class, 'caja_id');
    }
}
