<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\EgresoCaja;
use App\Models\Venta;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Cobro;

class CajaController extends Controller
{
    public function index()
    {
        $caja = Caja::where('user_id', auth()->id())
                    ->where('estado', 'abierta')
                    ->first();

        $ultimaCaja = Caja::where('user_id', auth()->id())
                    ->where('estado', 'cerrada')
                    ->latest('fecha_cierre')
                    ->first();

        $totalVentas = 0;
        $totalEgresos = 0;
        $totalCompras = 0;
        $saldoActual = 0;

        if ($caja) {
            $totalVentas = $caja->total_ventas;

            $totalEgresos = $caja->egresos()->sum('monto');

            $totalCompras = $caja->egresos()
                ->where('descripcion', 'like', 'Compra%')
                ->sum('monto');

            $saldoActual = $caja->monto_inicial + $totalVentas - $totalEgresos;
        }

        return view('caja.index', compact(
            'caja',
            'ultimaCaja',
            'totalVentas',
            'totalEgresos',
            'totalCompras',
            'saldoActual'
        ));
    }

    public function abrir(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0'
        ]);

        $existe = Caja::where('user_id', auth()->id())
                    ->where('estado', 'abierta')
                    ->exists();

        if ($existe) {
            return back()->with('error', 'Ya tienes una caja abierta.');
        }

        Caja::create([
            'user_id' => auth()->id(),
            'monto_inicial' => $request->monto_inicial,
            'total_ventas' => 0,
            'total_compras' => 0,            
            'total_egresos' => 0,
            'fecha_apertura' => now(),
            'estado' => 'abierta'
        ]);

        return redirect()->route('caja.index')->with('success', 'Caja abierta correctamente.');
    }

    public function agregarEgreso(Request $request)
    {
        $request->validate([
            'descripcion' => 'required',
            'monto' => 'required|numeric|min:1'
        ]);

        $caja = Caja::where('user_id', auth()->id())
                    ->where('estado', 'abierta')
                    ->first();

        if (!$caja) {
            return back()->with('error', 'No hay caja abierta.');
        }

        EgresoCaja::create([
            'caja_id' => $caja->id,
            'descripcion' => $request->descripcion,
            'monto' => $request->monto
        ]);

        $caja->increment('total_egresos', $request->monto);

        return back()->with('success', 'Egreso registrado.');
    }
///////////////////////////////////////////////////////////////////////////////
    public function cerrar(Request $request)
    {
        $request->validate([
            'monto_cierre' => 'required|numeric|min:0'
        ]);

        $caja = Caja::where('user_id', auth()->id())
                    ->where('estado', 'abierta')
                    ->first();

        if (!$caja) {
            return back()->with('error', 'No hay caja abierta.');
        }

        $esperado = $caja->monto_inicial + $caja->total_ventas - $caja->total_egresos;
        $diferencia = $request->monto_cierre - $esperado;

        $caja->update([
            'monto_cierre' => $request->monto_cierre,
            'diferencia' => $diferencia,
            'fecha_cierre' => now(),
            'estado' => 'cerrada'
        ]);

        return redirect()->route('caja.show', $caja->id);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function show(Caja $caja)
    {
        $cobros = Cobro::whereHas('venta', function ($q) use ($caja) {
            $q->where('caja_id', $caja->id)
            ->where('estado', '!=', 'anulada'); // 🔥 CLAVE
        })
        ->with('venta.cliente')
        ->get();

        $egresos = $caja->egresos()->get();
         // Totales
        $totalVentas = $cobros->sum('monto_aplicado');
        $totalEgresos = $egresos->sum('monto');
        $totalCompras = $egresos->filter(function ($egreso) {
            return str_contains($egreso->descripcion, 'Compra');
        })->sum('monto');
        $totalEsperado = $caja->monto_inicial + $totalVentas - $totalEgresos;
        $diferencia = $caja->monto_cierre - $totalEsperado;

        return view('caja.informe', compact(
            'caja',
            'cobros',
            'egresos',
            'totalVentas',
            'totalEgresos',
            'totalCompras',
            'totalEsperado',
            'diferencia'
        ));

    }


}
