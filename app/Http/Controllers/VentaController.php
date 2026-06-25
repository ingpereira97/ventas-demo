<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Caja;
use App\Models\Cobro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class VentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Mostrar formulario de creación de venta
    public function create()
    {
        $productos = Producto::all();
        $clientes = Cliente::all();
        return view('ventas.create', compact('productos', 'clientes'));
    }

////////////////////////////////////////////////////////////////////////////////////////////
    // Almacenar venta y detalle de productos
public function store(Request $request)
{
    $request->validate([
        'cliente_id' => 'required',
        'productos' => 'required|array',
        'cantidades' => 'required|array',
        'total' => 'required|numeric|min:0',
    ]);


    // 🔒 Verificar caja abierta ANTES de vender
    $caja = Caja::where('user_id', auth()->id())
                ->where('estado', 'abierta')
                ->first();

    if (!$caja) {
        return back()->with('error', 'Debe abrir caja antes de vender.');
    }

    DB::beginTransaction();

    try {

        $venta = new Venta();

        $venta->cliente_id = $request->cliente_id != 0 
            ? $request->cliente_id 
            : null;

        $venta->user_id = auth()->id();
        $venta->caja_id = $caja->id;
        $venta->total = 0; // 🔥 lo recalculamos nosotros

        // Generar nro comprobante
        $ultimo = Venta::latest('id')->first();
        $numero = $ultimo ? $ultimo->id + 1 : 1;
        $venta->nro_comprobante = 'V-' . str_pad($numero, 6, '0', STR_PAD_LEFT);

        $venta->save();

        $total = 0; // 🔥 AQUÍ estaba el error

        foreach ($request->productos as $index => $producto_id) {

            $producto = Producto::find($producto_id);

            if (!$producto) {
                throw new \Exception("Producto no encontrado.");
            }

            $cantidad = $request->cantidades[$index];

            // 🚨 VALIDACIÓN DE STOCK
            if ($producto->stock <= 0) {
                throw new \Exception("El producto {$producto->nombre} no tiene stock disponible.");
            }

            if ($cantidad > $producto->stock) {
                throw new \Exception("Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock}");
            }

            $subtotal = $producto->precio * $cantidad;

            $venta->productos()->attach($producto->id, [
                'cantidad' => $cantidad,
                'precio_unitario' => $producto->precio,
                'subtotal' => $subtotal,
            ]);

            // 🔻 Descontar stock
            $producto->decrement('stock', $cantidad);

            $total += $subtotal;
        }

       // 🔥 TODAS LAS VENTAS INICIAN COMO PENDIENTE
        $estado = 'pendiente';
        $saldo = $total;
        // 🔥 Actualizamos el total real calculado
        $venta->update([
            'total' => $total,
            'estado' => $estado,
            'saldo' => $saldo,
            'tipo_pago' => $request->tipo_pago // 🔥 importante
        ]);
        

        DB::commit();

        if ($request->tipo_pago === 'contado') {
            return redirect()->route('cobros.create', $venta->id);
        }

            return redirect()->route('ventas.index')
                ->with('success', 'Venta registrada exitosamente.');
                
    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error', $e->getMessage());
    }
}

////////////////////////////////////////////////////////////////////////////////////////////
    // Mostrar listado de ventas (opcional)
    public function index()
    {
        $ventas = Venta::with('cliente', 'productos', 'cobros')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('ventas.index', compact('ventas'));
    }

    ////////////////////////////////////////////////////////////////////////////////////////////

    public function show($id)
    {
                // Buscar la venta con el ID proporcionado y cargar los productos relacionados
                $venta = Venta::with('cliente', 'productos', 'cobros', 'usuarioAnulo')->findOrFail($id);
                
                // Retornar la vista con la venta cargada
                return view('ventas.show', compact('venta'));
    }

    ////////////////////////////////////////////////////////////////////////////////////////////

    public function anular(Request $request, Venta $venta)
    {
        if ($venta->estado === 'anulada') {
            return back()->with('error', 'La venta ya está anulada.');
        }

        $request->validate([
            'motivo' => 'required|string|max:500'
        ]);

        DB::beginTransaction();

        try {

            // 🔁 DEVOLVER STOCK
            foreach ($venta->productos as $producto) {
                $cantidad = $producto->pivot->cantidad;
                $producto->increment('stock', $cantidad);
            }

            // 💰 TOTAL PAGADO
            $totalPagado = $venta->cobros->sum('monto_aplicado');

            // 🏦 CAJA
            $caja = Caja::find($venta->caja_id);

            if ($caja && $totalPagado > 0) {
                $caja->decrement('total_ventas', $totalPagado);
            }

            // 🧾 ANULACIÓN
            $venta->update([
                'estado' => 'anulada',
                'saldo' => 0,
                'motivo_anulacion' => $request->motivo,
                'user_anulo_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
            ->route('ventas.show', $venta->id)
            ->with('success', 'Venta anulada correctamente.');
            
        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function buscarProductos(Request $request)
    {
        $buscar = $request->q;

        // 🔥 PRIMERO: buscar coincidencia EXACTA en código de barras
        $productoExacto = Producto::where('codigo_barras', $buscar)->first();

        if ($productoExacto) {
            return response()->json([$productoExacto]);
        }

        // 🔍 SI NO ES CÓDIGO → buscar por nombre
        $productos = Producto::where('nombre', 'like', "%$buscar%")
            ->limit(10)
            ->get();

        return response()->json($productos);
    }
}
