<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    use HasFactory;
    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad',
        'precio_compra',
        'subtotal'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

}
