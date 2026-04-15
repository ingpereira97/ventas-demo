<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Caja;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\EgresoCaja;
use App\Models\Proveedor;
use App\Models\User;





class CompraController extends Controller
{

    public function index()
    {
        $compras = Compra::with(['proveedor', 'user'])
                    ->orderBy('id', 'desc')
                    ->get();

        return view('compras.index', compact('compras'));
    }


    public function create()
    {
        $proveedores = Proveedor::all();
        $productos = Producto::all();

        return view('compras.create', compact('proveedores', 'productos'));
    }

    public function store(Request $request)
    {


        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'metodo_pago' => 'required|in:efectivo,transferencia,credito',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:0.01',
            'productos.*.precio' => 'required|numeric|min:0',
        ]);


        DB::beginTransaction();

        try {


            $caja = null;

                if ($request->metodo_pago === 'efectivo') {

                    $caja = Caja::where('user_id', auth()->id())
                                ->whereNull('fecha_cierre')
                                ->first();

                    if (!$caja) {
                        return back()->with('error', 'Debe abrir caja para registrar compras en efectivo.');
                    }
                }



            $compra = Compra::create([
                'proveedor_id' => $request->proveedor_id,
                'user_id' => auth()->id(),
                'total' => 0,
                'metodo_pago' => $request->metodo_pago
            ]);

            $total = 0;

            foreach ($request->productos as $item) {

                $producto = Producto::find($item['producto_id']);

                if (!$producto) {
                    throw new \Exception("Producto no encontrado.");
                }

                 $cantidad = $item['cantidad'];

                // 🚨 VALIDAR SEGÚN TIPO
                if ($producto->tipo === 'unidad') {
                    if (!is_numeric($cantidad) || floor($cantidad) != $cantidad) {
                        throw new \Exception("La cantidad de {$producto->nombre} debe ser un número entero.");
                    }
                }

                if ($cantidad <= 0) {
                    throw new \Exception("Cantidad inválida en {$producto->nombre}");
                }

                $subtotal = $item['cantidad'] * $item['precio'];

                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_compra' => $item['precio'],
                    'subtotal' => $subtotal,
                ]);

                // 🔼 Aumentar stock
                $producto->increment('stock', $item['cantidad']);

                 // 🔥 Actualizar último precio de compra
                $producto->update(['precio_compra' => $item['precio']]);

                $total += $subtotal;
            }

            $compra->update(['total' => $total]);


            if ($request->metodo_pago === 'efectivo') {

                EgresoCaja::create([
                    'caja_id' => $caja->id,
                    'user_id' => auth()->id(),
                    'monto' => $total,
                    'descripcion' => 'Compra de productos - Compra #' . $compra->id
                ]);
            }

            DB::commit();

            return redirect()->route('compras.index')
                ->with('success', 'Compra registrada correctamente.');

        } 
            catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error al registrar la compra: ' . $e->getMessage());
            }


    }

    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'user', 'detalles.producto'])
                        ->find($id);

        if (!$compra) {
            return redirect()->route('compras.index')
                            ->with('error', 'Compra no encontrada.');
        }

        return view('compras.show', compact('compra'));
    }


}
