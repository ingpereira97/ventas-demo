<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cobro;
use App\Models\Caja;
use Illuminate\Http\Request;

class CobroController extends Controller
{
    public function create(Venta $venta)
    {
        return view('cobros.create', compact('venta'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'metodo_pago' => 'required|string|max:50',
            'monto_pagado' => 'required|numeric|min:0',
        ]);
        // 🔎 Buscar venta
        $venta = Venta::findOrFail($request->venta_id);

        // 💰 saldo antes

        $saldoAnterior = $venta->saldo;
        

        // 🔥 MONTO REAL A APLICAR (CLAVE)
        if ($request->monto_pagado >= $saldoAnterior) {
            $montoAplicado = $saldoAnterior; // paga todo
        } else {
            $montoAplicado = $request->monto_pagado; // cuota
        }

        // 🔄 vuelto
        $vuelto = max(0, $request->monto_pagado - $saldoAnterior);

        // 💾 guardar cobro
        Cobro::create([
            'venta_id' => $venta->id,
            'monto_pagado' => $request->monto_pagado,
            'monto_aplicado' => $montoAplicado, // 🔥 ESTA ES LA CLAVE
            'metodo_pago' => $request->metodo_pago,
        ]);

        // 🔄 actualizar saldo
        $venta->saldo = $saldoAnterior - $montoAplicado;

        // 🔄 estado
        if ($venta->saldo <= 0) {
            $venta->saldo = 0;
            $venta->estado = 'pagado';
        } else {
            $venta->estado = 'pendiente';
        }

        $venta->save();

        // 🔥 SUMAR A CAJA (CORRECTO)
        $caja = Caja::where('user_id', auth()->id())
            ->where('estado', 'abierta')
            ->first();

        if ($caja) {
            $caja->increment('total_ventas', $montoAplicado);
        }

        // 🔁 redirect
        return redirect()
            ->route('ventas.index')
            ->with([
                'success' => 'Pago registrado correctamente.',
                'vuelto' => $vuelto
            ]);
    }

        public function index()
    {
        // Obtener todos los cobros, puedes personalizar esto según tus necesidades (filtrar, paginar, etc.)
        $cobros = Cobro::whereHas('venta', function ($q) {
            $q->where('estado', '!=', 'anulada'); // 🔥 CLAVE
        })
        ->with('venta.cliente')
        ->latest()
        ->get();

        return view('cobros.index', compact('cobros'));
    }

}

