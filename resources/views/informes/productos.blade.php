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
           <td>

                @if($producto->tipo == 'peso')
                    <span class="badge bg-info">
                        @if($producto->stock < 1)

                            {{ number_format($producto->stock * 1000, 0, ',', '.') }} g

                        @else

                            {{ rtrim(rtrim(number_format($producto->stock, 3, '.', ''), '0'), '.') }} Kg

                        @endif
                    </span>
                @else
                    <span class="badge bg-secondary">
                    {{ number_format($producto->stock, 0, ',', '.') }} unidades
                    </span>

                @endif

            </td>         
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