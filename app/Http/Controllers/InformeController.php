<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Caja;
use App\Models\Cobro;
use App\Models\EgresoCaja;
use App\Models\Cliente;





class InformeController extends Controller
{
    public function index()
    {
        return view('informes.index');
    }

    public function compras(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

        $compras = Compra::when($desde, function ($query) use ($desde) {
                $query->whereDate('created_at', '>=', $desde);
            })
            ->when($hasta, function ($query) use ($hasta) {
                $query->whereDate('created_at', '<=', $hasta);
            })
            ->with('proveedor')
            ->get();

        $total = $compras->sum('total');

        return view('informes.compras', compact('compras','desde','hasta','total'));
    }

    public function ventas(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

        $ventas = Venta::when($desde, function ($query) use ($desde) {
                $query->whereDate('created_at', '>=', $desde);
            })
            ->when($hasta, function ($query) use ($hasta) {
                $query->whereDate('created_at', '<=', $hasta);
            })
            ->with('cliente', 'productos', 'cobros') // relaciones de venta
            ->get();

         // 🔥 TOTALES POR ESTADO
        $totalPagadas = $ventas->where('estado', 'pagado')->sum('total');
        $totalPendientes = $ventas->where('estado', 'pendiente')->sum('total');
        $totalAnuladas = $ventas->where('estado', 'anulada')->sum('total');

        // 🔥 TOTAL GENERAL
        $total = $ventas->sum('total');

        return view('informes.ventas', compact(
            'ventas',
            'desde',
            'hasta',
            'total',
            'totalPagadas',
            'totalPendientes',
            'totalAnuladas'
        ));
    }
    
 // Informe de Productos
    public function productos()
    {
        $productos = Producto::all(); // Trae todos los productos
        return view('informes.productos', compact('productos'));
    }

    // Informe de Caja
    public function caja(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

        // COBROS (dinero real)
        $cobros = Cobro::whereHas('venta', function ($q) {
            $q->where('estado', '!=', 'anulada'); // 🔥 CLAVE
        })
        ->when($desde, function ($query) use ($desde) {
            $query->whereDate('created_at', '>=', $desde);
        })
        ->when($hasta, function ($query) use ($hasta) {
            $query->whereDate('created_at', '<=', $hasta);
        })
        ->with('venta.cliente')
        ->get();

    
        // EGRESOS
        $egresos = EgresoCaja::when($desde, function ($query) use ($desde) {
                $query->whereDate('created_at', '>=', $desde);
            })
            ->when($hasta, function ($query) use ($hasta) {
                $query->whereDate('created_at', '<=', $hasta);
            })
            ->get();

        // COMPRAS
        $compras = Compra::when($desde, function ($query) use ($desde) {
                $query->whereDate('created_at', '>=', $desde);
            })
            ->when($hasta, function ($query) use ($hasta) {
                $query->whereDate('created_at', '<=', $hasta);
            })
            ->get();

        // TOTALES
        $totalVentas = $cobros->sum('monto_aplicado');       
        $totalEgresos = $egresos->sum('monto');
        $totalCompras = $compras->sum('total');

        $balance = $totalVentas - $totalEgresos;

        return view('informes.caja', compact(
            'cobros',
            'egresos',
            'compras',
            'desde',
            'hasta',
            'totalVentas',
            'totalEgresos',
            'totalCompras',
            'balance'
        ));
    }

    public function clientes(Request $request)
    {
        $buscar = $request->buscar;

        $clientes = Cliente::with('ventas')
            ->when($buscar, function($q) use ($buscar) {
                $q->where('nombre', 'like', "%$buscar%");
            })
            ->get();

        foreach ($clientes as $cliente) {
            $cliente->deuda = $cliente->ventas
                ->where('estado', 'pendiente')
                ->sum('saldo');
        }

        return view('informes.clientes', compact('clientes', 'buscar'));
    }

}
