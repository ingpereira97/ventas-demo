@extends('layouts.app')

@section('content')

<h2>Informe de Productos</h2>

<table class="table table-bordered">

    <thead>
        <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Stock</th>
            <th>Precio</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>

        @forelse($productos as $producto)

        <tr>
            <td>{{ $producto->id }}</td>
            <td>{{ $producto->nombre }}</td>
            <td>{{ $producto->stock }}</td>
            <td>Gs {{ number_format($producto->precio) }}</td>
            <td>Gs {{ number_format($producto->stock * $producto->precio) }}</td>
        </tr>

        @empty

        <tr>
            <td colspan="5" class="text-center">
                No hay productos registrados
            </td>
        </tr>

        @endforelse

    </tbody>

</table>

<h3 class="text-right">Total en Inventario: 
    Gs {{ number_format($productos->sum(fn($p) => $p->stock * $p->precio)) }}
</h3>

<button onclick="window.print()" class="btn btn-success">
    Imprimir
</button>

@endsection