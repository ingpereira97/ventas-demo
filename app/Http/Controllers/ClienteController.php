<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        Cliente::create($request->all());

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        $cliente->update($request->all());

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        $ventasPendientes = $cliente->ventas()
            ->where('saldo', '>', 0)
            ->whereDoesntHave('cobros')
            ->get();

        $ventasParciales = $cliente->ventas()
            ->where('estado', 'pendiente')
            ->where('estado', '!=', 'anulada')
            ->whereHas('cobros')
            ->get();

        $ventasPagadas = $cliente->ventas()
            ->where('estado', 'pagado')
            ->where('estado', '!=', 'anulada')
            ->get();

        return view('clientes.show', compact(
            'cliente',
            'ventasPendientes',
            'ventasParciales',
            'ventasPagadas'
        ));
    }
}
